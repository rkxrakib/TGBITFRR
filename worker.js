export default {
  async fetch(request, env, ctx) {
    // CORS হেডার (যাতে আপনার সাইট থেকে রিকোয়েস্ট ব্লক না হয়)
    const corsHeaders = {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "GET, POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type",
    };

    // Pre-flight request handle
    if (request.method === "OPTIONS") {
      return new Response(null, { headers: corsHeaders });
    }

    const url = new URL(request.url);
    const params = url.searchParams;
    const action = params.get("action");

    // পরিবেশ ভেরিয়েবল (Environment Variables) থেকে ডাটাবেস তথ্য নেওয়া
    const DB_URL = env.FIREBASE_DB_URL; // e.g., https://your-project.firebaseio.com
    const DB_SECRET = env.FIREBASE_DB_SECRET; // Database Secret (Legacy)

    if (!DB_URL || !DB_SECRET) {
      return new Response(JSON.stringify({ error: "Server Config Missing" }), { headers: corsHeaders });
    }

    // ফায়ারবেজ REST API হেল্পার
    async function dbFetch(path, method = "GET", body = null) {
      const fetchUrl = `${DB_URL}/${path}.json?auth=${DB_SECRET}`;
      const options = { method: method };
      if (body) options.body = JSON.stringify(body);
      
      const response = await fetch(fetchUrl, options);
      return await response.json();
    }

    try {
      let result = {};

      // --- API ROUTES ---

      // ১. কনফিগারেশন লোড করা
      if (action === "getConfig") {
        result = await dbFetch("config");
      }

      // ২. ইউজার লগইন / রেজিস্টার এবং রেফারেল হ্যান্ডলিং
      else if (action === "login") {
        const reqData = await request.json(); // { id, firstName, photoUrl, refId }
        const uid = reqData.id;
        
        // চেক করি ইউজার আছে কিনা
        let user = await dbFetch(`users/${uid}`);

        if (!user) {
          // নতুন ইউজার তৈরি
          const config = await dbFetch("config");
          const bonus = config?.referralBonus || 0;

          // নতুন ইউজার অবজেক্ট
          user = {
            id: uid,
            firstName: reqData.firstName,
            photoUrl: reqData.photoUrl || "",
            referrals: 0,
            balance: 0,
            totalEarned: 0
          };

          // রেফারেল লজিক (যদি রেফারার থাকে)
          if (reqData.refId && reqData.refId != uid) {
            const referrer = await dbFetch(`users/${reqData.refId}`);
            if (referrer) {
              // রেফারারকে বোনাস দেওয়া
              const newRefBalance = (referrer.balance || 0) + bonus;
              const newRefCount = (referrer.referrals || 0) + 1;
              
              // রেফারার আপডেট করা
              await dbFetch(`users/${reqData.refId}`, "PATCH", { 
                balance: newRefBalance,
                referrals: newRefCount
              });
            }
          }

          // নতুন ইউজার ডাটাবেসে সেভ করা
          await dbFetch(`users/${uid}`, "PUT", user);
        } else {
          // পুরনো ইউজার: ছবি আপডেট এবং ব্যালেন্স চেক
          if (reqData.photoUrl && user.photoUrl !== reqData.photoUrl) {
             await dbFetch(`users/${uid}/photoUrl`, "PUT", reqData.photoUrl);
             user.photoUrl = reqData.photoUrl;
          }
          // ব্যালেন্স বাফার থাকলে রিসেট করা (আপনার আগের কোডের লজিক অনুযায়ী)
          // তবে REST API তে আমরা সরাসরি ব্যালেন্স রিটার্ন করছি, বাফার দরকার নেই।
        }
        result = user;
      }

      // ৩. ইউজার ডাটা আপডেট (টাস্ক বা অ্যাড দেখার পর)
      else if (action === "updateBalance") {
        const reqData = await request.json(); // { id, amount }
        const uid = reqData.id;
        const amount = parseFloat(reqData.amount);

        const user = await dbFetch(`users/${uid}`);
        if(user) {
          const newBal = (user.balance || 0) + amount;
          const newEarned = (user.totalEarned || 0) + amount;
          
          await dbFetch(`users/${uid}`, "PATCH", {
            balance: newBal,
            totalEarned: newEarned
          });
          result = { success: true, newBalance: newBal };
        }
      }

      // ৪. লিডারবোর্ড (Top 20)
      else if (action === "getLeaderboard") {
        const usersData = await dbFetch("users");
        if (usersData) {
          let users = Object.values(usersData);
          // রেফারাল অনুযায়ী সর্ট করা
          users.sort((a, b) => (b.referrals || 0) - (a.referrals || 0));
          result = users.slice(0, 20);
        } else {
          result = [];
        }
      }

      // ৫. হিস্টোরি (Withdrawals)
      else if (action === "getHistory") {
        const uid = params.get("id");
        // ফায়ারবেজ REST API তে কুয়েরি করা একটু জটিল, তাই আমরা সিম্পল লুপ চালাবো অথবা ক্লায়েন্ট ফিল্টার করবে।
        // ভালো পারফর্মেন্সের জন্য আমরা সব উইথড্র এনে ফিল্টার করবো (ছোট অ্যাপের জন্য ঠিক আছে)
        const pending = await dbFetch("withdrawals/pending");
        const completed = await dbFetch("withdrawals/completed");
        const rejected = await dbFetch("withdrawals/rejected");

        let history = [];
        const process = (obj, status) => {
          if(!obj) return;
          Object.values(obj).forEach(item => {
            if(String(item.userId) === String(uid)) {
              history.push({...item, status: status});
            }
          });
        };

        process(pending, 'pending');
        process(completed, 'completed');
        process(rejected, 'rejected');

        history.sort((a, b) => b.timestamp - a.timestamp);
        result = history.slice(0, 10);
      }

      // ৬. উইথড্র রিকোয়েস্ট
      else if (action === "withdraw") {
        const reqData = await request.json(); // { userId, userName, amount, method, account }
        const uid = reqData.userId;
        const amount = reqData.amount;

        // ইউজারের ব্যালেন্স চেক
        const user = await dbFetch(`users/${uid}`);
        
        if (user && user.balance >= amount) {
          // ১. পেন্ডিং লিস্টে যোগ করা
          await dbFetch("withdrawals/pending", "POST", {
            ...reqData,
            timestamp: Date.now()
          });

          // ২. ইউজারের ব্যালেন্স কেটে নেওয়া
          const newBal = user.balance - amount;
          await dbFetch(`users/${uid}/balance`, "PUT", newBal);
          
          result = { success: true };
        } else {
           result = { success: false, message: "Insufficient balance" };
        }
      }

      return new Response(JSON.stringify(result), {
        headers: { "Content-Type": "application/json", ...corsHeaders },
      });

    } catch (e) {
      return new Response(JSON.stringify({ error: e.message }), { headers: corsHeaders });
    }
  },
};
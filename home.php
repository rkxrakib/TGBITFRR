<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
</head>
<body>
    <div class="header">
        <div style="display:flex; align-items:center; gap:10px;">
            <img id="u-img" src="" class="avatar">
            <div><h3 id="u-name">Guest</h3><span style="color:var(--acc); font-size:0.7rem;">LV.1 Newbie</span></div>
        </div>
        <i class="fas fa-globe" style="color:var(--acc-sec)"></i>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><h2><i class="fab fa-ethereum"></i> <span id="u-usd">0.0</span></h2><p>USDT</p></div>
        <div class="stat-card"><h2><i class="fas fa-coins"></i> <span id="u-coin">0</span></h2><p>FG Coin</p></div>
        <div class="stat-card"><h2><i class="fas fa-check"></i> <span id="u-done">0</span></h2><p>Done</p></div>
    </div>

    <div style="padding: 20px;">
        <h4 style="color:var(--acc-sec); margin-bottom:15px;">HOT TASKS</h4>
        <div id="task-area"></div>
    </div>

    <nav class="nav">
        <a href="home.php" class="nav-item active"><i class="fas fa-home"></i>Home</a>
        <a href="tasks.php" class="nav-item"><i class="fas fa-bullseye"></i>Tasks</a>
        <a href="rank.php" class="nav-item"><i class="fas fa-trophy"></i>Rank</a>
        <a href="activity.php" class="nav-item"><i class="fas fa-history"></i>Activity</a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i>Profile</a>
    </nav>

    <script>
        firebase.initializeApp(<?php echo $firebaseConfig; ?>);
        const db = firebase.database();
        const tg = window.Telegram.WebApp;
        tg.ready();

        const user = tg.initDataUnsafe.user || {id: "123", first_name: "User"};
        db.ref('users/' + user.id).on('value', snap => {
            const data = snap.val() || {};
            document.getElementById('u-usd').innerText = (data.balance || 0).toFixed(2);
            document.getElementById('u-coin').innerText = (data.coins || 0).toFixed(0);
            document.getElementById('u-done').innerText = data.completed || 0;
            document.getElementById('u-name').innerText = user.first_name;
            document.getElementById('u-img').src = user.photo_url || 'https://via.placeholder.com/60';
        });

        db.ref('config/webTasks').on('value', snap => {
            const area = document.getElementById('task-area'); area.innerHTML = '';
            snap.forEach(c => {
                const t = c.val();
                area.innerHTML += `<div style="background:var(--surf); padding:15px; border-radius:15px; display:flex; justify-content:space-between; margin-bottom:10px;">
                <div style="display:flex; gap:10px;"><img src="${t.icon}" width="40" height="40"><div><b>${t.name}</b><br><small style="color:var(--acc)">+${t.reward} USDT</small></div></div>
                <button onclick="window.open('${t.url}')" style="background:var(--acc-sec); border:none; color:#fff; padding:5px 15px; border-radius:8px;">START</button></div>`;
            });
        });
    </script>
</body>
</html>

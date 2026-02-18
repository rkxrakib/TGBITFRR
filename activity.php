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
    <div style="padding: 25px 20px;">
        <h4 style="color:var(--acc-sec); letter-spacing:1px; margin-bottom:20px;">ACTIVITY LOG</h4>
        <div id="hist-area">
            <p style="text-align:center; color:var(--dim);">Loading history...</p>
        </div>
    </div>

    <nav class="nav">
        <a href="home.php" class="nav-item"><i class="fas fa-home"></i>Home</a>
        <a href="home.php" class="nav-item"><i class="fas fa-bullseye"></i>Tasks</a>
        <a href="rank.php" class="nav-item"><i class="fas fa-trophy"></i>Rank</a>
        <a href="activity.php" class="nav-item active"><i class="fas fa-history"></i>Activity</a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i>Profile</a>
    </nav>

    <script>
        firebase.initializeApp(<?php echo $firebaseConfig; ?>);
        const tg = window.Telegram.WebApp; tg.ready();
        const uid = (tg.initDataUnsafe.user || {id:"000"}).id;

        const histArea = document.getElementById('hist-area');
        
        firebase.database().ref('withdrawals/pending').on('value', s => {
            histArea.innerHTML = '';
            s.forEach(c => {
                const v = c.val();
                if(v.userId == uid) {
                    histArea.innerHTML += `<div class="item-card">
                        <div><b>${v.method} Cashout</b><br><small style="color:var(--dim)">${new Date(v.timestamp).toLocaleDateString()}</small></div>
                        <div style="text-align:right"><b style="color:var(--acc)">${v.amount} USD</b><br><span style="font-size:0.6rem; color:#ffb400">PENDING</span></div>
                    </div>`;
                }
            });
            if(histArea.innerHTML == '') histArea.innerHTML = '<p style="text-align:center; color:var(--dim);">No recent activity</p>';
        });
    </script>
</body>
</html>

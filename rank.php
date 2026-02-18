<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div style="padding: 20px; text-align: center;">
        <h3 style="margin-bottom:20px; color:var(--acc)">LEADERBOARD</h3>
        <div id="fake-rank"></div>
    </div>

    <nav class="nav">
        <a href="home.php" class="nav-item"><i class="fas fa-home"></i>Home</a>
        <a href="tasks.php" class="nav-item"><i class="fas fa-bullseye"></i>Tasks</a>
        <a href="rank.php" class="nav-item active"><i class="fas fa-trophy"></i>Rank</a>
        <a href="activity.php" class="nav-item"><i class="fas fa-history"></i>Activity</a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i>Profile</a>
    </nav>

    <script>
        const fakes = [
            {n: "ay***i", v: "200.13", i: "https://i.pravatar.cc/100?u=1"},
            {n: "Be***e", v: "68.56", i: "https://i.pravatar.cc/100?u=2"},
            {n: "In***1", v: "64.13", i: "https://i.pravatar.cc/100?u=3"},
            {n: "Sh***e", v: "62.50", i: "https://i.pravatar.cc/100?u=4"},
            {n: "Ma***x", v: "55.20", i: "https://i.pravatar.cc/100?u=5"},
            {n: "No***h", v: "48.10", i: "https://i.pravatar.cc/100?u=6"},
            {n: "Ki***g", v: "42.30", i: "https://i.pravatar.cc/100?u=7"},
            {n: "Lu***s", v: "38.90", i: "https://i.pravatar.cc/100?u=8"},
            {n: "Ro***y", v: "35.40", i: "https://i.pravatar.cc/100?u=9"},
            {n: "Za***n", v: "31.20", i: "https://i.pravatar.cc/100?u=10"}
        ];

        let html = '';
        fakes.forEach((f, idx) => {
            html += `<div style="display:flex; align-items:center; justify-content:space-between; background:var(--surf); padding:12px; border-radius:15px; margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:10px;"><span>${idx+1}</span><img src="${f.i}" width="35" style="border-radius:50%;"><b style="font-size:0.9rem;">${f.n}</b></div>
            <b style="color:var(--acc);">${f.v} USDT</b></div>`;
        });
        document.getElementById('fake-rank').innerHTML = html;
    </script>
</body>
</html>

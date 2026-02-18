<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div style="padding: 20px;">
        <div style="background:var(--surf); padding:20px; border-radius:20px; text-align:center;">
            <h4 style="margin-bottom:20px;">CASH OUT</h4>
            <input id="w-acc" placeholder="Address/Number" style="width:100%; padding:12px; background:#0d0d12; border:none; border-radius:10px; color:#fff; margin-bottom:10px;">
            <input id="w-amt" type="number" placeholder="Amount" style="width:100%; padding:12px; background:#0d0d12; border:none; border-radius:10px; color:#fff; margin-bottom:15px;">
            <button onclick="alert('Sent!')" style="width:100%; padding:15px; background:var(--acc-sec); color:#fff; border:none; border-radius:12px; font-weight:800;">WITHDRAW</button>
        </div>
    </div>

    <nav class="nav">
        <a href="home.php" class="nav-item"><i class="fas fa-home"></i>Home</a>
        <a href="tasks.php" class="nav-item"><i class="fas fa-bullseye"></i>Tasks</a>
        <a href="rank.php" class="nav-item"><i class="fas fa-trophy"></i>Rank</a>
        <a href="activity.php" class="nav-item"><i class="fas fa-history"></i>Activity</a>
        <a href="profile.php" class="nav-item active"><i class="fas fa-user"></i>Profile</a>
    </nav>
</body>
</html>

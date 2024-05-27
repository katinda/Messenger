<?php
require_once "functions.php";
check_login();

// Récupérer le pseudo de l'utilisateur connecté
$user = $_SESSION['user'];

// Récupérer les noms des followee et des followers
$followee_names = get_followee_names($user);
$follower_names = get_follower_names($user);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Vos amis</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="styles.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color: cornflowerblue; color: yellow;">

<div class="main">
    <?php include('menu.html'); ?>
    <div class="main" style="text-align: center">
        <h1 style="margin-top: 50px;" class="title">Vos followers et followees</h1>
        <h2>Vos followees:</h2>
        <ul style="list-style-type: none;">
            <?php foreach ($followee_names as $followee_name): ?>
                <li><a style="text-decoration: none;" href="profile.php?pseudo=<?php echo urlencode($followee_name); ?>"><?php echo $followee_name; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <h2>Vos followers:</h2>
        <ul style="list-style-type: none;">
            <?php foreach ($follower_names as $follower_name): ?>
                <li><a style="text-decoration: none;" href="profile.php?pseudo=<?php echo urlencode($follower_name); ?>"><?php echo $follower_name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
</body>
</html>


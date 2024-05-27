<?php
require_once "functions.php";
check_login();

if(isset($_GET["pseudo"])){
    $pseudo = sanitize($_GET["pseudo"]);
}
else {
    $pseudo = $user;
}

$profile = get_member($pseudo);

if(!$profile){
    abort("Can't find user '$pseudo' in the database.");
}
else {
    $description = $profile["profile"];
    $picture_path = $profile["picture_path"];
}

// Fonction pour vérifier si l'utilisateur actuel suit l'utilisateur dont on visualise le profil
$is_following = $user !== $pseudo ? is_following($user, $pseudo) : false;
// Obtenir le nombre de followee et de follower
$followee_count = count_followee($pseudo);
$follower_count = count_follower($pseudo);

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pseudo; ?>'s Profile!</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body style="background-color: cornflowerblue; color: yellow;">
<?php include('menu.html'); ?>
<div class="title"><?php echo $pseudo; ?>'s Profile!</div>
<div style="text-align: center;" class="main">
    <?php
    if(strlen($description) == 0){
        echo 'No profile string entered yet!';
    } else {
        echo $description;
    }
    ?>
    <br><br>
    <?php
    if(strlen($picture_path) == 0){
        echo 'No picture loaded yet!';
    } else {
        echo "<image style='display: block; margin-left: auto; margin-right: auto '
            src='$picture_path' width='200' alt='$pseudo&apos;s photo!'>";
    }
    ?>
    <?php if($pseudo !== $user): ?>
        <div id="followBtnContainer">
            <button id="followBtn" onclick="toggleFollow('<?php echo $pseudo; ?>')"><?php echo $is_following ? 'Unfollow' : 'Follow'; ?></button>
        </div>
    <?php endif; ?>
    <?php
    echo "<div style='margin-top: 100px; margin-bottom: 50px; text-align: center' class='fol'>";
    echo "<span>Followee: $followee_count</span>";
    echo "<span>Follower: $follower_count</span>";
    echo "</div>";
    ?>






    <script>
        function toggleFollow(followee) {
            var btn = $('#followBtn');
            var action = btn.text().trim() === 'Follow' ? 'follow' : 'unfollow';

            console.log(action, followee);

            $.ajax({
                type: 'POST',
                url: 'follow.php',
                data: { action: action, followee: followee }
            })
                .done(function(response) {
                    if (response === 'followed') {
                        btn.text('Unfollow');
                    } else if (response === 'unfollowed') {
                        btn.text('Follow');
                    } else {
                        console.log('Error: ' + response);
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Error:', error);
                    console.log('Status:', status);
                    console.log('XHR:', xhr);
                });
        }

    </script>


</div>
</body>
</html>


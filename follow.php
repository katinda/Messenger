<?php
require_once "functions.php";
check_login();

if($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST["action"]) && isset($_POST["followee"])) {
        $action = $_POST["action"];
        $followee = sanitize($_POST["followee"]);

        if($action === 'follow') {
            if(add_follower($user, $followee)) {
                echo 'followed';
            } else {
                echo 'Error following user: could not add follower.';
            }
        } elseif($action === 'unfollow') {
            if(delete_follower($user, $followee)) {
                echo 'unfollowed';
            } else {
                echo 'Error unfollowing user: could not delete follower.';
            }
        } else {
            echo 'Invalid action.';
        }
    } else {
        echo 'Missing parameters.';
    }
} else {
    echo 'Invalid request method.';
}
?>

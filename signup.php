<?php
require_once("functions.php");
$pdo = connect();
$pseudo = '';
$password = '';
$password_confirm = '';

if(isset($_POST['pseudo']) && isset($_POST['password']) && isset($_POST['password_confirm'])){
    $pseudo = sanitize($_POST['pseudo']);
    $password = sanitize($_POST['password']);
    $password_confirm = sanitize($_POST['password_confirm']);

    if (!is_pseudo_available($pseudo))
        $errors[] = "Le nom d'utilisateur existe déjà";
    if(trim($pseudo) == '')
        $errors[] = "Le pseudo est obligatoire";
    if(strlen(trim($pseudo)) < 3)
        $errors[] = "Le pseudo doit contenir 3 caractères au minimum";
    if($password != $password_confirm)
        $errors[] = "Les mots de passe doivent être identiques";

    if(!isset($errors)){
        add_member($pseudo,$password);
        log_user($pseudo);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="styles.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color: cornflowerblue; color: yellow;">
<div class="title">Sign Up</div>
<div class="menu">
    <a href="index.php">Home</a>
</div>
<div class="main">
    Please enter your details to sign up :
    <br><br>
    <form action="signup.php" method="post">
        <table>
            <tr>
                <td>Pseudo:</td>
                <td><input id="pseudo" name="pseudo" type="text" value="<?php echo $pseudo; ?>"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input id="password" name="password" type="password" value="<?php echo $password; ?>"></td>
            </tr>
            <tr>
                <td>Confirm Password:</td>
                <td><input id="password_confirm" name="password_confirm" type="password" value="<?php echo $password_confirm; ?>"></td>
            </tr>
        </table>
        <input type="submit" value="Sign Up">
    </form>
    <?php
    if(isset($errors)){
        echo "<div class='errors'>
                          <br><br><p>Veuillez corriger les erreurs suivantes :</p>
                          <ul>";
        foreach($errors as $error){
            echo "<li>".$error."</li>";
        }
        echo '</ul></div>';
    }
    ?>
</div>

</body>
</html>

<?php

session_start();


function connect(){
    $dbhost = "localhost";
    $dbname = "my_social_network_base";
    $dbuser = "root";
    $dbpassword = "root";

    try
    {
        $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", "$dbuser", "$dbpassword");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
    catch (Exception $exc)
    {
        abort("Erreur lors de l'accès à la base de données.");
    }
}

function sanitize($var)
{
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlspecialchars($var);
    return $var;
}

function redirect($url, $statusCode = 303)
{
    header('Location: ' . $url, true, $statusCode);
    die();
}

function check_login()
{
    global $user;
    if (!isset($_SESSION['user']))
        redirect('index.php');
    else
        $user = $_SESSION['user'];
}

function my_hash($password)
{
    $prefix_salt = "vJemLnU3";
    $suffix_salt = "QUaLtRs7";
    return md5($prefix_salt.$password.$suffix_salt);
}

function check_password($password, $hash)
{
    return $hash === my_hash($password);
}

function abort($err)
{
    global $error;
    $error = $err;
    include 'error.php';
    die;
}

function is_pseudo_available($pseudo) {
    $pdo = connect();
    try{
        $query = $pdo->prepare("SELECT * FROM Members WHERE pseudo=:pseudo");
        $query->execute(array("pseudo"=>$pseudo));
        $result = $query->fetchAll();
        return count($result) === 0;
    } catch (Exception $e){
        abort("Error while accessing database. Please contact your administrator.");
    }
}

function get_member($pseudo){
    $pdo = connect();
    try
    {
        $query = $pdo->prepare("SELECT * FROM Members where pseudo = :pseudo");
        $query->execute(array("pseudo" => $pseudo));
        $profile = $query->fetch(); // un seul résultat au maximum
    }
    catch (Exception $exc)
    {
        abort("Error while accessing database. Please contact your administrator.");
    }
    if($query->rowCount()==0){
        return false;
    }
    else{
        return $profile;
    }
}

function get_all_members(){
    $pdo = connect();
    try
    {
        $query = $pdo->prepare("SELECT pseudo FROM Members");
        $query->execute();
        $members = $query->fetchAll();
        return $members;
    }
    catch (Exception $exc)
    {
        abort("Erreur lors de l'accès à la base de données.");
    }
}

function get_filtered_members($filter){
    $pdo = connect();
    try
    {
        $query = $pdo->prepare("SELECT pseudo FROM Members where pseudo like :filter");
        $query->execute(array("filter" => "%$filter%"));
        $members = $query->fetchAll();
        return $members;
    }
    catch (Exception $exc)
    {
        abort("Erreur lors de l'accès à la base de données.");
    }
}


//pre : user does'nt exist yet
function add_member($pseudo, $password){
    $pdo = connect();
    try{
        $query = $pdo->prepare("INSERT INTO Members(pseudo,password)
                                        VALUES(:pseudo,:password)");
        $query->execute(array("pseudo"=>$pseudo, "password"=>my_hash($password)));
        return true;
    } catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}

function delete_follower($follower, $followee){
    $pdo = connect();
    try{
        $query = $pdo->prepare("DELETE FROM Follows WHERE follower = :follower AND followee = :followee");
        $query->execute(array("follower"=>$follower, "followee"=>$followee));
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}


function add_follower($follower, $followee){
    $pdo = connect();
    try{
        $query = $pdo->prepare( "INSERT INTO Follows (follower, followee) VALUES (:follower, :followee)");
        $query->execute(array("follower"=>$follower, "followee"=>$followee ));
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}

// Fonction pour vérifier si l'utilisateur actuel suit l'utilisateur dont on visualise le profil
function is_following($follower, $followee){
    $pdo = connect();
    $query = $pdo->prepare("SELECT * FROM Follows WHERE follower = :follower AND followee = :followee");
    $query->execute(array("follower"=>$follower, "followee"=>$followee));
    return $query->rowCount() > 0;
}


function count_followee($follower){
    try{
        $pdo = connect();
        $query = $pdo->prepare("SELECT COUNT(followee) FROM Follows WHERE follower = :follower");
        $query->execute(array("follower"=>$follower));
        $result = $query->fetchColumn();
        return $result !== false ? $result : 0; // Retourne le nombre de followee ou 0 s'il n'y en a pas
    }catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}

function count_follower( $followee){
    try{
        $pdo = connect();
        $query = $pdo->prepare("SELECT COUNT(follower) FROM Follows WHERE followee = :followee");
        $query->execute(array("followee"=>$followee));
        $result = $query->fetchColumn();
        return $result !== false ? $result : 0; // Retourne le nombre de follower ou 0 s'il n'y en a pas
    }catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}

function get_followee_names($followee)
{
    try {
        $pdo = connect();
        $query = $pdo->prepare("SELECT follower FROM Follows WHERE followee = :followee");
        $query->execute(array("followee" => $followee));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $names = array();
        foreach ($result as $row) {
            $names[] = $row['follower'];
        }

        return $names;
    } catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}

function get_follower_names($follower)
{
    try {
        $pdo = connect();
        $query = $pdo->prepare("SELECT followee FROM Follows WHERE follower = :follower");
        $query->execute(array("follower" => $follower));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $names = array();
        foreach ($result as $row) {
            $names[] = $row['followee'];
        }

        return $names;
    } catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}







function update_member($pseudo, $profile, $picture_path){
    $actual = get_member($pseudo);
    if($profile == NULL)
        $profile = $actual['profile'];
    if($picture_path == NULL)
        $picture_path = $actual['picture_path'];
    $pdo = connect();
    try{
        $query = $pdo->prepare("UPDATE Members SET picture_path=:path, profile=:profile WHERE pseudo=:pseudo ");
        $query->execute(array("path"=>$picture_path,"profile"=>$profile,"pseudo"=>$pseudo));
        return true;
    } catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }

}

function send_private_message($author, $recipient, $body) {
    $pdo = connect();
    try {
        $query = $pdo->prepare("INSERT INTO Messages (author, recipient, body, private, date_time) VALUES (:author, :recipient, :body, 1, NOW())");
        $query->execute(array("author" => $author, "recipient" => $recipient, "body" => $body));
        return true;
    } catch (Exception $ex) {
        abort("Error while accessing database. Please contact your administrator.");
        return false;
    }
}

function get_private_messages($recipient) {
    $pdo = connect();
    try {
        $query = $pdo->prepare("SELECT * FROM Messages WHERE recipient = :recipient AND private = 1 ORDER BY date_time DESC");
        $query->execute(array("recipient" => $recipient));
        $messages = $query->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    } catch (Exception $ex) {
        abort("Erreur lors de la récupération des messages. Veuillez contacter l'administrateur.");
        return false;
    }
}
function send_public_message($author, $body) {
    $pdo = connect();
    try {
        $query = $pdo->prepare("INSERT INTO Messages (author, body, private, date_time) VALUES (:author, :body, 0, NOW())");
        $query->execute(array("author" => $author, "body" => $body));
        return true;
    } catch (Exception $ex) {
        abort("Erreur lors de l'envoi de la publication. Veuillez contacter l'administrateur.");
        return false;
    }
}




function log_user($pseudo){
    $_SESSION["user"] = $pseudo;
    redirect("profile.php");
}





?>
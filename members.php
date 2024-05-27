<?php
require_once "functions.php";

check_login();

$filter = "";

if (isset($_GET["action"])) {
    $action = sanitize($_GET["action"]);
    if ($action == "Apply" && isset($_GET["filter"])) {
        $filter = sanitize($_GET["filter"]);
    }
}

if (strlen(trim($filter)) > 0)
    $members = get_filtered_members($filter);
else
    $members = get_all_members();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Members</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="styles.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color: cornflowerblue; color: yellow;">
<?php include('menu.html'); ?>
<div class="main">
    <div class="title" style="text-align: center; font-size: 50px; font-weight: bold; margin-top: 30px;">Other Members</div>
    <div style="margin: 0 auto;width: 50%; /* ou toute autre largeur souhaitée */text-align: center; margin-top: 70px;">
        <form  method="get">
            <label>Filter:</label>
            <input type="text" name="filter" value="<?php echo $filter ?>">
            <input type="submit" name="action" value="Apply">
            <input type="submit" name="action" value="Clear">
        </form>
    </div>
    <?php if (count($members) > 0) { ?>
        <ul style="list-style-type:none; text-align: center; margin-top: 70px;" >
            <?php
            foreach($members as $member){
                $name = $member['pseudo'];
                echo "<li ><a style='text-decoration: none; ' href=profile.php?pseudo=$name>$name</a></li>";
            }
            ?>
        </ul>
    <?php } else { ?>
        <p>No members found !</p>
    <?php } ?>
</div>
</body>
</html>



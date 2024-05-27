<?php
session_start();

require_once 'functions.php';

check_login();

$user = $_SESSION['user'];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient = sanitize($_POST['recipient']);
    $message_body = sanitize($_POST['message_body']);

    // Vérifier que les champs ne sont pas vides avant d'envoyer le message
    if (!empty($recipient) && !empty($message_body)) {
        // Envoyer le message
        if (send_private_message($user, $recipient, $message_body)) {
            echo "Message envoyé avec succès !";
            // Rediriger vers une autre page après l'envoi du message
            header("Location: messages.php");
            exit(); // Assure que le script s'arrête ici
        } else {
            echo "Une erreur s'est produite lors de l'envoi du message.";
        }
    } else {
        echo "Veuillez remplir tous les champs du formulaire.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoyer un message privé</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body style="background-color: cornflowerblue; color: yellow;">

<h1 style="text-align: center;">Envoyer un message privé</h1>

<div style="margin: 0 auto;width: 50%; /* ou toute autre largeur souhaitée */text-align: center; margin-top: 70px; margin-bottom: 30px;">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="recipient">Destinataire:</label>
        <select id="recipient" name="recipient" required>
            <?php
            // Récupérer tous les membres de la base de données
            $members = get_all_members();

            // Afficher chaque membre comme une option dans la liste déroulante
            foreach ($members as $member) {
                echo '<option value="' . htmlspecialchars($member['pseudo']) . '">' . htmlspecialchars($member['pseudo']) . '</option>';
            }
            ?>
        </select><br><br>

        <label for="message_body">Message:</label><br>
        <textarea id="message_body" name="message_body" rows="4" cols="50" required></textarea><br><br>

        <input type="submit" value="Envoyer">
    </form>
</div>



<hr>

<h2 style="text-align: center;">Mes messages privés reçus</h2>

<?php
// Récupérer les messages privés
$messages = get_private_messages($user);

// Afficher les messages dans un tableau
if ($messages !== false) {
    if (count($messages) > 0) {
        echo '<table>';
        echo '<tr><th style="color: cornflowerblue;">De</th><th style="color: cornflowerblue;">Date</th><th style="color: cornflowerblue;">Message</th></tr>';
        foreach ($messages as $message) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($message['author']) . '</td>';
            echo '<td>' . htmlspecialchars($message['date_time']) . '</td>';
            echo '<td>' . htmlspecialchars($message['body']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Aucun message à afficher.</p>';
    }
}
?>

<div style="text-align: center; margin-top: 40px; margin-bottom: 50px;">
    <a  href="profile.php">Retour au profil</a>
</div>


</body>
</html>

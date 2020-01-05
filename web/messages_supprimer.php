<?php
require_once 'includes/config.php';

if(!$user->is_logged_in()) {
        header('Location: login.php');
}

// on teste si l'id du message a bien été fourni en argument au script messages_envoyer.php
if (!isset($_GET['id_message']) || empty($_GET['id_message'])) {
        header('Location: /messagerie.php?membre='.html($_SESSION['username']));
        exit();
}
else {
        $stmt = $db->prepare('DELETE FROM blog_messages WHERE messages_id = :messages_id AND messages_id_destinataire = :messages_id_destinataire');
        $stmt->execute(array(
                ':messages_id' => html($_GET['id_message']),
                ':messages_id_destinataire' => html($_SESSION['userid'])
        ));

        header('Location: /messagerie.php?membre='.html($_SESSION['username']).'&action=messupprime');
        exit();
}
?>

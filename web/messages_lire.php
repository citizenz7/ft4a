<?php
include_once 'includes/config.php';

$pagetitle = 'Messagerie';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<div class="first">
	<!-- ### -->

	<?php
        // on teste si notre paramètre existe bien et qu'il n'est pas vide
        if (!isset($_GET['id_message']) || empty($_GET['id_message'])) {
                $error[] = 'Aucun message reconnu.';
        }
        else {
                // on prépare une requete SQL selectionnant la date, le titre et l'expediteur du message que l'on souhaite lire, tout en prenant soin de vérifier que le message appartient bien au membre connecté
                $stmtmess = $db->prepare('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_messages.messages_message, blog_members.memberID as memberid, blog_members.username as expediteur FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = :userid AND blog_messages.messages_id_expediteur = blog_members.memberID AND blog_messages.messages_id = :id_message');
                $stmtmess->execute(array(
                        ':userid' => html($_SESSION['userid']),
                        ':id_message' => html($_GET['id_message'])
                ));

                $nb = $stmtmess->rowCount();

                if ($nb == 0) {
                        $error[] = 'Ce message n\'existe pas...';
                }
                else {
                        // si le message a été trouvé, on l'affiche
                        $data = $stmtmess->fetch();
                        echo '<br>';
                                echo '<span class="font-medium bold">Message de : </span>'.html($data['expediteur']).'<br>';
                                sscanf($data['messages_date'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                                echo '<div class="fl_right font-tiny"><span class="fa fa-calendar"></span> : '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</div>';
                                echo '<span class="font-medium bold">Titre : </span>'.html($data['messages_titre']);
                                echo '<p class="justify" style="border-left:6px orange solid; padding-left:15px; margin-left:30px;">'.BBCode2Html(strip_tags($data['messages_message'])).'</p><br>';

                        echo '<p class="fl_right first">';
                                // on affiche un lien pour répondre au message
                                echo '<a href="messages_repondre.php?id_message=' , html($_GET['id_message']) , '&id_destinataire=' , html($data['memberid']) ,'"><input type="button" class="button small green" value="Répondre"></a> ';
                                // on affiche également un lien permettant de supprimer ce message de la boite de réception
                                echo '<a href="messages_supprimer.php?id_message=' , html($_GET['id_message']) , '"><input type="button" class="button small red" value="Supprimer" onclick="return confirm(\'Êtes-vous certain de vouloir supprimer ce message ?\')"></a>';
                        echo '</p>';
                }
                $stmtmess->closeCursor();
        }

                // On met à jour le champ "messages_lu" de blog_messages à 1 pour signifier que le message a été lu
                $stmt = $db->prepare('UPDATE blog_messages SET messages_lu = "1" WHERE messages_id = :messages_id');
                $stmt->execute(array(
                        ':messages_id' => $_GET['id_message']
                ));


                //S'il y a des erreurs, on les affiche
                if(isset($error)){
                        foreach($error as $error){
                                echo '<div class="msg-alert error rnd8">ERREUR : '.$error.'</div>';
                        }
                }
        ?>



        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

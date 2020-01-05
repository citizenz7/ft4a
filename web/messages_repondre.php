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
	// on teste si le formulaire a bien été soumis
	if (isset($_POST['go']) && $_POST['go'] == 'Envoyer') {
        	if (empty($_POST['destinataire']) || empty($_POST['titre']) || empty($_POST['message'])) {
                	$error[] = 'Au moins un des champs est vide.';
        	}
        	else {

                // si tout a été bien rempli, on insère le message dans notre table SQL
                $stmt = $db->prepare('INSERT INTO blog_messages (messages_id_expediteur,messages_id_destinataire,messages_date,messages_titre,messages_message) VALUES (:messages_id_expediteur,:messages_id_destinataire,:messages_date,:messages_titre,:messages_message)');
                $stmt->execute(array(
                        ':messages_id_expediteur' => html($_SESSION['userid']),
                        ':messages_id_destinataire' => html($_POST['id_destinataire']),
                        ':messages_date' => date("Y-m-d H:i:s"),
                        ':messages_titre' => html($_POST['titre']),
                        ':messages_message' => html($_POST['message'])
                ));

                header('Location: /profil.php?membre='.html($_SESSION['username']).'&message=ok');
                //$stmt->closeCursor();
                //exit();
        	}
	}

	//S'il y a des erreurs, on les affiche
	if(isset($error)){
        	foreach($error as $error){
                	echo '<div class="alert-msg rnd8 error">ERREUR : '.$error.'</div>';
        	}
	}
	

	$desti = $db->prepare('SELECT * FROM blog_messages LEFT JOIN blog_members ON blog_members.memberID = blog_messages.messages_id_expediteur WHERE messages_id = :message_id');
	$desti->execute(array(
        	':message_id' => html($_GET['id_message'])
	));
	$data = $desti->fetch();
	?>

	<form class="rnd5" action="messages_repondre.php" method="post">
	   <div class="form-input clear">
        	<input type="hidden" name="id_destinataire" value="<?php echo html($_GET['id_destinataire']); ?>">
        	<label for="destinataire">Répondre à : 
		    <input type="text" name="destinataire" value="<?php echo html(trim($data['username'])); ?>">
		</label>
        	<br>
        	<label for="titre">Titre : 
		   <input type="text" name="titre" size="70" value="Re: <?php echo html(trim($data['messages_titre'])); ?>">
		</label>
       	 	<br>
        	<label for="message">Message : 
		   <textarea name="message" rows="15">
			<?php echo html(trim($data['messages_message'])); ?>
        	   </textarea>
		</label>
	    </div>
	    <br><p>
        	<input type="submit" class="button small orange" name="go" value="Envoyer">
		&nbsp;
		<input type="reset" value="Annuler" class="button small grey">
		</p>
	</form>

        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

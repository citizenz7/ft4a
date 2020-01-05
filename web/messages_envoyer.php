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

		if (empty($_POST['destinataire'])) {
			$error[] = 'Le champ destinataire est vide.';
		}

		elseif (empty($_POST['titre'])) {
                	$error[] = 'Veuillez entrer un titre pour votre message.';
        	}

		elseif (empty($_POST['message'])) {
                	$error[] = 'Votre message est vide ??!';
        	}

		else {
			//reCaptcha
			$secret = "6LfrmrUUAAAAAGcsi7lz-SSW0XnZj8DMex4gBF0P";
			$response = $_POST['g-recaptcha-response'];
			$remoteip = $_SERVER['REMOTE_ADDR'];
			$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
				. $secret
				. "&response=" . $response
				. "&remoteip=" . $remoteip ;
			$decode = json_decode(file_get_contents($api_url), true);

			if ($decode['success'] == true) {
				// si tout a été bien rempli, y compris le captcha, on insère le message dans notre table SQL
				$stmt = $db->prepare('INSERT INTO blog_messages (messages_id_expediteur,messages_id_destinataire,messages_date,messages_titre,messages_message) VALUES (:messages_id_expediteur,:messages_id_destinataire,:messages_date,:messages_titre,:messages_message)');
				$stmt->execute(array(
					':messages_id_expediteur' => html($_SESSION['userid']),
					':messages_id_destinataire' => html($_POST['destinataire']),
					':messages_date' => date("Y-m-d H:i:s"),
					':messages_titre' => html($_POST['titre']),
					':messages_message' => html(trim($_POST['message']))
				));

				header('Location: /messagerie.php?membre='.html($_SESSION['username']).'&message=ok');
				$stmt->closeCursor();
				exit();
			} // /if decode
			else {
				echo '<div class="alert-msg error rnd8"><span class="fa fa-warning font-large"></span>&nbsp;ERREUR : mauvais code anti-spam !</div>';
			}
		} // /else
	} // /if isset post go


// on sélectionne tous les membres ... sauf le Visiteur (ID 32) ... et soi-même :)
$desti = $db->prepare('SELECT username as nom_destinataire, memberID as id_destinataire FROM blog_members WHERE memberID <> :session AND memberID != 32 ORDER BY username ASC');
$desti->bindValue(':session', $_SESSION['userid'], PDO::PARAM_INT);
$desti->execute();
?>

<form class="rnd5" action="messages_envoyer.php" method="post">
Pour :
 	<?php
        if (isset($_GET['destuser']) && isset($_GET['destid']) && !empty($_GET['destuser']) && !empty($_GET['destid'])) {
		echo '<select name="destinataire">';
                       	echo '<option value="'.html($_GET['destid']).'">'.html(trim($_GET['destuser'])).'</option>';
		echo '</select>';
        }
	else {
  		echo '<select name="destinataire">';
		// on alimente le menu déroulant avec les login des différents membres du site
		while ($data = $desti->fetch()) {
			echo '<option value="'.html($data['id_destinataire']).'">'.html(trim($data['nom_destinataire'])).'</option>';
		}
  		echo '</select>';
	}
	?>	
<br>

<?php
//S'il y a des erreurs, on les affiche
if(isset($error)){
	foreach($error as $error){
		echo '<div class="alert-msg rnd8 error">ERREUR : '.$error.'</div>';
	}
}
?>

<br>
<span class="fa fa-search"></span>&nbsp;<a href="recherche_membres.php">Rechercher un membre</a>
<br>
<br>

<div class="form-input clear">
<label for="titre">Titre : 
   <input type="text" name="titre" size="50" value="<?php if (isset($_POST['titre'])) echo html(trim($_POST['titre'])); ?>">
</label>
<br>
<label for="message">Message : 
	<textarea rows="10" name="message"><?php if (isset($_POST['message'])) echo html(trim($_POST['message'])); ?></textarea>
</label>
</div>
<br>
<div class="g-recaptcha" data-sitekey="6LfrmrUUAAAAAOU9sv-UO9A6joAVpLvrRB3sCbtt"></div>
<br><br>
<p class="right">
<input type="submit" class="button small orange" name="go" value="Envoyer">
&nbsp;
<input type="reset" value="Annuler" class="button small grey">
</p>
</form>

<?php
$desti->closeCursor();
?>

        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

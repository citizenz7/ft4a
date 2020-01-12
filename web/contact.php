<?php
include_once 'includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require (WEBPATH.'classes/vendor/autoload.php');

$pagetitle = 'Nous contacter';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';

?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="contact" class="clear">
      <div class="two_third first">


	<?php
	// Affichage : message envoyé !
	if(isset($_GET['action'])){
		echo '<div class="alert-msg rnd8 success">Votre message a bien été envoyé ! Nous y répondrons dès que possible ! <a class="close" href="#">X</a></div>';
	}
	if(isset($_GET['wrong_code'])) {
		echo '<br><div class="alert-msg rnd8 error"><span class="fa fa-warning font-medium"></span>&nbsp;Mauvais code anti-spam ! <a class="close" href="#">X</a></div>';
	}
	?>

	<?php
	//if form has been submitted process it
	if(isset($_POST['submit'])) {
		$name = html($_REQUEST["name"]);
        	$subject = html(strip_tags($_REQUEST["subject"]));
        	$message = strip_tags(html($_REQUEST["message"]));
        	$from = html($_REQUEST["from"]);
        	//$verif_box = $_REQUEST["verif_box"];

 		if($name ==''){
                	$error[] = '<span class="fa fa-warning font-large"></span>&nbsp;Veuillez entrer un pseudo !';
        	}

        	if($from ==''){
                	$error[] = '<span class="fa fa-warning font-large"></span>&nbsp;Veuillez entrer une adresse e-mail !';
        	}

		// On vérifie l'e-mail
		if (isset($from) && !empty($from)) {
			if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
        			$error[] = '<span class="fa fa-warning font-large"></span>&nbsp;Cette adresse e-mail n\'est pas valide !';
			}
		}

        	if($subject ==''){
                	$error[] = '<span class="fa fa-warning font-large"></span>&nbsp;Veuillez préciser un sujet !';
        	}

        	if($message ==''){
                	$error[] = '<span class="fa fa-warning font-large"></span>&nbsp;Votre message est vide ?!?';
        	}

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
			if(!isset($error)) {

				$mail = new PHPMailer;
				$mail->CharSet = CHARSET;

				$mail->isSMTP();			// Active l'envoi via SMTP
				$mail->Host = SMTPHOST;			// À remplacer par le nom de votre serveur SMTP
				$mail->SMTPAuth = true;			// Active l'authentification par SMTP
				$mail->Username = SITEMAIL;		// Nom d'utilisateur SMTP (votre adresse email complète)
				$mail->Password = SITEMAILPASSWORD;	// Mot de passe de l'adresse email indiquée précédemment
				$mail->Port = SMTPPORT;			// Port SMTP
				$mail->SMTPSecure = 'tls';		// Utiliser SSL / TLS
				$mail->isHTML(true);			// Format de l'email en HTML
				$mail->SMTPDebug = 2;			// Debug perposes

				$mail->From = $from;		// L'adresse mail de l'emetteur du mail (en général identique à l'adresse utilisée pour l'authentification SMTP)
				$mail->FromName = $name;	// Le nom de l'emetteur qui s'affichera dans le mail
				$mail->addAddress(SITEMAIL);	// Un premier destinataire

				//$mail->addAddress('ellen@example.com');	// Un second destifataire (facultatif)
										// Possibilité de répliquer la ligne pour plus de destinataires
				$mail->addReplyTo($from);		// Pour ajouter l'adresse à laquelle répondre (en général celle de la personne ayant rempli le formulaire)
				//$mail->addCC('cc@example.com');	// Pour ajouter un champ Cc
				//$mail->addBCC('bcc@example.com');	// Pour ajouter un champ Cci

				$mail->Subject = 'Message depuis '.SITENAMELONG.' : '.$subject;	 // Le sujet de l'email

				$message = "Nom: ".$name."<br><br>".$message;
                                $message = "De: ".$from."<br>".$message;

				$mail->Body = nl2br($message);	 // Le contenu du mail en HTML
				//$mail->AltBody = 'Contenu du message pour les clients non HTML'; // Le contenu du mail au format texte

				if(!$mail->send()) {
					echo '<div class="alert-msg rnd8 error">';
					echo '<span class="fa fa-warning"></span>&nbsp;Le message ne peut être envoyé :( <br>';
					echo 'Erreur: ' . $mail->ErrorInfo . '</div><br><br>';
				} 
				else {
					header("Location: /contact.php?action=ok");
				}

				// PHPMailer

			} //if(!isset($error))
		} //if($decode['success'] == true)
	
		else {
    			$error[] = '<span class="fa fa-warning font-large"></span>&nbsp;ERREUR : Vous n\'avez pas validé l\'anti-spam</span>';
		}
	} // /if isset

	if(isset($error)) {
		foreach($error as $error){
        		echo '<div class="alert-msg rnd8 error">'.$error.'</div>';
        	}
	}
?>


<h2>Nous contacter :</h2>
<p>Merci d'utiliser le formulaire ci-dessous pour nous contacter :</p>
<br>

<div class="clear">

<form class="rnd5" action="" method="post">

<div class="form-input clear">

	<div class="one_half">
	<label for="name">Votre nom :
	   <?php $nom = isset($_POST['name']) ? $_POST['name'] : ''; ?>
	   <input name="name" type="text" value="<?php echo html($nom); ?>">
	<Label>
	</div>
	<div class="one_half">
	<label for="from">Votre e-mail :
	   <?php $de = isset($_POST['from']) ? $_POST['from'] : ''; ?>
	   <input name="from" type="text" value="<?php echo html($de); ?>">
	</label>
	</div>
<br><br><br>
	<label for="subject">Sujet :
	   <?php $sujet = isset($_POST['subject']) ? $_POST['subject'] : ''; ?>
	   <input name="subject" type="text" value="<?php echo html($sujet); ?>">
	</label>
<br>
	<label for="message">Message :
           <textarea rows="12" name="message"></textarea>
        </label>

<br>
	<label for="verif_box">Anti-spam : <br>
           <div class="g-recaptcha" data-sitekey="6LfrmrUUAAAAAOU9sv-UO9A6joAVpLvrRB3sCbtt"></div>
        </label>

</div>

<br><br><p>
	<div class="fl_right">
	<input name="submit" class="button small orange" type="submit" value="Envoyer le message">
	&nbsp;
	<input type="reset" value="Annuler" class="button small grey">
	</div>
</p>
</form>
<br>

</div>

	<!-- ### -->
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

<?php
include_once 'includes/config.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/Exception.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/PHPMailer.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/SMTP.php');

// Une fois le formulaire envoyé
if(isset($_POST["recuperationpass"]) && $_POST['recuperationpass']) {

	if(!empty($_POST['email'])) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$error[] = 'Cette adresse e-mail n\'est pas valide !';
		}
		else {
			$email = htmlentities($_POST['email']);
		}
	}
	
	else {
		$error[] = 'veuillez renseigner votre adresse email.';
	}

	$stmt = $db->query("SELECT email FROM blog_members WHERE email = '".$email."' ");

	//si le nombre de lignes retourne par la requete != 1
	if ($stmt->rowCount() != 1) {
		$error[] = 'adresse e-mail inconnue.';
	}

	//reCaptcha
	$secret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
	$response = $_POST['g-recaptcha-response'];
	$remoteip = $_SERVER['REMOTE_ADDR'];
	$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
		. $secret
		. "&response=" . $response
		. "&remoteip=" . $remoteip ;
	$decode = json_decode(file_get_contents($api_url), true);

	//Captcha validation ?
	if ($decode['success'] == true) {

	if(!isset($error)) {
		$row1 = $stmt->fetch();
		
		$retour = $db->query("SELECT password FROM blog_members WHERE email = '".$email."' ");
		$row2 = $retour->fetch();
		$new_password = fct_passwd(); //création d'un nouveau mot de passe
		$hashedpassword = $user->password_hash($new_password, PASSWORD_BCRYPT); // cryptage du password

		/*
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.SITENAMELONG.' <'.SITEMAIL.'>'."\r\n";
		*/

		$subject = 'Votre nouveau mot de passe sur '.SITENAMELONG;

		$body = "Bonjour,<br>\n";
		$body .= "Vous avez demandé un nouveau mot de passe pour votre compte sur " . SITENAMELONG . ".<br>\n";
		$body .= "Votre nouveau mot de passe est : " . $new_password . "<br>\n\n";
		$body .= "Cordialement,<br>\n\n";
		$body .= "L'equipe de " . SITENAMELONG;

		$emaildest = $row1['email'];

		$mail = new PHPMailer;
		$mail->CharSet = CHARSET;

		$mail->isSMTP();                        // Active l'envoi via SMTP
		$mail->Host = SMTPHOST;                 // À remplacer par le nom de votre serveur SMTP
		$mail->SMTPAuth = true;                 // Active l'authentification par SMTP
		$mail->Username = SITEMAIL;             // Nom d'utilisateur SMTP (votre adresse email complète)
		$mail->Password = SITEMAILPASSWORD;     // Mot de passe de l'adresse email indiquée précédemment
		$mail->Port = SMTPPORT;                 // Port SMTP
		$mail->SMTPSecure = 'tls';              // Utiliser SSL / TLS
		$mail->isHTML(true);                    // Format de l'email en HTML

		$mail->From = SITEMAIL; 		// L'adresse mail de l'emetteur du mail (en général identique à l'adresse utilisée pour l'authentification SMTP)
		$mail->FromName = SITENAMELONG;         // Le nom de l'emetteur qui s'affichera dans le mail
		$mail->addAddress($emaildest);          // Destinataire

		$mail->addReplyTo(SITEMAIL);            // Pour ajouter l'adresse à laquelle répondre (en général celle de la personne ayant rempli le formulaire)
		
		$mail->Subject = $subject;  // Le sujet de l'email
		$mail->Body = $body;       // Le contenu du mail en HTML

		if(!$mail->send()) {
			echo '<div class="alert-msg rnd8 error">';
			echo '<span class="fa fa-warning"></span>&nbsp;Le message ne peut être envoyé :( <br>';
			echo 'Erreur: ' . $mail->ErrorInfo . '</div><br><br>';
		} 
		else {
			// si tout est ok, le mail a été envoyé
			//mise à jour BD avec le nouveau mot de passe utilisateur
                        $stmt = $db->prepare('UPDATE blog_members SET password = :password WHERE email = :email') ;
                        $stmt->execute(array(
                                ':password' => $hashedpassword,
                                ':email' => $email
                        ));

                	header("Location: /recup_pass.php?action=ok");
		}

		/*
		if(!mail($row1['email'], $objet, $message, $headers)) {
			$error[] = "Problème lors de l'envoi du mail.";
		}

		else {
			//mise à jour de la base de données de l'utilisateur
			$stmt = $db->prepare('UPDATE blog_members SET password = :password WHERE email = :email') ;
        		$stmt->execute(array(
            			':password' => $hashedpassword,
            			':email' => $email
        		));
	
		header("Location: /recup_pass.php?action=ok");
		}
		 */

		} // captcha validation

	}
}
	
$pagetitle = 'Demande de nouveau mot de passe';

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

	<?php
	if(isset($_GET['action'])){
		echo '<div class="alert-msg rnd8 success">Un mail contenant votre nouveau mot de passe vous a été envoyé.<br/>Veuillez le consulter avant de vous reconnecter sur ' . SITENAMELONG . ' ! <a class="close" href="#">X</a></div><br>';
}
	?>

	<h2>Vous avez oublié votre mot de passe ?</h2>

	   <div class="alert-msg rnd8 warning justify">
        	Vous allez faire une demande de nouveau mot de passe.<br>
                Ce nouveau mot de passe vous sera envoyé par e-mail.<br>
                Une fois connecté avec vos identifiants, vous pourrez éventuellement redéfinir un mot de passe à partir de votre page profil.<br>
                Veuillez donc entrer ci-dessous l'adresse e-mail associée à votre compte :
	   </div>

	<div>
	   <form class="rnd5" action='' method='post'>
	      <div class="form-input clear four_fifth">
	        <label for="email">Entrez votre adresse e-mail : 
		    <input type="text" style="width:450px;" name="email">
	        </label>
		<br>
		<label for="verif_box">Anti-spam : <br>
			<div class="g-recaptcha" data-sitekey="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></div>
		</label>
     	      </div>
	      <br><br><br><br><br><br><br><br><br>
		<p class="right">
	         <input type="submit" name="recuperationpass" class="button small orange" value="Envoyer">
	         &nbsp;<input type="reset" value="Annuler" class="button small grey">
	      </p>
	   </form>
	   <br>
	</div>

	<br><br>
	<?php
	if(isset($error)){
		foreach($error as $error){
			echo '<div class="alert-msg error rnd8 five_sixth first"><span class="fa fa-warning"></span> ERREUR : '.$error.'</div>';
		}
	}
	?>
	<!-- ### -->
        </div>
	
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

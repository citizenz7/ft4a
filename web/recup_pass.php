<?php
include_once 'includes/config.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/Exception.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/PHPMailer.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/SMTP.php');

$pagetitle = 'Récupération de votre mot de passe';

// Une fois le formulaire envoyé
if(isset($_POST['submit'])) {

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

		} //if isset $error
	}//if decode success
	else {
		$error[] = 'Erreur anti-spam';
	}
}

include_once 'includes/header.php';
?>

<body id="top">

<div class="container">

        <header>

                <!-- titre -->
                <?php include_once 'includes/header-title.php'; ?>

                <!-- navbar -->
                <?php include_once 'includes/navbar.php'; ?>

        </header>

        <div class="container p-3 my-3 border">
                <div class="row">
                        <div class="col-sm-9">
				<?php
					if(isset($_GET['action'])){
						echo '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Un mail contenant votre nouveau mot de passe vous a été envoyé.<br/>Veuillez le consulter avant de vous reconnecter sur ' . SITENAMELONG . ' ! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}
				?>

				<h4>Vous avez oublié votre mot de passe ?</h4>

	   			<div class="small alert alert-warning text-justify rounded">
        				Vous allez faire une demande de nouveau mot de passe.<br>
                			Ce nouveau mot de passe vous sera envoyé par e-mail.<br>
                			Une fois connecté avec vos identifiants, vous pourrez éventuellement redéfinir un mot de passe à partir de votre page profil.<br>
                			Veuillez donc entrer ci-dessous l'adresse e-mail associée à votre compte :
	   			</div>

				<div class="container bg-light py-2 px-2 small">
	   				<form class="form-group" action='' method='post'>
	        					<label for="email">Entrez votre adresse e-mail : 
		    						<input class="form-control" type="text" style="width:450px;" name="email" required>
	        					</label>
							<br>
							<label for="verif_box">Anti-spam : <br>
								<div class="g-recaptcha" data-sitekey="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></div>
							</label>
							<div class="form-group">
								<button class="btn btn-primary btn-sm mb-2 mt-2" type="submit" name="submit">Envoyer</button>
								<button class="btn btn-secondary btn-sm ml-3 mb-2 mt-2" type="reset">Annuler</button>
							</div>
	   				</form>
				</div>


				
				<?php
				if(isset($error)){
					if (is_array($error) || is_object($error)) {	
						foreach($error as $error){
							echo '<div class="alert alert-danger mt-3 alert-dismissible fade show small" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
						}
					}
				}
				?>



			</div> <!-- //col-sm-9 -->
			
			<!-- sidebar -->
                        <?php include_once 'includes/sidebar.php'; ?>

		</div> <!-- //row -->

		<!-- footer -->
        	<?php include_once 'includes/footer.php'; ?>

	</div> <!-- //container coprs -->

</div> <!-- //container global -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

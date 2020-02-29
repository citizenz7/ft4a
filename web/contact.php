<?php
include_once 'includes/config.php';

//PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/Exception.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/PHPMailer.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/SMTP.php');

$pagetitle = 'Contact';

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
			
			<!-- Contact -->
			<div class="col-sm-9 mb-4 small">

				<?php
				// Affichage : message envoyé !
				if(isset($_GET['action'])){
					echo '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Votre message a bien été envoyé ! Nous y répondrons dès que possible !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				}
				if(isset($_GET['wrong_code'])) {
					echo '<div class="alert alert-danger mt-3 alert-dismissible fade show">Mauvais code anti-spam !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				}
				
				//if form has been submitted process it
				if(isset($_POST['submit'])) {
					$name = html($_REQUEST["name"]);
        				$subject = html(strip_tags($_REQUEST["subject"]));
        				$message = strip_tags(html($_REQUEST["message"]));
        				$from = html($_REQUEST["from"]);
        				
 					if($name ==''){
                				$error[] = 'Veuillez entrer un pseudo !';
        				}
					if($from ==''){
                				$error[] = 'Veuillez entrer une adresse e-mail !';
        				}

					// On vérifie l'e-mail
					if (isset($from) && !empty($from)) {
						if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
        						$error[] = 'Cette adresse e-mail n\'est pas valide !';
						}
					}

        				if($subject ==''){
                				$error[] = 'Veuillez préciser un sujet !';
        				}

        				if($message ==''){
                				$error[] = 'Votre message est vide ?!?';
        				}

					//reCaptcha
					$secret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
					$response = $_POST['g-recaptcha-response'];
					$remoteip = $_SERVER['REMOTE_ADDR'];
					$api_url = "https://www.google.com/recaptcha/api/siteverify?secret=" 
        					. $secret
        					. "&response=" . $response
        					. "&remoteip=" . $remoteip ;
  					$decode = json_decode(file_get_contents($api_url), true);

					if ($decode['success'] == true) {	
						if(!isset($error)) {
							//PHPMailer
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
								echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">';
								echo 'Le message ne peut être envoyé :( <br>';
								echo 'Erreur: ' . $mail->ErrorInfo . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
							} 
							else {
								header("Location: /contact.php?action=ok");
							}
							// PHPMailer

						} //if(!isset($error))
					} //if($decode['success'] == true)

					else {
    						$error[] = 'ERREUR : Vous n\'avez pas validé l\'anti-spam';
					}
				} // /if isset post submit

				if(isset($error)) {
					foreach($error as $error){
        					echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        				}
				}
				?>


				<div class="container bg-light">
					<h3 class="text-center">Nous contacter</h3>
					<p class="text-center"><em>Merci d'utiliser le formulaire ci-dessous pour nous envoyer un message. Nous y répondrons dès que possible.</em></p>
						<form class="form-group" action="" method="post">
							<div class="row">
								<div class="col">
									<div class="row">
										<div class="col-sm-6 form-group">
											<input class="form-control" id="name" name="name" placeholder="Votre nom" type="text" required>
										</div>
										<div class="col-sm-6 form-group">
											<input class="form-control" id="email" name="from" placeholder="Votre E-mail" type="email" required>
										</div>
										<div class="col-sm-12 form-group">
											<input class="form-control" id="subject" name="subject" placeholder="Sujet" type="text" required>
										</div>
									</div>
									<textarea class="form-control" id="message" name="message" placeholder="Votre message" rows="10" required></textarea>
									<br>
									<label for="verif_box">Anti-spam : <br>
           									<div class="g-recaptcha" data-sitekey="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></div>
        								</label>
									<div class="row">
										<div class="col-md-12 form-group text-right">
											<button class="btn btn-primary mb-2 mt-3" name="submit" type="submit">Envoyer</button>
											<button class="btn btn-secondary ml-3 mb-2 mt-3" type="reset">Annuler</button>
										</div>
									</div>
								</div>
							</div>
						</form>
				</div> 

			</div> <!-- //col-sm-9 -->
			
			<!-- sidebar -->
			<?php include_once 'includes/sidebar.php'; ?>

		</div> <!-- //row -->
		

	<!-- footer -->
	<?php include_once 'includes/footer.php'; ?>

</div> <!-- //container -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html> 

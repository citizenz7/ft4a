<?php
include_once 'includes/config.php';

//PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/Exception.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/PHPMailer.php');
require (WEBPATH.'classes/vendor/phpmailer/phpmailer/src/SMTP.php');

//Si l'utilisateur est déjà loggé, on le renvoie sur l'index
if($user->is_logged_in()) {
	header('Location: ./');
}

$pagetitle = 'Créer un compte';
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
			<div class="col-sm-9 small">
				<div class="row">
				<div class="col-sm-5 bg-light ml-3 mr-3 p-3">
					<h4>Créer un compte</h4>
					Vous allez créer un compte sur <?php echo SITENAMELONG; ?>. Le fait de devenir membre vous fera bénéficier de plusieurs avantages :

					<ul>
						<li>pouvoir downloader (télécharger) des torrents</li>
						<li>pouvoir uploader (proposer) des torrents,</li>
						<li>disposer de statistiques personnelles</li>
						<li>disposer d'un espace membre et d'une messagerie interne,</li>
					</ul>
					Merci de choisir un pseudo, un mot de passe et une adresse e-mail. Vous recevrez un e-mail de notre part avec un lien qui vous permettra d'activer votre nouveau compte.
					<br><span style="color:green; font-style:italic; font-size:9pt;">(Eventuellement, merci de vérifier votre répertoire Spam)</span>
					</div>

					<?php
        				//if form has been submitted process it
        				if(isset($_POST['submit'])){

                				//collect form data
                				extract($_POST);

                				//very basic validation
                				if($username ==''){
                        				$error[] = 'Veuillez entrer un pseudo';
                				}

                				if($password ==''){
                        				$error[] = 'Veuillez entrer un mot de passe';
                				}

						if (strlen($password) < 6) {
                					$error[] = 'Le mot de passe est trop court ! (6 caractères minimum)';
                				}

                				if($passwordConfirm ==''){
                        				$error[] = 'Veuillez confirmer le mot de passe';
                				}

                				if($password != $passwordConfirm){
                        				$error[] = 'Les mots de passe ne concordent pas';
                				}

						$email = filter_var($email, FILTER_SANITIZE_EMAIL);
						if(($email =='') || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
							$error[] = 'Veuillez entrer une adresse e-mail valide';
						}

						// On cherche si l'adresse e-mail est déjà dans la base
						if (isset($email) && !empty($email)) {
							//$postemail = filter_input(INPUT_POST, $email, FILTER_SANITIZE_EMAIL);
							$stmt = $db->prepare('SELECT email FROM blog_members WHERE email = :email');		
							$stmt->bindValue(':email',$email,PDO::PARAM_STR);
							$stmt->execute();
							$res = $stmt->fetch();

							if ($res) {
								$error[] = 'Cette adresse e-mail est déjà utilisée !';
							}
						
							//Vérification simple de la validité de l'e-mail
							if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$error[] = 'Cette adresse e-mail n\'est pas valide !';
							}
						} //if isset $email

						// Le username ne peut pas contenir de caractères spéciaux, balises, etc.
						$postusername = $_POST['username'];
						if (!preg_match("/^[a-zA-Z0-9]+$/",$postusername)) {
							$error[] = 'Le pseudo ne peut contenir que des lettres et des chiffres !';
						}

						// On cherche si le pseudo fait moins de 6 caractères
                				if (strlen($_POST['username']) < 6) {
                					$error[] = 'Le pseudo est trop court ! (6 caractères minimum)';
						}

						// On cherche si le pseudo fait + de 15 caractères
						if (strlen($_POST['username']) > 15) {
							$error[] = 'Le pseudo est trop long ! (15 caractères maximum)';
						}
						// ... et s'il est déjà dans la base
						else {
							$stmt = $db->prepare('SELECT username FROM blog_members WHERE username = :username');
							$stmt->bindValue(':username',$postusername,PDO::PARAM_STR);
            						$stmt->execute();
            						$row = $stmt->fetch();

                        				if (!empty($row['username'])) {
                                				$error[] = 'Ce pseudo est déjà utilisé ! Merci d\'en choisir un autre';
                        				}
                				}

						// reCaptcha
						$secret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
						$response = $_POST['g-recaptcha-response'];
						$remoteip = $_SERVER['REMOTE_ADDR'];
						$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
							. $secret
							. "&response=" . $response
							. "&remoteip=" . $remoteip ;
						$decode = json_decode(file_get_contents($api_url), true);

						if ($decode['success'] == true) {

							if(!isset($error)){
								$hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);
								$pid = md5(uniqid(rand(),true));
								$activation = md5(uniqid(rand(),true));

								// Remove all illegal characters from an email address
								$email = filter_var($email, FILTER_SANITIZE_EMAIL);
								
								try {
									//On insert les données dans la table blog_members
                                					$result1 = $db->prepare('INSERT INTO blog_members (username,password,email,pid,memberDate,active) VALUES (:username,:password,:email,:pid,:memberDate,:active)') ;
                                					$result1->execute(array(
                                        					':username' => html($username),
                                        					':password' => $hashedpassword,
                                        					':email' => $email,
										':pid' => $pid,
										':memberDate' => date('Y-m-d H:i:s'),
										':active' => $activation
                                					));

									$newuid = $db->lastInsertId();

									//On insert aussi le PID et l'ID du membre dans la table xbt_users
									$result2 = $db->prepare('INSERT INTO xbt_users (uid, torrent_pass) VALUES (:uid, :torrent_pass)');
									$result2->execute(array(
										':uid' => $newuid,
										':torrent_pass' => $pid
									));

									if(!$result1 || !$result2) {	
                              							$error[] = 'Erreur : votre compte utilisateur n\'a pas pu être créé.';
                         						}

									else {
										// si tout OK, on envoie le mail de confirmation de compte
										$newuid = $db->lastInsertId();
										$to = $email;
										$subject = "Confirmation d'enregistrement de compte sur ".SITENAMELONG;
										$body = "<p>".$username.",</p>
										<p>Merci pour votre enregistrement sur ".SITENAMELONG.".</p>
										<p>Pour activer votre compte, veuillez cliquer sur le lien suivant :<br>
										<a href='".SITEURLHTTPS."/activate.php?x=$newuid&y=$activation'>".SITEURLHTTPS."/activate.php?x=$newuid&y=$activation</a></p>
										<p>Cordialement,
										<br>".SITEAUTOR.", webmaster de ".SITENAMELONG."</p>";

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
                                						$mail->FromName = SITENAMELONG;		// Le nom de l'emetteur qui s'affichera dans le mail
                                						$mail->addAddress($to);    		// Destinataire
										$mail->addReplyTo(SITEMAIL);    	// Pour ajouter l'adresse à laquelle répondre (en général celle de la personne ayant rempli le formulaire)
										$mail->Subject = $subject;  		// Le sujet de l'email
                                						$mail->Body    = $body;       		// Le contenu du mail en HTML
                                						//$mail->AltBody = 'Contenu du message pour les clients non HTML'; // Le contenu du mail au format texte

										if(!$mail->send()) {
                                        						echo '<div class="alert-msg rnd8 error">';
                                        						echo '<span class="fa fa-warning"></span>&nbsp;Le message ne peut être envoyé :( <br>';
                                        						echo 'Erreur: ' . $mail->ErrorInfo . '</div><br><br>';
                                						} 
										else {
                                							header('Location: /membres.php?action=activation');
                                							exit;
										}
									}

                        					} 
								catch(PDOException $e) {
                            						echo $e->getMessage();
                        					}
                					}// if !isset $error
						} // decode success captcha

						else {
    							$error[] = 'Erreur anti-spam';
						}

					}

					?>

				<div class="col-sm-6 bg-light ml-4 pt-3">
					<form class="form-group" action="" method="post">
						<div class="col">
							<label for="username"><strong>Choisissez un pseudo</strong><br>(6 caractères mini / 15 maxi)</label>
                	   					<input type="text" class="form-control mr-3" name="username" id="username" value="<?php if(isset($error)){ echo $_POST['username'];}?>" required>
                					<br><label for="password"><strong>Choisissez un mot de passe</strong><br>(6 caractères mini)</label>
                	   					<input type="password" class="form-control mr-3" name='password' value="<?php if(isset($error)){ echo $_POST['password'];}?>" required>
                					<br><label for="passwordConfirm"><strong>Confirmation du mot de passe</strong></label>
                	   					<input type="password" class="form-control" name="passwordConfirm" value="<?php if(isset($error)){ echo $_POST['passwordConfirm'];}?>" required>
						</div>
						<div class="col">
							<br><label for="email"><strong>E-mail</strong></label>
                	   					<input type="text" class="form-control" name="email" value="<?php if(isset($error)){ echo $_POST['email'];}?>" required>
							<br><label for="captcha"><strong>Anti-spam</strong></label>
   								<div class="g-recaptcha" data-sitekey="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"></div>
							<br>
							<button class="btn btn-primary btn-sm mb-2 mt-2" type="submit" name="submit">Créer un compte</button>
							<button class="btn btn-secondary btn-sm ml-3 mb-2 mt-2" type="reset">Annuler</button>
						</div>
        				</form>

				</div> <!-- container bg-light -->
			</div> <!-- row -->

				<?php
	 			if(isset($error)){
                			foreach($error as $error){
                        			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
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

<?php
include_once 'includes/config.php';

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
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<div class="first">

	<h2>Créer un compte</h2>

	<div class="one_half first justify">
		Vous allez créer un compte sur <?php echo SITENAMELONG; ?>. 
		Le fait de devenir membre vous fera bénéficier de plusieurs avantages :
		<ul class="list arrow indent">
			<li>pouvoir downloader (télécharger) des torrents</li>
			<li>pouvoir uploader (proposer) des torrents,</li>
			<li>disposer de statistiques personnelles</li>
			<li>disposer d'un espace membre et d'une messagerie interne,</li>
		</ul>
		Merci de choisir un pseudo, un mot de passe et une adresse e-mail.<br>
		<br>Vous recevrez un e-mail de notre part avec un lien qui vous permettra d'activer votre nouveau compte.<br>
		<br><span style="color:green; font-style:italic; font-size:9pt;">(Eventuellement, merci de vérifier votre répertoire Spam)</span>
	</div>

	<?php
        //if form has been submitted process it
        if(isset($_POST['submit'])){

                //collect form data
                extract($_POST);

                //very basic validation
                if($username ==''){
                        $error[] = 'Veuillez entrer un pseudo.';
                }

                if($password ==''){
                        $error[] = 'Veuillez entrer un mot de passe.';
                }

		if (strlen($password) < 4) {
                	$error[] = 'Le mot de passe est trop court ! (4 caractères minimum)';
                }

                if($passwordConfirm ==''){
                        $error[] = 'Veuillez confirmer le mot de passe.';
                }

                if($password != $passwordConfirm){
                        $error[] = 'Les mots de passe ne concordent pas.';
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
                                $error[] = 'Ce pseudo est déjà utilisé ! Merci d\'en choisir un autre.';
                        }
                }

		// reCaptcha
		$secret = "6LfrmrUUAAAAAGcsi7lz-SSW0XnZj8DMex4gBF0P";
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

				if(!$result1 || !$result2)
                         	{
                              		$error[] = 'Erreur : votre compte utilisateur n\'a pas pu être créé.';
                         	}

				else {
					// si tout OK, on envoie le mail de confirmation de compte
					$newuid = $db->lastInsertId();	
					$to = $email;
					$subject = "Confirmation d'enregistrement de compte sur ".SITENAMELONG;
					$body = "<p>Merci pour votre enregistrement sur ".SITENAMELONG.".</p>
					<p>Pour activer votre compte, veuillez cliquer sur le lien suivant: <a href='".SITEURLHTTPS."/activate.php?x=$newuid&y=$activation'>".SITEURLHTTPS."/activate.php?x=$newuid&y=$activation</a></p>
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
                                	} else {
                                		header('Location: /membres.php?action=activation');
                                		exit;
					}
				}


                        } catch(PDOException $e) {
                            echo $e->getMessage();
                        }

                }

	} // captcha

	else {
    		$error[] = 'Mauvais code captcha.';
	}

        }

        //check for any errors
	/*
        if(isset($error)){
                foreach($error as $error){
                        echo '<div class="alert-msg error rnd8"><span class="fa fa-warning"></span> ERREUR : '.$error.'</div><br><br>';
                }
        }
	*/
        ?>

	<div class="one_half">
        	<form  id="myform" class="rnd5" action="" method="post">
		   <div class="form-input clear">
			<label for="username">Choisissez un pseudo (6 caractères minimum / 15 caractères maximum)
                	   <input type="text" name="username" style="width:100%;" id="username" value="<?php if(isset($error)){ echo $_POST['username'];}?>">
			</label>
			<br>
                	<label for="password">Choisissez un mot de passe (6 caractères minimum)
                	   <input type="password" style="width:100%;" name='password' id="myPassword" value="<?php if(isset($error)){ echo $_POST['password'];}?>">
			</label>
			<br>
                	<label for="passwordConfirm">Confirmation du mot de passe
                	   <input type="password" style="width:100%;" name="passwordConfirm" id="passwordConfirm" value="<?php if(isset($error)){ echo $_POST['passwordConfirm'];}?>">
			</label>
			<br>
                	<label for="email">E-mail
                	   <input type="text" style="width:100%;" name="email" value="<?php if(isset($error)){ echo $_POST['email'];}?>">
			</label>
			<br>
			<label for="captcha">Anti-spam : 
   				<div class="g-recaptcha" data-sitekey="6LfrmrUUAAAAAOU9sv-UO9A6joAVpLvrRB3sCbtt"></div>
			</label>
		   </div>
                   <br><p>
		      <input type="submit" class="button small orange" name="submit" value="Créer un compte">
		      &nbsp;
		      <input type="reset" value="Annuler" class="button small grey">
		   </p>
        	</form>

		<script type="text/javascript" src="/layout/scripts/strength.js"></script>
		<script type="text/javascript" src="/layout/scripts/js.js"></script>

		<script>
		$(document).ready(function($) {
			$('#myPassword').strength({
				strengthClass: 'strength',
				strengthMeterClass: 'strength_meter',
				strengthButtonClass: 'button_strength',
				strengthButtonText: '<span class="fa fa-eye"></span> ',
				strengthButtonTextToggle: '<span class="fa fa-eye-slash"></span> '
			});
		});
		</script>

		<br>
	</div>

	<!-- ### -->
        </div>

	<div class="divider2"></div>	
	
	<?php
	 if(isset($error)){
                foreach($error as $error){
                        echo '<div class="alert-msg error rnd8"><span class="fa fa-warning"></span> ERREUR : '.$error.'</div>';
                }
        }
	?>

      </div>

<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

<?php
include_once 'includes/config.php';

//Si l'utilisateur est déjà loggé, on le renvoie sur l'index
if($user->is_logged_in()) {
	header('Location: ./');
}

$pagetitle = 'Connexion membres';

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
        	//check the action
                switch ($_GET['action']) {
                	case 'active':
                        $message = '<div class="alert-msg rnd8 success">Votre compte est maintenant actif. Vous pouvez vous connecter <a class="close" href="#">X</a></div>';
                        break;
                        case 'echec':
                        $message = '<div class="alert-msg rnd8 error"><span class="fa fa-warning font-large"></span>&nbsp;Erreur : votre compte n\'a pas pu être activé :/<br />Merci de vérifier le lien d\'activation.<br />
			En cas de problème persistant, merci d\'informer le webmaster en utilisant <a href="/contact.php">le formulaire de contact du site</a> et en indiquant votre pseudo et votre adresse e-mail <a class="close" href="#">X</a></div>';
                        break;
			case 'connecte':
                        $message = '<div class="alert-msg rnd8 warning"><span class="fa fa-warning font-large"></span>&nbsp;Vous devez être connecté(e) pour accéder à cette page <a class="close" href="#">X</a></div>';
                        break;
			case 'connecteprofil':
                        $message = '<div class="alert-msg rnd8 warning"><span class="fa fa-warning font-large"></span>&nbsp;Vous devez être connecté(e) pour accéder à la page profil d\'un membre<a class="close" href="#">X</a></div>';
                        break;
			case 'pasledroit':
			$message = '<div class="alert-msg rnd8 error"><span class="fa fa-warning font-large"></span>&nbsp;Erreur : vous n\'avez pas le droit d\'accéder à cette page <a class="close" href="#">X</a></div>';
			break;
			case 'deco':
			$message = '<div class="alert-msg rnd8 warning"><span class="fa fa-warning font-large"></span>&nbsp;Vous avez été déconnecté(e) pour inactivité pendant 15 minutes. Veuillez vous reconnecter.</div>';
			break;
			case 'compte':
                        $message = '<div class="alert-msg rnd8 warning"><span class="fa fa-warning font-large"></span>&nbsp;Seuls les membres inscrits peuvent télécharger ou uploader des torrents. La création du compte ne prend que quelques secondes : <a href="/signup.php">créer un compte</a></div>';
			break;
                }
	}

	//process login form if submitted
	if(isset($_POST['submit'])){
        	$username = html(trim($_POST['username']));
                $password = html(trim($_POST['password']));

                if($user->login($username,$password)) {
                	//Une fois connecté, on retourne sur la page index
			write_log('<span style="color:green; font-weight:bold;">Connexion utilisateur :</span> '.$username, $db);
                	header('Location: ./');
                	exit;
                }

		else {
                	$message = '<div class="alert-msg rnd8 error">Erreur : mauvais identifiants ou compte non activé <a class="close" href="#">X</a></div>';
                }

		}//end if submit

		if(isset($message)) {
			echo $message;
		}

	?>

	<div class="two_fifth first">
	<form class="rnd5" action="" method="post">
           <div class="form-input clear">
              <label for="username">Pseudo :
                 <input type="text" name="username" id="username" value="">
              </label>
		<br>
	      <label for="password">Mot de passe :
		 <input type="password" name="password" value="">
	      </label>
	   </div>
	   <br><p>
              <input type="submit" value="Connexion" name="submit" class="button small orange">
              &nbsp;
              <input type="reset" value="Annuler" class="button small grey">
           </p>
	</form>
		
	<br><p><a href="recup_pass.php">Mot de passe oublié ?</a></p>
	</div>

	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

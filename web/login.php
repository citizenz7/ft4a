<?php
include_once 'includes/config.php';

//Si l'utilisateur est déjà loggé, on le renvoie sur l'index
if($user->is_logged_in()) {
	header('Location: ./');
}

$pagetitle = 'Page de connexion';
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
        			//check the action
                		switch ($_GET['action']) {
                			case 'active':
                        		$message = '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Votre compte est maintenant actif. Vous pouvez vous connecter<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        		break;
                        		case 'echec':
                        		$message = '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">Erreur : votre compte n\'a pas pu être activé :/<br />Merci de vérifier le lien d\'activation.<br />
					En cas de problème persistant, merci d\'informer le webmaster en utilisant <a href="/contact.php">le formulaire de contact du site</a> et en indiquant votre pseudo et votre adresse e-mail<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        		break;
					case 'connecte':
                        		$message = '<div class="alert alert-warning mt-3 alert-dismissible fade show" role="alert">Vous devez être connecté(e) pour accéder à cette page<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        		break;
					case 'connecteprofil':
                        		$message = '<div class="alert alert-warning mt-3 alert-dismissible fade show" role="alert">Vous devez être connecté(e) pour accéder à la page profil d\'un membre<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        		break;
					case 'deco':
					$message = '<div class="alert alert-warning mt-3 alert-dismissible fade show" role="alert">Vous avez été déconnecté(e) pour inactivité pendant 10 minutes. Veuillez vous reconnecter.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					break;
					case 'compte':
                        		$message = '<div class="alert alert-warning mt-3 alert-dismissible fade show" role="alert">Seuls les membres inscrits peuvent télécharger ou uploader des torrents. La création du compte ne prend que quelques secondes : <a href="/signup.php">créer un compte</a><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
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
                			$message = '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">Erreur : mauvais identifiants ou compte non activé<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                		}

			}//end if submit

			if(isset($message)) {
				echo $message;
			}
			?>

			<div class="container bg-light pt-4 pb-4">
				<div class="row text-center mb-4">
					<div class="col-md-12">
						<h5>ft4a.fr</h5>
						<small><?php echo SITEVERSION; ?></small>
					</div>
				</div>
				<div class="row text-center">
					<div class="col-md-6 offset-md-3">
						<div class="card">
							<div class="card-body">
								<img src="<?php echo SITEURLHTTPS; ?>/images/logo.png" style="max-width:90px;" alt="ft4a.fr">
								<div class="login-form mt-4">
									<form class="form-group py-2 px-2" action="" method="post">
										<div class="form-row">
											<div class="form-group col-md-12">
												<input class="form-control" name="username" placeholder="Pseudo" type="text" required>
											</div>
											<div class="form-group col-md-12">
												<input class="form-control" name="password" placeholder="Mot de passe" type="password" required>
											</div>
										</div>
										<div class="form-row">
											<button class="btn btn-primary btn-block" type="submit" name="submit">Connexion</button>
										</div>
									</form>
								</div>
								<div class="logi-forgot text-center mt-2 small">
									<a href="recup_pass.php">Mot de passe oublié ?</a></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


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

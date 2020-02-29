<?php
include_once 'includes/config.php';
$pagetitle = 'Messagerie interne : envoyer un message';
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
			// on teste si le formulaire a bien été soumis
			if (isset($_POST['submit'])) {
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
						echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : mauvais code anti-spam !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}
				} // /else
			} // /if isset post submit 

			// on sélectionne tous les membres ... sauf le Visiteur (ID 32) ... et soi-même :)
			$desti = $db->prepare('SELECT username as nom_destinataire, memberID as id_destinataire FROM blog_members WHERE memberID <> :session AND memberID != 32 ORDER BY username ASC');
			$desti->bindValue(':session', $_SESSION['userid'], PDO::PARAM_INT);
			$desti->execute();
			?>

			<div class="container bg-light small">
			<form class="form-group" action="" method="post">
				Pour :
 				<?php
        			if (isset($_GET['destuser']) && isset($_GET['destid']) && !empty($_GET['destuser']) && !empty($_GET['destid'])) {
					echo '<select class="custom-select custom-select-sm" name="destinataire">';
                       				echo '<option value="'.html($_GET['destid']).'">'.html(trim($_GET['destuser'])).'</option>';
					echo '</select>';
        			}
				else {
  					echo '<select class="custom-select custom-select-sm" name="destinataire">';
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
						echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					}
				}
				?>

				<br>
				<a href="recherche_membre.php" class="btn btn-primary btn-sm" role="button" aria-pressed="true"><i class="fas fa-search"></i> Rechercher un membre</a>
				<br><br>

				<div class="row">
					<div class="col-sm-12">
						<label for="titre">Titre :<br> 
   							<input type="text" name="titre" size="100" value="<?php if (isset($_POST['titre'])) echo html(trim($_POST['titre'])); ?>">
						</label>
					</div>
				</div>
				<div class="row">
                                        <div class="col-sm-12">
						<label for="message">Message :<br> 
							<textarea id="tiny" name="message"><?php if (isset($_POST['message'])) echo html(trim($_POST['message'])); ?></textarea>
						</label>
						<script>
   						    tinymce.init({
						    	selector: 'textarea#tiny',
							width: 754,
							menubar: false
   						    });
						</script>
					</div>
				</div>
				Anti-spam :<br>
				<div class="g-recaptcha" data-sitekey="6LfrmrUUAAAAAOU9sv-UO9A6joAVpLvrRB3sCbtt"></div>
				<div class="row">
					<div class="col-md-12 form-group text-right">
						<button class="btn btn-primary mb-2 mt-3" name="submit" type="submit">Envoyer</button>
						<button class="btn btn-secondary ml-3 mb-2 mt-3" type="reset">Annuler</button>
					</div>
				</div>
			</form>
			</div>

			<?php
			$desti->closeCursor();
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

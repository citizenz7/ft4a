<?php
include_once '../includes/config.php';

if(!$user->is_logged_in()) {
        header('Location: ../login.php');
}

//Il n'y a que l'admin qui accède à cette page
if(isset($_SESSION['userid']) && $_SESSION['userid'] != 1) {
        header('Location: ../');
}

$pagetitle = 'Message groupé à tous les membres';

include_once '../includes/header.php';
?>

<body id="top">

<div class="container">

        <header>

                <!-- titre -->
                <?php include_once '../includes/header-title.php'; ?>

                <!-- navbar -->
                <?php include_once '../includes/navbar.php'; ?>

        </header>

        <div class="container p-3 my-3 border">
                <div class="row">
                        <div class="col-sm-9">

				<?php include_once('menu.php'); ?>

				<?php
				// on teste si le formulaire a bien été soumis
				if (isset($_POST['submit'])) {

					if (empty($_POST['titre'])) {
                				$error[] = 'Veuillez entrer un titre pour votre message.';
        				}
					if (empty($_POST['message'])) {
                				$error[] = 'Votre message est vide ??!';
        				}
					try {
						//On cherche tous les membres sauf l'admin (1) et le Visiteur (32) 
						$getusers = $db->query('SELECT * FROM blog_members WHERE memberID != 1 AND active = "yes"');
						while($result = $getusers->fetch(PDO::FETCH_ASSOC)) {
							$stmt = $db->prepare('INSERT INTO blog_messages (messages_id_expediteur,messages_id_destinataire,messages_date,messages_titre,messages_message) VALUES (:messages_id_expediteur,:messages_id_destinataire,:messages_date,:messages_titre,:messages_message)');
							$stmt->execute(array(
								':messages_id_expediteur' => 1,
								':messages_id_destinataire' => $result['memberID'],
								':messages_date' => date("Y-m-d H:i:s"),
								':messages_titre' => html($_POST['titre']),
								':messages_message' => html($_POST['message'])
							));
						} //while
						header('Location: /admin/index.php?&message=envoye');
						$stmt->closeCursor();
						exit();
					}
					catch(PDOException $e) {
						echo $e->getMessage();
					}
				}//if isset post submit
				?>

				<form class="form-group" action="/admin/messages_envoyer_tous.php" method="post">
					<div class="alert alert-info"><h4>Envoyer un message à tous les membres : </h4></div>
					<?php
					//S'il y a des erreurs, on les affiche
					if(isset($error)){
						foreach($error as $error){
							echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
						}
					}
					?>

					<br>

					Titre du message :<br>
					<input type="text" class="form-control" name="titre" size="50" value="<?php if (isset($_POST['titre'])) echo html(trim($_POST['titre'])); ?>" required>
					<br>
					Message : <br>
					<textarea class="form-control" rows="7" name="message"><?php if (isset($_POST['message'])) echo trim($_POST['message']); ?></textarea>
					<br>
					<p class="text-right">
						<button class="btn btn-primary mb-2 mt-3" name="submit" type="submit">Envoyer</button>
                    				<button class="btn btn-primary ml-3 mb-2 mt-3" type="reset">Annuler</button>
						<!--
						<input type="submit" class="button small orange" name="go" value="Envoyer">
						&nbsp;
						<input type="reset" class="button small grey" value="Annuler">
						-->
					</p>
				</form>


			</div> <!-- //col-sm-9 -->
			
			<!-- sidebar -->
                        <?php include_once '../includes/sidebar.php'; ?>

		</div> <!-- //row -->

		<!-- footer -->
        	<?php include_once '../includes/footer.php'; ?>

	</div> <!-- //container coprs -->

</div> <!-- //container global -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

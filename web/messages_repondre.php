<?php
include_once 'includes/config.php';
$pagetitle = 'Messagerie : répondre aux messages';
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
        				if (empty($_POST['destinataire']) || empty($_POST['titre']) || empty($_POST['message'])) {
                				$error[] = 'Au moins un des champs est vide.';
        				}
        				else {
                				// si tout a été bien rempli, on insère le message dans notre table SQL
                				$stmt = $db->prepare('INSERT INTO blog_messages (messages_id_expediteur,messages_id_destinataire,messages_date,messages_titre,messages_message) VALUES (:messages_id_expediteur,:messages_id_destinataire,:messages_date,:messages_titre,:messages_message)');
                				$stmt->execute(array(
                        				':messages_id_expediteur' => html($_SESSION['userid']),
                        				':messages_id_destinataire' => html($_POST['id_destinataire']),
                        				':messages_date' => date("Y-m-d H:i:s"),
                        				':messages_titre' => html($_POST['titre']),
                        				':messages_message' => html($_POST['message'])
                				));

                				header('Location: /profil.php?membre='.html($_SESSION['username']).'&message=ok');
                				//$stmt->closeCursor();
                				exit;
        				}
				}//if isset post submit

				//S'il y a des erreurs, on les affiche
				if(isset($error)){
        				foreach($error as $error){
                				echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        				}
				}

				$desti = $db->prepare('SELECT * FROM blog_messages LEFT JOIN blog_members ON blog_members.memberID = blog_messages.messages_id_expediteur WHERE messages_id = :message_id');
				$desti->execute(array(
        				':message_id' => html($_GET['id_message'])
				));
				$data = $desti->fetch();
				?>

				<div class="container bg-light small">
					<form class="form-group py-2 px-2" action="" method="post">
						<div class="row">
							<div class="col-sm-12">
        							<input type="hidden" name="id_destinataire" value="<?php echo html($_GET['id_destinataire']); ?>">
        							<label for="destinataire">Répondre à :<br>
		    							<input type="text" name="destinataire" value="<?php echo html($data['username']); ?>" readonly>
								</label>
        							<br>
        							<label for="titre">Titre :<br>
		   							<input type="text" name="titre" size="100" value="Re: <?php echo html($data['messages_titre']); ?>">
								</label>
								<br>
								Message de <?php echo html($data['username']); ?> :<br>
								<div class="container">
									<div class="row">
                                                               			<div class="col-sm-12 alert alert-info text-justify"><?php echo htmlspecialchars_decode(nl2br($data['messages_message'])); ?></div>
									</div>
								</div>
        							<label for="message">Votre réponse :<br> 
									<textarea id="tiny" name="message" cols ="100" rows="10"></textarea>
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
						<div class="row text-center small">
                    					<div class="col-md-12 form-group">
                        					<button class="btn btn-primary mb-2 mt-3" type="submit" name="submit">Envoyer</button>
                        					<button class="btn btn-secondary ml-3 mb-2 mt-3" type="reset">Annuler</button>
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

	</div> <!-- //container coprs -->

</div> <!-- //container global -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

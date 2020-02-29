<?php
include_once 'includes/config.php';
$pagetitle = 'Messagerie : lire les messages';
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
			<div class="container bg-light small">
			<?php
        		// on teste si notre paramètre existe bien et qu'il n'est pas vide
        		if (!isset($_GET['id_message']) || empty($_GET['id_message'])) {
                		$error[] = 'Aucun message reconnu.';
        		}

			else {
                		// on prépare une requete SQL selectionnant la date, le titre et l'expediteur du message que l'on souhaite lire, tout en prenant soin de vérifier que le message appartient bien au membre connecté
                		$stmtmess = $db->prepare('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_messages.messages_message, blog_members.memberID as memberid, blog_members.username as expediteur FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = :userid AND blog_messages.messages_id_expediteur = blog_members.memberID AND blog_messages.messages_id = :id_message');
                		$stmtmess->execute(array(
                        		':userid' => html($_SESSION['userid']),
                        		':id_message' => html($_GET['id_message'])
                		));
				$nb = $stmtmess->rowCount();

                		if ($nb == 0) {
                        		$error[] = 'Ce message n\'existe pas...';
                		}
                		else {
                        		// si le message a été trouvé, on l'affiche
                        		$data = $stmtmess->fetch();
					echo '<div class="row p-3">';
						echo '<div class="col-sm-6">';
							echo '<span class="font-weight-bolder">Message de : </span>'.html($data['expediteur']);
						echo '</div>';
						echo '<div class="col-sm-6">';
                                			sscanf($data['messages_date'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                                			echo '<div class="text-right small"><span class="fas fa-calendar"></span> Envoyé le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="row pl-3 pr-3">';
						echo '<div class="col-sm-12">';
                                			echo '<span class="font-weight-bolder">Titre : </span>'.html($data['messages_titre']);
                                			echo '<p class="text-justify" style="border-left:6px orange solid; padding-left:15px; margin-left:30px;">'.htmlspecialchars_decode(nl2br(trim($data['messages_message']))).'</p><br>';
						echo '</div>';
					echo '</div>';
					echo '<div class="row p-3">';
                                                echo '<div class="col-sm-12 text-right">';
                                			// on affiche un lien pour répondre au message
							echo '<a href="messages_repondre.php?id_message=' , html($_GET['id_message']) , '&id_destinataire=' , html($data['memberid']) ,'" class="btn btn-success btn-sm mr-2" tabindex="-1" role="button">Répondre</a>';
							// on affiche également un lien permettant de supprimer ce message de la boite de réception
							echo '<a href="messages_supprimer.php?id_message=' , html($_GET['id_message']) , '" class="btn btn-danger btn-sm" tabindex="-1" role="button" onclick="return confirm(\'Êtes-vous certain de vouloir supprimer ce message ?\')">Supprimer</a>';
                        			echo '</div>';
					echo '</div>';
                		}
                		$stmtmess->closeCursor();
        		}

                	// On met à jour le champ "messages_lu" de blog_messages à 1 pour signifier que le message a été lu
                	$stmt = $db->prepare('UPDATE blog_messages SET messages_lu = "1" WHERE messages_id = :messages_id');
                	$stmt->execute(array(
                        	':messages_id' => $_GET['id_message']
                	));

                	//S'il y a des erreurs, on les affiche
                	if(isset($error)){
                        	foreach($error as $error){
                                	echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        	}
                	}
        		?>
			</div> <!-- //container bg-ligh -->
			
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

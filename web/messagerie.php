<?php
include_once 'includes/config.php';
$pagetitle = 'Messagerie interne';
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
			//On affiche le résultat de l'envoi de message interne
			if(isset($_GET['message'])) {
				echo '<div class="alert alert-success mt-3 alert-dismissible fade show">Le message a été envoyé avec succès !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			}

			if(isset($_GET['action']) && $_GET['action'] == 'messupprime'){
				echo '<div class="alert alert-success mt-3 alert-dismissible fade show">Le message a été supprimé de votre messagerie !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			}

			try {
				$stmt = $db->prepare('SELECT * FROM blog_members,xbt_users WHERE blog_members.memberID = xbt_users.uid AND username = :username');
				$stmt->bindvalue('username', $_GET['membre'], PDO::PARAM_STR);
				$stmt->execute();
				$row = $stmt->fetch();
			}

			catch(PDOException $e) {
				echo $e->getMessage();
			}

			$pages = new Paginator('10','m');
			$stmt = $db->prepare('SELECT messages_id FROM blog_messages WHERE messages_id_destinataire = :destinataire');
			$stmt->execute(array(
				':destinataire' => $row['memberID']
			));
			$pages->set_total($stmt->rowCount());

			// on prépare une requete SQL cherchant le titre, la date, l'expéditeur des messages pour le membre connecté
			$stmt = $db->prepare('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_members.username as expediteur, blog_messages.messages_id as id_message, blog_messages.messages_lu FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = :id_destinataire AND blog_messages.messages_id_expediteur = blog_members.memberID ORDER BY blog_messages.messages_date DESC '.$pages->get_limit());
			$stmt->bindValue(':id_destinataire', $row['memberID'], PDO::PARAM_INT);
			$stmt->execute();
			?>

			<div class="alert alert-info text-center"><h4>Messagerie interne</h4></div>
			<p class="text-center">
				<a href="<?php echo SITEURL; ?>/messages_envoyer.php" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Envoyer un message à un membre</a>
			</p>
			<br>

			<table class="table table-striped small">
	    			<thead class="thead-dark">
					<th class="border border-white" style="width:20%;">Date</th>
					<th class="border border-white">Titre</th>
                			<th class="border border-white">Expéditeur</th>
	    			</thead>
				<?php
			while($data = $stmt->fetch()){
				echo '<tbody>';
					echo '<tr>';
						sscanf($data['messages_date'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        			echo '<td class="small">le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</td>';
						echo '<td>';
							if($data['messages_lu'] == 0) {
								echo '<span class="fas fa-envelope"></span>&nbsp;';	
							}				
							echo '<a href="'.SITEURL.'/messages_lire.php?id_message='.$data['id_message'].'">'.html(trim($data['messages_titre'])).'</a>';
						echo '</td>';
						echo '<td>'.html(trim($data['expediteur'])).'</td>';
					echo '</tr>';
				echo '</tbody>';
			}
			?>
			</table>

			<?php
			echo '<div class="text-center">';
				echo $pages->page_links('messagerie.php?membre='.html($row['username']).'&');
			echo '</div>';
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

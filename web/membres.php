<?php
include_once 'includes/config.php';
$pagetitle = 'Membres';
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
			
			<!-- Liste des membres -->
			<div class="col-sm-9 mb-4">

			<?php
			//Message de création de compte...
        		if(isset($_GET['action']) && $_GET['action'] == 'activation'){
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
					Merci ! Votre compte est en cours de création.<br>Vous allez recevoir, par e-mail, un lien afin de l\'activer.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
        		}

			if(isset($_GET['action']) && $_GET['action'] == 'noexistmember'){
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Erreur : ce membre n\'existe pas.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
        		?>
				<p>Rechercher des membres inscrits :</p>
				<form class="form-group" method="post" action="recherche_membre.php">
					<input type="text" name="requete" class="form-control" placeholder="Tapez le pseudo du membre">
						<div class="text-right mt-2">
							<button type="submit" class="btn btn-primary btn-sm">Recherche</button>
							<button type="reset" class="btn btn-primary btn-sm">Annuler</button>
						</div>
				</form>
				
				<h4>Liste des membres</h4>
				<table class="table table-striped table-hover small table-responsive-sm">
					<thead class="thead-dark">
					<tr>
						<th width="40%;"><a href="membres.php?tri=username&ordre=desc"><i class="fas fa-sort-up text-white"></i></a>Pseudo<a href="membres.php?tri=username&ordre=asc"><i class="fas fa-sort-down text-white"></i></a></th>
						<th class="text-center"><a href="membres.php?tri=memberDate&ordre=desc"><i class="fas fa-sort-up text-white"></i></a>Inscription le<a href="membres.php?tri=memberDate&ordre=asc"><i class="fas fa-sort-down text-white"></i></a></th>
						<th class="text-center"><a href="membres.php?tri=uploaded&ordre=desc"><i class="fas fa-sort-up text-white"></i></a>Envoyé<a href="membres.php?tri=uploaded&ordre=asc"><i class="fas fa-sort-down text-white"></i></a></th>
						<th class="text-center"><a href="membres.php?tri=downloaded&ordre=desc"><i class="fas fa-sort-up text-white"></i></a>Téléchargé<a href="membres.php?tri=downloaded&ordre=asc"><i class="fas fa-sort-down text-white"></i></a></th>
						<th class="text-center">Ratio</th>
					</tr>
					</thead>

					<tbody>

					<?php
                			try {
						// On affiche 15 membres par page
						$pages = new Paginator('15','p');

						$stmt = $db->query('SELECT memberID FROM blog_members');
						$pages->set_total($stmt->rowCount());

						// On met en place le tri
						if(isset($_GET['tri'])) {
                                			$tri = html($_GET['tri']);
                        			}
                        			else {
							$memberID_tri = 'memberID';
                                			$tri = html($memberID_tri);
                        			}

                        			if(isset($_GET['ordre'])) {
                                			$ordre = html($_GET['ordre']);
                        			}
                        			else {
                                			$ordre_tri = 'DESC';
							$ordre = html($ordre_tri);
                        			}

						// Protection du tri -------------------------
						if (!empty($_GET['tri']) && !in_array($_GET['tri'], array('memberID','username', 'memberDate', 'uploaded', 'downloaded'))) {
							header('Location: index.php');
							exit();
						}

						if (!empty($_GET['ordre']) && !in_array($_GET['ordre'], array('asc','desc','ASC','DESC'))) {
							header('Location: index.php');
							exit();
						}

						// --------------------------------------------

						$stmt = $db->query('SELECT * FROM blog_members,xbt_users WHERE blog_members.memberID=xbt_users.uid AND blog_members.username != "visiteur" AND blog_members.active = "yes" ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit());
                        			while($row = $stmt->fetch()) {
							echo '<tr>';
							if (!empty($row['avatar'])) {
								if ($row['memberID'] == 1) {
									$admin = '<span style="color:red; font-style:italic; font-size:10px;">(Admin)</span>';
									echo '<td class="align-middle">
										<img src="/images/avatars/'.html($row['avatar']).'" style="max-height:30px;" alt="'.html($row['username']).'" class="rounded-circle">&nbsp;
										<a href="/profil.php?membre='.html($row['username']).'">'.html($row['username']).'</a>'
										.$admin.'</td>';
								}
								else {
									echo '<td class="align-middle">
										<img src="/images/avatars/'.html($row['avatar']).'" style="max-height:30px;" alt="'.html($row['username']).'" class="rounded-circle">&nbsp;
										<a href="/profil.php?membre='.html($row['username']).'">'.html($row['username']).
									'</a></td>';
								}
							}
							else {
								echo '<td class="align-middle">
									<img src="/images/avatars/avatar.png" style="max-height:30px;" alt="'.html($row['username']).'" class="rounded-circle">&nbsp;
									<a href="/profil.php?membre='.html($row['username']).'">'.html($row['username']).
									'</a></td>';
							}			

							sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
							echo '<td class="text-center small align-middle">'.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</td>';
                                			echo '<td class="text-center small align-middle">'.makesize($row['uploaded']).'</td>';
                                			echo '<td class="text-center small align-middle">'.makesize($row['downloaded']).'</td>';

							if (intval($row["downloaded"])>0) {
								$ratio=number_format($row["uploaded"]/$row["downloaded"],2);
							}
							else {
								$ratio='&#8734;';
							}

							echo '<td class="text-center small align-middle">'.$ratio.'</td>';
                                			echo '</tr>';
                        			}
                			} 

					catch(PDOException $e) {
                    				echo $e->getMessage();
                			}
        				?>
					</tbody>
				</table>

				<!-- pagination liste membres -->
				<?php
					echo $pages->page_links('membres.php?tri='.$tri.'&ordre='.$ordre.'&');
				?>
			</div>
			
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

<?php 
include_once 'includes/config.php';
$pagetitle = 'Liste des torrents';
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
			
			<!-- Liste des torrents -->
			<div class="col-sm-9 mb-4">

			<?php
        		// On affiche : torrent ajouté ! 
        		if(isset($_GET['action']) && $_GET['action'] == 'ajoute') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
					Le torrent a été ajouté avec succès !
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
        		}

			// Pas d'accès direct à la page download sans file ID
        		if(isset($_GET['action']) && $_GET['action'] == 'nodirect') {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					ERREUR : Vous ne pouvez pas accéder directement à cette page sans préciser le torrent à télécharger
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
        		}

			// Pas d'accès à la page download si le file ID n'existe pas
        		if(isset($_GET['action']) && $_GET['action'] == 'noexist') {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					ERREUR : Ce torrent n\'existe pas !
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
        		}

			//On affiche le message de suppression
			if(isset($_GET['delpost'])){
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Le torrent a été supprimé avec succès !
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>';
			}
			?>

				<table class="table table-striped table-bordered table-hover small table-responsive-sm">
					<thead class="thead-dark">
					<tr>
						<th class="border border-white" width="57%;"><a class="text-white" href="torrents.php?tri=postTitle&ordre=desc"><i class="fas fa-sort-up"></i></a>Nom<a class="text-white" href="torrents.php?tri=postTitle&ordre=asc"><i class="fas fa-sort-down"></i></a></th>
						<th class="border border-white text-center"><a class="text-white" href="torrents.php?tri=postTaille&ordre=desc"><i class="fas fa-sort-up"></i></a>Taille<a class="text-white" href="torrents.php?tri=postTaille&ordre=asc"><i class="fas fa-sort-down"></i></a></th>
						<th class="border border-white text-center"><a class="text-white" href="torrents.php?tri=postDate&ordre=desc"><i class="fas fa-sort-up"></i></a>Ajouté<a class="text-white" href="torrents.php?tri=postDate&ordre=asc"><i class="fas fa-sort-down"></i></a></th>
						<th class="border border-white text-center"><a class="text-white" href="torrents.php?tri=seeders&ordre=desc"><i class="fas fa-sort-up"></i></a>S<a class="text-white" href="torrents.php?tri=seeders&ordre=asc"><i class="fas fa-sort-down"></i></a></th>
						<th class="border border-white text-center"><a class="text-white" href="torrents.php?tri=leechers&ordre=desc"><i class="fas fa-sort-up"></i></a>L<a class="text-white" href="torrents.php?tri=leechers&ordre=asc"><i class="fas fa-sort-down"></i></a></th>
						<th class="border border-white text-center"><a class="text-white" href="torrents.php?tri=completed&ordre=desc"><i class="fas fa-sort-up"></i></a>C<a class="text-white" href="torrents.php?tri=completed&ordre=asc"><i class="fas fa-sort-down"></i></a></th>
					</tr>
					</thead>

					<?php
        				try {
						// On affiche 15 torrents par page
						$pages = new Paginator(NBTORRENTS,'page');

						$stmt = $db->query('SELECT postHash FROM blog_posts_seo');
						$pages->set_total($stmt->rowCount());


						// On met en place le tri--------------------------------------------
						if(isset($_GET['tri'])) {
							$tri = html($_GET['tri']);
						}
						else {
							$post_tri = 'postDate';
							$tri = html($post_tri);
						}

						if(isset($_GET['ordre'])) {
							$ordre = html($_GET['ordre']);
						}
						else {
							$ordre_tri = 'desc';
							$ordre = html($ordre_tri);
						}

						// Protection du tri -----------------------------------------------
						if (!empty($_GET['tri']) && !in_array($_GET['tri'], array('postID','postHash', 'postTitle', 'postViews', 'postTaille', 'postDate', 'postAuthor', 'seeders', 'leechers', 'completed'))) {
							header('Location: index.php');
							exit();
						}
						if (!empty($_GET['ordre']) && !in_array($_GET['ordre'], array('asc','desc','ASC','DESC'))) {
							header('Location: index.php');
							exit();
						}
						// -----------------------------------------------------------------

						$stmt = $db->query('SELECT * FROM blog_posts_seo b LEFT JOIN xbt_files x ON x.fid = b.postID ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit());

						echo '<tbody>';

						while($row = $stmt->fetch()){
							$stmt2 = $db->prepare('SELECT catTitle, catSlug FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID ORDER BY catTitle ASC');
							$stmt2->execute(array(':postID' => $row['postID']));
							$catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);
							
							echo '<tr>';
								//Age du torrent - on ajoute NEW si 30 jours ou moins
								$ndays = 14;
								echo '<td class="align-middle">';
									echo '<img src="/images/imgtorrents/'.$row['postImage'].'" style="max-height:30px;" class="rounded mr-2" alt="'.$row['postTitle'].'" data-toggle="tooltip" title="'.$row['postTitle'].'">';
									echo '<a href="'.html($row['postSlug']).'">'.html($row['postTitle']);
										if(floor((strtotime(date('YmdHi')) - strtotime($row['postDate'])) / (60*60*24)) < $ndays) {
											echo '<span class="badge badge-success ml-2">New</span>';
										}
									echo '</a>';
								echo '</td>';

								echo '<td class="text-center small align-middle">'.html(makesize($row['postTaille'])).'</td>';

								sscanf($row['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
								echo '<td class="text-center small align-middle">'.$jour.'-'.$mois.'-'.$annee.'</td>';
			
								//echo '<td><a href="admin/profil.php?membre='.html($row['postAuthor']).'">'.html($row['postAuthor']).'</a></td>';

								/*
								$links = array();
								foreach ($catRow as $cat) {
									$links[] = '<a href="c-'.html($cat['catSlug']).'">'.html($cat['catTitle']).'</a>';
                    						}

								$max = 300;
								$chaine = implode(", ", $links);
								if (strlen($chaine) >= $max) {
									$chaine = substr($chaine, 0, $max);
									$espace = strrpos($chaine, ", ");
									$chaine = substr($chaine, 0, $espace).' ...';
								}

								echo '<td class="font-tiny center" style="width:150px;">'.$chaine.'</td>';
								*/

								$exa = '0x';
								$hash = $exa.$row['postHash'];
			
								$stmt3 = $db->prepare('SELECT * FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postHash = :postHash AND xbt_files.info_hash = '.$hash);
								$stmt3->execute(array(':postHash' => $row['postHash']));
								$xbt = $stmt3->fetch();

								echo '<td class="text-center align-middle"><a class="text-success" href="peers.php?hash='.html($row['postHash']).'">'.$xbt['seeders'].'</a></td>';
								echo '<td class="text-center align-middle"><a class="text-danger" href="peers.php?hash='.html($row['postHash']).'">'.$xbt['leechers'].'</a></td>';
								echo '<td class="text-center align-middle">'.$xbt['completed'].'</td>';

							echo '</tr>';
						} // /while

						echo '</tbody>';

					} // /try

					catch(PDOException $e) {
						echo $e->getMessage();
					}
        				?>
				</table>

				<!-- Legende liste torrents -->
				<div class="alert alert-secondary small text-center" role="alert">
					L = leechers | S = seeders | C = completed<br>
					Les nouveaux torrents (moins de 15 jours) comportent un badge "New" 
				</div>
				
				<!-- pagination liste torrents -->
				<?php echo $pages->page_links('torrents.php?tri='.$tri.'&ordre='.$ordre.'&'); ?>
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

<?php
include_once 'includes/config.php';

$pagetitle = 'Bienvenue sur '.SITENAMELONG.' !';

include_once 'includes/header.php';
?>

<body id="top">

<!-- container -->
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
				<!-- Derniers torrents -->
				<?php
				try {
					// Préparation de la pagination
					$pages = new Paginator('5','p');
					$stmt = $db->query('SELECT postHash FROM blog_posts_seo');

					// On passe le nb d'enregistrements à $pages
					$pages->set_total($stmt->rowCount());

					//$stmt = $db->query('SELECT postID,postHash,postTitle,postAuthor,postSlug,postDesc,postDate,postImage,postViews FROM blog_posts_seo ORDER BY postDate DESC '.$pages->get_limit());
					$stmt = $db->query('SELECT * FROM blog_posts_seo LEFT JOIN blog_members ON blog_members.username = blog_posts_seo.postAuthor  ORDER BY postDate DESC '.$pages->get_limit());

					while($row = $stmt->fetch()) {
				?>

						<ul class="list-group shadow rounded mb-3">
							<li class="list-group-item">
								<!-- Titre du torrent -->
								<div class="row">
									<div class="col-sm-9">
										<h5><a class="text-secondary" href="<?php echo html($row['postSlug']); ?>"><?php echo html($row['postTitle']); ?></a></h5>
									</div>
									<div class="col-sm-3 text-right small">
										<span class="text-muted">
											<a class="text-muted" href="profil.php?membre=<?php echo html($row['postAuthor']); ?>">
												<?php 
												if (!empty($row['avatar'])) {
													echo '<img src="/images/avatars/'.$row['avatar'].'" class="avatar-rounded-small" style="max-width:20px;" alt="'.html($row['username']).'">';
												}
												else {
													echo '<img src="/images/avatars/avatar.png" class="avatar-rounded-small" style="max-width:20px;" alt="'.html($row['username']).'">';
												}
												echo html($row['postAuthor']);
												?>
											</a>
										</span>
									</div>
								</div>

								<!-- Description (texte de présentation) du torrent + image du torrent -->
								<div class="small" style="text-align:justify;">
									<?php
									if (!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
										echo '<img class="float-left img-fluid img-thumbnail" style="max-height:110px; margin-right:10px;" src="'.$WEB_IMAGES_TORRENTS.html($row['postImage']).'" alt="'.html($row['postTitle']).'">';
									}
									else {
										echo '<img class="float-left img-fluid img-thumbnail" src="images/noimage.png" alt="Pas d\'image" style="max-height:110px;">';
									}
									$max = 400;
									$chaine = $row['postDesc'];
									if (strlen($chaine) >= $max) {
										$chaine = substr($chaine, 0, $max);
										$espace = strrpos($chaine, " ");
										$chaine = substr($chaine, 0, $espace).' ...';
									}
									// On affiche la description du torrent
									echo nl2br(bbcode($chaine));
									?>

									<br><span class="text-left"><a href="<?php echo html($row['postSlug']); ?>" class="text-success">Lire la suite &rarr;</a></span>
									<p class="mt-2">
                                                                        <?php
                                                                        // Date d'ajout du torrent
                                                                        sscanf($row['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                                                                        ?>
                                                                        <i class="fas fa-calendar-alt"></i> <?php echo $jour; ?>-<?php echo $mois; ?>-<?php echo $annee; ?> |
                                                                        <!-- 
                                                                        <i class="fas fa-user"></i> <a class="text-muted" href="profil.php?membre=<?php echo html($row['postAuthor']); ?>"><?php echo html($row['postAuthor']); ?></a> | 
                                                                        -->
                                                                        <?php
                                                                        // Catégories
                                                                        $stmt2 = $db->prepare('
                                                                        SELECT catTitle, catSlug FROM blog_cats, blog_post_cats 
                                                                        WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID');
                                                                        $stmt2->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
                                                                        $stmt2->execute();
                                                                        $catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                                                                        $links = array();

                                                                        foreach ($catRow as $cat) {
                                                                                $links[] = '<a class="text-decoration-none text-dark" href="c-'.html($cat['catSlug']).'">'.html($cat['catTitle']).'</a>';
                                                                        }

                                                                        $max = 120;
                                                                        $chaine = implode(", ", $links);
                                                                        if (strlen($chaine) >= $max) {
                                                                                $chaine = substr($chaine, 0, $max);
                                                                                $espace = strrpos($chaine, ", ");
                                                                                $chaine = substr($chaine, 0, $espace).' - ...';
                                                                        }
                                                                        ?>

                                                                        <!-- On affiche les catégories du torrent -->
                                                                        <i class="fas fa-tags"></i> <?php echo $chaine; ?> |

                                                                        <?php
                                                                        // Licences
                                                                        $stmt4 = $db->prepare('
                                                                        SELECT licenceID,licenceTitle,licenceSlug FROM blog_licences, blog_post_licences
                                                                        WHERE blog_licences.licenceID = blog_post_licences.licenceID_BPL AND blog_post_licences.postID_BPL = :postID_BPL
                                                                        ORDER BY licenceTitle ASC');
                                                                        $stmt4->bindValue(':postID_BPL', $row['postID'], PDO::PARAM_INT);
                                                                        $stmt4->execute();
                                                                        $licenceRow = $stmt4->fetchALL(PDO::FETCH_ASSOC);

                                                                        $liclist = array();
                                                                        foreach($licenceRow as $lic) {
                                                                                $liclist[] = '<a class="text-decoration-none text-dark" href="l-'.html($lic['licenceSlug']).'">'.html($lic['licenceTitle']).'</a>';
                                                                        }
									$max = 140;
                                                                        $chaine = implode(", ", $liclist);
                                                                        if (strlen($chaine) >= $max) {
                                                                                $chaine = substr($chaine, 0, $max);
                                                                                $espace = strrpos($chaine, ", ");
                                                                                $chaine = substr($chaine, 0, $espace).' [...] ';
                                                                        }
                                                                        ?>

                                                                        <!-- On affiche les licences du torrent -->
                                                                        <i class="fab fa-creative-commons"></i> <?php echo $chaine; ?> |

                                                                        <?php
                                                                        // Nb de commentaires
                                                                        $stmt3 = $db->prepare('SELECT cid FROM blog_posts_comments WHERE cid_torrent = :cid_torrent');
                                                                        $stmt3->execute(array(':cid_torrent' => $row['postID']));
                                                                        $commRow = $stmt3->rowCount();

                                                                        if($commRow == 0) { ?>
                                                                                <i class="fas fa-comment-alt"></i> <?php echo $commRow; ?> |
                                                                        <?php }
                                                                        else { ?>
                                                                                <i class="fas fa-comment-alt"></i> <a class="text-muted" href="<?php echo html($row['postSlug']); ?>#commentaires"><?php echo $commRow; ?></a> |
                                                                        <?php } ?>

                                                                        <!-- On affiche le nb de commentaires(s) -->
                                                                        <i class="fas fa-eye"></i> <?php echo html($row['postViews']); ?>
									</p>
								</div>
								
						</li>
					</ul>
				<?php } //While

				} //try

				catch(PDOException $e) {
					echo $e->getMessage();
				}
				?>

				<!-- Pagination -->
				<?php
					echo $pages->page_links();
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

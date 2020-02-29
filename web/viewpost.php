<?php
include_once 'includes/config.php';

$id = isset($_GET['id']) ? $_GET['id'] : NULL;

$stmt = $db->prepare('SELECT postID,postHash,postTitle,postSlug,postAuthor,postLink,postDesc,postCont,postTaille,postDate,postTorrent,postImage FROM blog_posts_seo WHERE postSlug = :postSlug');
$stmt->bindValue(':postSlug', $id, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();

//Si le torrent est à supprimer ...
if(isset($_GET['deltorr'])) {

	$deltorr = (int) $_GET['deltorr'];

	if(isset($_SESSION['username']) && isset($_SESSION['userid'])) {

        	if(($row['postAuthor'] == $_SESSION['username']) || ($_SESSION['userid'] == 1)) {

        		// 1 - on supprime le fichier .torrent dans le répertoire /torrents
        		$stmt4 = $db->prepare('SELECT postID,postTorrent FROM blog_posts_seo WHERE postID = :postID') ;
			$stmt4->bindValue(':postID', $deltorr, PDO::PARAM_INT);
        		$stmt4->execute();
        		$efface = $stmt4->fetch();

        		$file = $REP_TORRENTS.$efface['postTorrent'];
        		if (file_exists($file)) {
                		unlink($file);
        		}

        		// 2 - on supprime le torrent dans la base blog_posts_seo
        		$stmt = $db->prepare('DELETE FROM blog_posts_seo WHERE postID = :postID') ;
			$stmt->bindValue(':postID', $deltorr, PDO::PARAM_INT);
        		$stmt->execute();

        		// 3 - on supprime sa référence de catégorie
        		$stmt1 = $db->prepare('DELETE FROM blog_post_cats WHERE postID = :postID');
			$stmt1->bindValue(':postID', $deltorr, PDO::PARAM_INT);
        		$stmt1->execute();

        		// 4 - on supprime sa référence de licence
        		$stmt2 = $db->prepare('DELETE FROM blog_post_licences WHERE postID_BPL = :postID_BPL');
			$stmt2->bindValue(':postID_BPL', $deltorr, PDO::PARAM_INT);
        		$stmt2->execute();

        		// 5 - on supprime ses commentaires s'ils existent
        		$stmt22 = $db->prepare('SELECT cid_torrent FROM blog_posts_comments WHERE cid_torrent = :cid_torrent');
			$stmt22->bindValue(':cid_torrent', $deltorr, PDO::PARAM_INT);
        		$stmt22->execute();
        		$commentaire = $stmt22->fetch();

        		if(!empty($commentaire)) {
                		$stmtsupcomm = $db->prepare('DELETE FROM blog_posts_comments WHERE cid_torrent = :cid_torrent');
				$stmtsupcomm->bindValue(':cid_torrent', $deltorr, PDO::PARAM_INT);
                		$stmtsupcomm->execute();
        		}

        		// 6 - enfin, on supprime le torrent du tracker en mettant le champ "flag" à "1" dans l'enregistrement correspondant de la table xbt_files
        		$stmt3 = $db->prepare('UPDATE xbt_files SET flags = :flags WHERE fid = :fid') ;
			$stmt3->bindValue(':flags', '1', PDO::PARAM_INT);
			$stmt3->bindValue(':fid', $deltorr, PDO::PARAM_INT);
        		$stmt3->execute();

        		header('Location: torrents.php?action=supprime');
        		//exit;

		} // /if row postAuthor

		else {
			// Alors comme ça vous n'avez pas le droit de supprimer ce torrent ?!!
			header('Location: ./');
                        exit();
		}

	} // /if isset session username

}//fin de if isset $_GET['deltorr']

//Si le post n'existe pas on redirige l'utilisateur
if($row['postID'] == ''){
        header('Location: ./');
        exit();
}

$pagetitle = html($row['postTitle']);
?>

<?php
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
			
			<!-- Présentation du torrent -->
			<div class="col-sm-9">
				<div class="card">
					<?php 
					$stmt = $db->prepare('SELECT postID,postHash,postTitle,postSlug,postAuthor,postLink,postDesc,postCont,postTaille,postDate,postTorrent,postImage,postViews FROM blog_posts_seo WHERE postSlug = :postSlug');
					$stmt->bindValue(':postSlug', $id, PDO::PARAM_STR);
					$stmt->execute();
					$row = $stmt->fetch();
					?>
					<div class="card-body bg-dark text-white py-1 rounded">
						<i class="fas fa-tag"></i> 
						<span class="font-weight-bolder lead"><?php echo html($row['postTitle']); ?></span>
						<?php
						//Edition & suppression par admin ou auteur
						if(isset($_SESSION['username']) && isset($_SESSION['userid'])) {
							if(($row['postAuthor'] == $_SESSION['username']) || ($_SESSION['userid'] == 1)) {
						?>
								<a href="admin/edit-post.php?id=<?php echo html($row['postID']); ?>" class="badge badge-warning"><i class="fas fa-edit"></i></a>
								<a href="javascript:deltorr('<?php echo html($row['postID']); ?>','<?php echo html($row['postTitle']); ?>')" class="badge badge-warning"><i class="fas fa-trash-alt"></i></a>
						<?php
							}
						}
						?>	
						<span class="badge badge-light ml-2 float-right mr-auto mt-1"><i class="fas fa-eye"></i> <?php echo html($row['postViews']); ?></span>
					</div>
				</div>
					
				<div class="card mt-2">
					<div class="card-body">
						<?php
						if (!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
                					echo '<img src="images/imgtorrents/'.html($row['postImage']).'" alt="'.html($row['postTitle']).'" class="float-left thumbnail img-fluid mr-3 mb-4" style="max-height: 150px;">';
                				}
                				else {
                					echo '<img src="images/noimage.png" alt="Image" class="float-left thumbnail img-fluid mr-3 mb-4" style="max-height: 150px;">';
                				}
						?>

						<p class="small text-justify">
							<?php
							echo html($row['postDesc']);
							?>
						</p>
						<?php
						echo '<div class="small justify">';
							echo $row['postCont'];
						echo '</div>';
                                                ?>
                                                </p>
					</div>
				</div>

				<div class="card mt-4">
					<div class="card-body bg-dark text-white font-weight-bolder py-1 rounded"><i class="fas fa-info-circle"></i> Information sur le torrent</div>
				</div>

				<div class="row">
					<div class="col">
						<div class="card mt-2">
							<ul class="list-group small">
								<?php
                                                        	// Catégroie(s)
                                                        	$stmt2 = $db->prepare('SELECT catTitle, catSlug FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID ORDER BY catTitle ASC');
                                                        	$stmt2->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
                                                        	$stmt2->execute();
                                                        	$catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                                                        	$links = array();
                                                        	foreach ($catRow as $cat) {
                                                                	$links[] = "<a href='c-".$cat['catSlug']."'>".$cat['catTitle']."</a>";
                                                        	}
                                                        	$chaine = implode(", ", $links);
                                                        	?>
								<li class="list-group-item">Catégorie(s) : <?php echo $chaine; ?></li>

								<?php
								// Licence(s)
								$stmt3 = $db->prepare('SELECT licenceID,licenceTitle,licenceSlug FROM blog_licences, 
								blog_post_licences WHERE blog_licences.licenceID = blog_post_licences.licenceID_BPL 
								AND blog_post_licences.postID_BPL = :postID_BPL ORDER BY licenceTitle ASC');
        							$stmt3->execute(array(':postID_BPL' => $row['postID']));
								$licenceRow = $stmt3->fetchALL(PDO::FETCH_ASSOC);
								$liclist = array();
								foreach ($licenceRow as $lic) {
									$liclist[] = '<a href="l-'.html($lic['licenceSlug']).'">'.html($lic['licenceTitle']).'</a>';
								}
								$chaine = implode(", ", $liclist);
								?>
								<li class="list-group-item">Licence : <?php echo $chaine; ?></li>

								<!-- Taille -->
								<li class="list-group-item">Taille : <?php echo makesize($row['postTaille']); ?></li>

								<?php
								// Nb de fichiers dans le torrent
								$filetorrent = $REP_TORRENTS.html($row['postTorrent']);
								$fd = fopen($filetorrent, "rb");
								$length = filesize($filetorrent);			

								if ($length) {
									$alltorrent = fread($fd, $length);
								}
								$array = BDecode($alltorrent);
								$hash = sha1(BEncode($array["info"]));
								fclose($fd);

								if (isset($array["info"]) && $array["info"]) {
									$upfile=$array["info"];
								}
								else {
									$upfile = 0;
								}

								if (isset($upfile["length"])) {
									$size = (float)($upfile["length"]);
								}
								else if (isset($upfile["files"])) {
									//Pour les torrents multifichiers (Lupin - Xbtit - Btiteam - 2005)
									$size=0;
									foreach ($upfile["files"] as $file) {
										$size+=(float)($file["length"]);
        								}
								}
								else {
									$size = "0";
								}

								$ffile=fopen($filetorrent,"rb");
								$content=fread($ffile,filesize($filetorrent));
								fclose($ffile);

								$content=BDecode($content);
								$numfiles=0;

								if (isset($content["info"]) && $content["info"]) {
									$thefile=$content["info"];
									if (isset($thefile["length"])) {
										$dfiles[$numfiles]["filename"]=$thefile["name"];
										$dfiles[$numfiles]["size"]=makesize($thefile["length"]);
										$numfiles++;
									}

									elseif (isset($thefile["files"])) {
										foreach($thefile["files"] as $singlefile) {
											$dfiles[$numfiles]["filename"]=implode("/",$singlefile["path"]);
											$dfiles[$numfiles]["size"]=makesize($singlefile["length"]);
											$numfiles++;
										}
									}

									else {
										// Impossible ... mais bon ...
									}
								}

								if (isset($content['info']) && $content['info']) {
            								$thefile=$content['info'];
								}
								if($numfiles == 1) {
       									echo '<li class="list-group-item">Nb de fichier du torrent : '.$numfiles.'</li>';
								}
								//else {
								//	echo '<li class="list-group-item">Nb de fichiers du torrent : '.$numfiles;
								//}

								if (isset($thefile['files'])) {
									echo '<li class="list-group-item">';
										echo '<select class="custom-select custom-select-sm mt-3 mb-3">';
											echo '<option selected>Nb de fichiers du torrent : '.$numfiles.'</option>';
											foreach($content['info']['files'] as $multiplefiles) {
												echo '<i class="fas fa-file"></i> <option>'.implode('/',$multiplefiles['path']).'</option>';
											}
										echo '</select>';
									echo '</li>';
								}
                						else {
                							echo '<li class="list-group-item">Fichier du torrent : ';
										echo '<i class="fas fa-file"></i> '.html($thefile['name']);
									echo '</li>';
								}
								?>
							</ul>
						</div>
					</div>
					<div class="col">
						<div class="card mt-2">
							<ul class="list-group small">
								<li class="list-group-item">Posté par : <a href="profil.php?membre=<?php echo html($row['postAuthor']); ?>"><?php echo html($row['postAuthor']); ?></a></li>
								<li class="list-group-item">Posté le : <?php echo date_fr('d-m-Y à H:i:s', strtotime($row['postDate'])); ?></li>
								<li class="list-group-item">Sur le web : <a href="<?php echo html($row['postLink']); ?>">URL du média</a></li>

								<?php
								// Leechers & seeders
								$stmt3 = $db->prepare('SELECT * FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postID = :postID AND xbt_files.fid = blog_posts_seo.postID');
								$stmt3->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
        							$stmt3->execute();
        							$xbt = $stmt3->fetch();
								?>
								<li class="list-group-item">
									<i class="fas fa-download"></i> Seeders : <span class="font-weight-bolder mr-2"><a class="text-success" href="peers.php?hash=<?php echo html($row['postHash']); ?>"><?php echo $xbt['seeders']; ?></a></span> | 
									<i class="fas fa-upload ml-2"></i> Leechers : <span class="font-weight-bolder"><a class="text-danger" href="peers.php?hash=<?php echo html($row['postHash']); ?>"><?php echo $xbt['leechers']; ?></a></span>
								</li>
								<li class="list-group-item"><i class="fas fa-sort-amount-up"></i> Nb de téléchargements : <span class="text-primary font-weight-bolder"><?php echo $xbt['completed']; ?></span></li>
							</ul>
						</div>
					</div>
				</div>

				<?php
				// on met à jour le nb de vues de l'article
				$stmt33 = $db->query('UPDATE blog_posts_seo SET postViews = postViews+1 WHERE postID = '.$row['postID']);
				?>
				

				<div class="card mt-4 mb-5">
					<a class="btn btn-primary btn-lg" role="button" aria-pressed="true" href="download.php?id=<?php echo html($row['postID']); ?>"> <i class="fas fa-caret-right"></i> Télécharger le torrent <i class="fas fa-caret-left"></i></a>
				</div>

				<!-- Commentaires -->
				<div class="card mt-2">
                                        <div class="card-body bg-dark text-white font-weight-bolder py-1 rounded"><i class="fas fa-comment-alt"></i> <a name="commentaires"></a>Commentaires</div>
                                </div>

				<div class="card px-3 py-3">
					<?php
					// On affiche : commentaire supprimé ! 
        				if(isset($_GET['action']) && $_GET['action'] == 'commsupprime') {
        					echo '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Le commentaire a été supprimé.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        				}

					if(!$user->is_logged_in()) {
						echo '<div class="alert alert-warning mt-3 alert-dismissible fade show small" role="alert">
							Vous devez être <a href="/login.php">connecté(e)</a> pour rédiger un commentaire.
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>';
					}

					// il faut être connecté pour laisser un commentaire
        				if($user->is_logged_in()) {
        				?>

						<form action="" method="post">
							<div class="form-group">
								<label for="validationDefaultUsername">Pseudo</label>
			      					<input type="text" class="form-control" id="validationDefault01" name="username" value="<?php echo $_SESSION['username']; ?>" required>
							</div>
							<div class="form-group">
								<label for="Validation DefaultCommentaire">Commentaire</label>
			   					<textarea class="form-control" name="commentaire" id="commentaire" rows="4" required></textarea>
							</div>
							<!--
							<div class="form-group">
								<label for="verif_box">Anti-spam :</label>
								<br>
           							<div class="g-recaptcha" data-sitekey="6LfrmrUUAAAAAOU9sv-UO9A6joAVpLvrRB3sCbtt"></div>
							</div>
							-->
							<button class="btn btn-primary btn-sm" type="submit" name="submitcomm">Envoyer le commentaire</button>
                   					<button class="btn btn-secondary btn-sm" type="reset">Annuler</button>
						</form>
					<?php
        				} // fin if
					?>

				<?php
				if(isset($_POST['submitcomm'])) {
					//collect form data
           				extract($_POST);
			
					if($username ==''){
                				$error[] = 'Veuillez indiquer un pseudo.';
                			}						

					if($commentaire =='') {
                				$error[] = 'Veuillez au moins entrer un ou deux mots pour ce commentaire... sinon, ce n\'est plus un commentaire ! :D.';
                			}

					//reCaptcha
					/*
					$secret = "6LfrmrUUAAAAAGcsi7lz-SSW0XnZj8DMex4gBF0P";
					$response = $_POST['g-recaptcha-response'];
					$remoteip = $_SERVER['REMOTE_ADDR'];
					$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
					. $secret
					. "&response=" . $response
					. "&remoteip=" . $remoteip ;
					$decode = json_decode(file_get_contents($api_url), true);

					if ($decode['success'] == true) {
					*/

						if(!isset($error)) {
							try {
                                				$stmt = $db->prepare('INSERT INTO blog_posts_comments (cid_torrent,cadded,ctext,cuser) VALUES (:cid_torrent, :cadded, :ctext, :cuser)') ;
                                				$stmt->execute(array(
                                        				':cid_torrent' => $row['postID'],
                                        				':cadded' => date('Y-m-d H:i:s'),
                                        				':ctext' => $commentaire,
                                        				':cuser' => $username
                                				));
							}// / try

							catch(PDOException $e) {
                            					echo $e->getMessage();
                        				}

							$stmt->closeCursor();
			
						}// / if !isset error
					//} // /if decode success

					else {
						$error[] = 'Erreur anti-spam !';
					}
				
				}// fin if isset $_POST

				//check for any errors
        			if(isset($error)){
        				foreach($error as $error){
						echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                			}
        			}

				$stmt = $db->prepare('SELECT * FROM blog_posts_comments LEFT JOIN blog_members ON blog_members.username = blog_posts_comments.cuser WHERE cid_torrent = :cid_torrent ORDER BY cadded ASC');
        			$stmt->execute(array(':cid_torrent' => $row['postID']));
				$nbcomm = $stmt->rowCount();

				if ($nbcomm < 2) {
					echo '<p class="small mt-3">Il y a '.$nbcomm.' commentaire pour "'.html($row['postTitle']).'" </p><br><hr>';
				}
				else {
					echo '<p class="small mt-3">Il y a '.$nbcomm.' commentaires pour "'.html($row['postTitle']).'" </p><br><hr>';
				}

				while($comm = $stmt->fetch()) {
					
					echo '<div class="container">';
						echo '<div class="row">';
							echo '<div class="col-sm-12">';
								echo '<div class="inbox-message">';
									echo '<ul>';
										echo '<li>';
											echo '<div class="message-avatar">';
												if(!empty($comm['avatar'])) {
													echo '<img src="/images/avatars/'.$comm['avatar'].'" alt="">';
												}
												else {
													echo '<img src="/images/avatars/avatar.png" alt="">';
												}
											echo '</div>';
											echo '<div class="message-body">';
												echo '<div class="message-body-heading">';
													echo '<h6><a class="text-decoration-none text-dark" href="/profil.php?membre='.html($comm['cuser']).'">'.html($comm['cuser']).'</a></h6>';
													sscanf($comm['cadded'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
													echo '<span class="text-muted" style="font-size:11px;"><i class="fas fa-calendar-alt"></i> '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</span>';
												echo '</div>';
												echo '<p class="text-justify">';
													echo nl2br(bbcode(($comm['ctext'])));
													$cuser = $comm['cuser'];
													if($user->is_logged_in() && $_SESSION['username'] == $cuser) {
														echo '<p class="text-right">
															<a href="commentaire_supprimer.php?cid='.$comm['cid'].'&cid_torrent='.$comm['cid_torrent'].'" onclick="return confirm(\'Êtes-vous certain de vouloir supprimer ce commentaire ?\')">
																<i class="fas fa-trash-alt mr-2 alert alert-danger small" data-toggle="tooltip" title="Supprimer le commentaire"></i>
															</a>
														</p>';
													}
												echo '</p>';
											echo '</div>';
										echo '</li>';
									echo '</ul>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				
				} // /while

				//check for any errors
        			if(isset($error)) {
					if (is_array($error) || is_object($error)) {
        					foreach($error as $error){
							echo '<br /><div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                				}
        				}
				}
				?>
				</div>

<!-- EXEMPLE -->
<!--
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<div class="inbox-message">
				<ul>
					<li>
						<a href="#">
							<div class="message-avatar">
								<img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="">
							</div>
							<div class="message-body">
								<div class="message-body-heading">
									<h5>Daniel Dock</h5><span><small><i class="fas fa-calendar-alt"></i> 30-01-2020 à 15:24:45</small></span>
								</div>
								<p>Hello, Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolor....</p>
							</div>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
-->






			</div> <!-- //col-sm-9 -->

			<!-- sidebar -->
			<?php include_once 'includes/sidebar.php'; ?>

		</div> <!-- //row -->

	<!-- footer -->
	<?php include_once 'includes/footer.php'; ?>

	</div>

</div> <!-- //container -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html> 

<?php
include_once '../includes/config.php';

//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) { 
	header('Location: ../login.php?action=connecte');
	exit();
}

if(isset($_SESSION['userid'])) {
        if($_SESSION['userid'] != 1) {
                header('Location: ../login.php?action=pasledroit');
		exit();
        }
}

//Si le torrent est à supprimer ...
if(isset($_GET['delpost'])) {

        // 1 - on supprime le fichier .torrent dans le répertoire /torrents ...
        $stmt4 = $db->prepare('SELECT postID, postTorrent, postImage FROM blog_posts_seo WHERE postID = :postID') ;
        $stmt4->execute(array(
                ':postID' => $_GET['delpost']
        ));
        $efface = $stmt4->fetch();

        $file = $REP_TORRENTS.$efface['postTorrent'];
        if (file_exists($file)) {
                unlink($file);
        }

	// 2 - ... on supprime aussi l'image de présentation du torrent
	$postimage = $REP_IMAGES_TORRENTS.$efface['postImage'];
	if (file_exists($postimage)) {
                unlink($postimage);
        }

	// 3 - on supprime le torrent dans la base
        $stmt = $db->prepare('DELETE FROM blog_posts_seo WHERE postID = :postID') ;
        $stmt->execute(array(
		':postID' => $_GET['delpost']
	));

        // 4 - on supprime sa référence de catégorie
        $stmt1 = $db->prepare('DELETE FROM blog_post_cats WHERE postID = :postID');
        $stmt1->execute(array(
		':postID' => $_GET['delpost']
	));

        // 5 - on supprime sa référence de licence
        $stmt2 = $db->prepare('DELETE FROM blog_post_licences WHERE postID_BPL = :postID_BPL');
        $stmt2->execute(array(
                ':postID_BPL' => $_GET['delpost']
        ));

	// 6 - on supprime ses commentaires s'ils existent
	$stmt22 = $db->prepare('SELECT cid_torrent FROM blog_posts_comments WHERE cid_torrent = :cid_torrent');
	$stmt22->execute(array(
		':cid_torrent' => $_GET['delpost']
	));
	$commentaire = $stmt22->fetch();
	
	if(!empty($commentaire)) {
		$stmtsupcomm = $db->prepare('DELETE FROM blog_posts_comments WHERE cid_torrent = :cid_torrent');
		$stmtsupcomm->execute(array(
                	':cid_torrent' => $_GET['delpost']
        	));
	}

	// 7 - enfin, on met le flag à "1" pour supprimer le fichier dans la tables xbt_files
	$stmt3 = $db->prepare('UPDATE xbt_files SET flags = :flags WHERE fid = :fid') ;
        $stmt3->execute(array(
		':flags' => '1',
		':fid' => $_GET['delpost'] 
	));	

        header('Location: /index.php?action=supprime');
        exit;

}//fin de if isset $_GET['delpost']

$pagetitle = 'Admin : page générale';

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
        			//show message from add / edit page
        			if(isset($_GET['action']) && $_GET['action'] == 'supprime'){
                			echo '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Le torrent a été supprimé avec succès.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        			}

				if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">Le torrent a été ajouté avec succès.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        			}

				if(isset($_GET['message']) && $_GET['message'] == 'envoye') {
					echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">Message envoyé à tous les membres.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				}
        			?>

				<table class="table table-striped table-bordered table-hover table-sm small">
					<thead>
						<tr>
                					<th>Titre</th>
                					<th class="text-center">Date</th>
							<th class="text-center">Uploader</th>
                					<th class="text-center">Action</th>
        					</tr>
					</thead>
					<?php
                			try {
						$pages = new Paginator('10','p');
            					$stmt = $db->query('SELECT postID FROM blog_posts_seo');
            					//pass number of records to
            					$pages->set_total($stmt->rowCount());
                        			$stmt = $db->query('SELECT postID, postTitle, postAuthor, postDate FROM blog_posts_seo ORDER BY postID DESC '.$pages->get_limit());
						echo '<tbody>';
						while($row = $stmt->fetch()){
                                			echo '<tr>';
                                				echo '<td class="align-middle" style="width:60%;">'.$row['postTitle'].'</td>';
								sscanf($row['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
								echo '<td class="text-center small align-middle">'.$jour.'-'.$mois.'-'.$annee.'</td>';
								echo '<td class="text-center small align-middle"><a href="../profil.php?membre='.$row['postAuthor'].'">'.$row['postAuthor'].'</a></td>';
                                	?>
								<td class="text-center small align-middle">
									<a href="/admin/edit-post.php?id=<?php echo $row['postID'];?>" class="btn btn-primary btn-sm active small" role="button" aria-pressed="true"><i class="fas fa-edit small"></i></a>
									<a href="javascript:delpost('<?php echo $row['postID'];?>','<?php echo $row['postTitle'];?>')" class="btn btn-danger btn-sm active small" role="button" aria-pressed="true"><i class="fas fa-trash-alt small"></i></a>

									<!--
                                        				<a href="edit-post.php?id=<?php echo $row['postID'];?>"><input type="button" class="btn btn-sm alert-info" value="Edit."></a> | 
                                        				<a href="javascript:delpost('<?php echo $row['postID'];?>','<?php echo $row['postTitle'];?>')"><input type="button" class="btn btn-sm alert-danger" value="Supp."></a>
									-->
								</td>
                                	<?php
                                			echo '</tr>';
						}
						echo '</tbody>';
                			} 
					catch(PDOException $e) {
                    				echo $e->getMessage();
                			}
        				?>
        			</table>

				<p class="text-center">	
					<?php echo $pages->page_links('/admin/?'); ?>
				</p>	

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

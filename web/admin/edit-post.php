<?php
include_once '../includes/config.php';
try {
	$stmt = $db->prepare('SELECT * FROM blog_posts_seo WHERE postID = :postID');
	$stmt->execute(array(':postID' => $_GET['id']));
	$rowpost = $stmt->fetch();
}

catch(PDOException $e) {
	echo $e->getMessage();
}

//Si pas connecté : on renvoie sur page principale 
if(!$user->is_logged_in()) {
        header('Location: ../login.php');
}

// si c'est l'auteur du post ou si c'est l'admin, on donne les droits d'édition
if(isset($_SESSION['username']) && isset($_SESSION['userid'])) {

	if(($rowpost['postAuthor'] == $_SESSION['username']) || ($_SESSION['userid'] == 1)) {

		//Si on supprime l'icone de présentation
		if(isset($_GET['delimage'])) {
			$delimage = $_GET['delimage'];
			//on supprime le fichier image
			$stmt = $db->prepare('SELECT postImage FROM blog_posts_seo WHERE postID = :postID');
			$stmt->execute(array(
				':postID' => $delimage
			));
			$sup = $stmt->fetch();
			$file = $REP_IMAGES_TORRENTS.$sup['postImage']; 
			if (file_exists($file)) {
				unlink($file);
			}
			//puis on supprime l'image dans la base
			$stmt = $db->prepare('UPDATE blog_posts_seo SET postImage = NULL WHERE postID = :postID');
			$stmt->execute(array(
                		':postID' => $delimage
        		));
			header('Location: /admin/edit-post.php?id='.$delimage);
		}

		// titre de la page
		$pagetitle = 'Admin : édition du torrent : '.$rowpost['postTitle'];

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

				<h4>Edition du post : <?php echo $rowpost['postTitle']; ?></h4>

				<?php
				$id = $_GET['id'];

				//if form has been submitted process it
				if(isset($_POST['submit'])) {
					if(isset($_FILES['icontorr']['name']) && !empty($_FILES['icontorr']['name'])) {
						// *****************************************
						// upload icone de présentation du torrent
						// *****************************************
						$target_dir = $REP_IMAGES_TORRENTS;
						$target_file = $target_dir . basename($_FILES["icontorr"]["name"]);
						$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

						//L'erreur N°4 indique qu'il n'y a pas de fichier. On l'exclut car l'icone de présentation du torrent n'est pas obligatoire.
						if ($_FILES['icontorr']['error'] > 0 && $_FILES['icontorr']['error'] != 4) {
							//if ($_FILES['icontorr']['error'] > 0) {
							$error[] = 'Erreur lors du transfert de l\'icone de présentation du torrent.';
						}

						// On cherche si l'image n'existe pas déjà sous ce même nom
						if (file_exists($target_file)) {
							$error[] = 'Désolé, cette image existe déjà. Veillez en choisir une autre ou tout simplement changer son nom.';
						}

						// taille de l'image
						if ($_FILES['icontorr']['size'] > $MAX_SIZE_ICON) {
							$error[] = 'Image trop grosse. Taille maxi : '.makesize($MAX_SIZE_ICON);
						}
	
						// format de l'image	
						if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    							$error[] = 'Désolé : seuls les fichiers .jpg, .png, .jpeg sont autorisés !';
						} 
	
						// poids de l'image	
						$image_sizes = getimagesize($_FILES['icontorr']['tmp_name']);
						if ($image_sizes[0] > $WIDTH_MAX_ICON OR $image_sizes[1] > $HEIGHT_MAX_ICON) {
							$error[] = 'Image trop grande : '.$WIDTH_MAX_ICON.' x '.$HEIGHT_MAX_ICON.' maxi !';
						}
		
						// on vérifie que c'est bien une image
						if($image_sizes == false) {
							$error[] = 'Le fichier envoyé n\'est pas une image !';
						}

						// on upload l'image s'il n'y a pas d'erreur
						if(!isset($error)) {
							if(!move_uploaded_file($_FILES['icontorr']['tmp_name'], $REP_IMAGES_TORRENTS.$_FILES['icontorr']['name'])) {
								$error[] = 'Problème de téléchargement de l\'icone de présentation du torrent.';
							}
						}
					}//fin de if(isset($_FILES['icontorr']['name']))

					extract($_POST);
			                //very basic validation
                			if($postID == ''){
                        			$error[] = 'Ce post possède un ID invalide !';
                			}
			                if($postTitle == ''){
                        			$error[] = 'Veuillez entrer un titre.';
               	 			}
					if($postLink == ''){
                        			$error[] = 'Veuillez entrer une URL pour le média.';
                			}
			                if($postDesc == ''){
                        			$error[] = 'Veuillez entrer une courte description.';
                			}
			                if($postCont == ''){
                        			$error[] = 'Veuillez entrer un contenu.';
                			}
					if($catID == '') {
						$error[] = 'Veuillez sélectionner au moins une catégorie.';
					}
					if($licenceID == '') {
                        			$error[] = 'Veuillez sélectionner au moins une licence.';
                			}

					if(!isset($error)){
                        			try {
							$postSlug = slug($postTitle);
							// Si on a une nouvelle image, on met tout à jour, même l'image de présentation
							if(isset($_FILES['icontorr']['name']) && !empty($_FILES['icontorr']['name'])) {
								//insert into database
                        				        $stmt = $db->prepare('UPDATE blog_posts_seo SET postTitle = :postTitle, postSlug = :postSlug, postLink = :postLink, postDesc = :postDesc, postCont = :postCont, postImage = :postImage WHERE postID = :postID') ;
                                				$stmt->execute(array(
                                        				':postTitle' => $postTitle,
                                        				':postSlug' => $postSlug,
									':postLink' => $postLink,
                                        				':postDesc' => $postDesc,
                                        				':postCont' => $postCont,
									':postImage' => $_FILES['icontorr']['name'],
                                        				':postID' => $_GET['id']
                                				));
							}

							else { // sinon on met tout à jour SAUF l'icone de présentation
								//insert into database
				                                $stmt = $db->prepare('UPDATE blog_posts_seo SET postTitle = :postTitle, postSlug = :postSlug, postLink = :postLink, postDesc = :postDesc, postCont = :postCont WHERE postID = :postID') ;
                                				$stmt->execute(array(
                                        				':postTitle' => $postTitle,
                                        				':postSlug' => $postSlug,
									':postLink' => $postLink,
                                        				':postDesc' => $postDesc,
                                        				':postCont' => $postCont,
                                        				':postID' => $_GET['id']
                                				));
							}

							//delete all items with the current postID in categories
                                			$stmt = $db->prepare('DELETE FROM blog_post_cats WHERE postID = :postID');
                                			$stmt->execute(array(':postID' => $postID));
			                                if(is_array($catID)){
                        			                foreach($_POST['catID'] as $catID){
                                                			$stmt = $db->prepare('INSERT INTO blog_post_cats (postID,catID)VALUES(:postID,:catID)');
                                                			$stmt->execute(array(
                                                        			':postID' => $postID,
                                                        			':catID' => $catID
                                                			));
                                        			}
                                			}

							//delete all items with the current postID in licences
                                			$stmt = $db->prepare('DELETE FROM blog_post_licences WHERE postID_BPL = :postID_BPL');
                                			$stmt->execute(array(':postID_BPL' => $postID));
			                                if(is_array($licenceID)){
                        			                foreach($_POST['licenceID'] as $licenceID){
                                                			$stmt = $db->prepare('INSERT INTO blog_post_licences (postID_BPL,licenceID_BPL) VALUES (:postID_BPL,:licenceID_BPL)');
                                                			$stmt->execute(array(
                                                        			':postID_BPL' => $postID,
                                                        			':licenceID_BPL' => $licenceID
                                                			));
                                        			}
                                			}

							//redirect to index page
							header('Location: ../torrents.php');
                                			exit;
                        			} // fin de try

						catch(PDOException $e) {
                            				echo $e->getMessage();
                        			}
                			} // fin de if(!isset($error))
        			} // fin if(isset($_POST['submit']))

				//check for any errors
        			if(isset($error)){
                			foreach($error as $error){
                        			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                			}
        			}

               			try {
		                        $stmt = $db->prepare('SELECT postID, postTitle, postLink, postDesc, postCont, postImage FROM blog_posts_seo WHERE postID = :postID') ;
                		        $stmt->execute(array(
						':postID' => $id
					));
		                        $row = $stmt->fetch();
	                	}
				catch(PDOException $e) {
                		    echo $e->getMessage();
                		}
        			?>

				<form class="form-group py-2 px-2" action="" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col">
					                <input type="hidden" name="postID" value="<?php echo html($row['postID']);?>">
                					<label for="postTitle">Titre</label>
                					<input class="form-control" type="text" name="postTitle" value="<?php echo html($row['postTitle']);?>" required>
							<br>
							<label for="postLink">URL : lien web du média</label>
                					<input class="form-control" type="text" name="postLink" value="<?php echo html($row['postLink']);?>" required>
                					<br>
							<label for="postDesc">Description</label>
                					<textarea class="form-control" name="postDesc" rows="7" required><?php echo html($row['postDesc']); ?></textarea>
                					<br>
							<label for="postCont">Contenu</label>
                					<textarea class="form-control" id="tiny" name="postCont" rows="15" required><?php echo html($row['postCont']); ?></textarea>
							<script>
    								tinymce.init({
    								selector: 'textarea#tiny',
    								width: 768,
								menubar: false,
								image_dimensions: false,
								image_class_list: [
									{title: 'Responsive', value: 'img-fluid'}
								],
    								plugins: [
        								"advlist autolink lists link image charmap preview anchor",
        								"searchreplace visualblocks code fullscreen",
        								"insertdatetime table contextmenu paste"
    								],
    								toolbar: "insertfile undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    								});
							</script>
							<br>
							<label for="icontorr">Icone de présentation (JPG ou PNG, <?php echo $WIDTH_MAX_ICON; ?> x <?php echo $HEIGHT_MAX_ICON; ?>, <?php echo makesize($MAX_SIZE_ICON); ?> max.)</label>
                					<input class="form-control" type="file" name="icontorr">
							<br><br>
							Icone de présentation :
							<?php
							if(!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
								echo '<img class="img-thumbnail" style="max-width: 150px;" src="../images/imgtorrents/'.html($row['postImage']).'" alt="Icone de présentation de '.html($row['postTitle']).'" />';	
							?>
								<a href="javascript:delimage('<?php echo html($row['postID']);?>','<?php echo html($row['postImage']);?>')">Supprimer</a>
							<?php
							}
							else {
								echo '<img class="img-thumbnail" style="max-width: 150px;" src="../images/noimage.png" alt="Pas d\'icone de présentation pour '.html($row['postTitle']).'" />';
							}
							?>
							<br><br>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
                        				<span class="font-weight-bolder">Catégories</span><br>
                        				<?php
                        				$stmt2 = $db->query('SELECT catID, catTitle FROM blog_cats ORDER BY catTitle');
                        				while($row2 = $stmt2->fetch()){
				                                $stmt3 = $db->prepare('SELECT catID FROM blog_post_cats WHERE catID = :catID AND postID = :postID') ;
                                				$stmt3->execute(array(':catID' => $row2['catID'], ':postID' => $row['postID']));
                               		 			$row3 = $stmt3->fetch();

                                				if($row3['catID'] == $row2['catID']){
                                        				$checked = 'checked=checked';
                                				} 
								else {
                                        				$checked = null;
                                				}
								echo "<input type='checkbox' style='float:left; width:auto;' name='catID[]' value='".$row2['catID']."' $checked> ".$row2['catTitle']."<br>";
                        				}
							$stmt2->closeCursor();
                       	 				?>
						</div>
						<div class="col-sm-6">
                        				<span class="font-weight-bolder">Licences</span><br>
                        				<?php
                        				$stmt2 = $db->query('SELECT licenceID, licenceTitle FROM blog_licences ORDER BY licenceTitle');
                        				while($row2 = $stmt2->fetch()) {
				                                $stmt3 = $db->prepare('SELECT licenceID_BPL FROM blog_post_licences WHERE licenceID_BPL = :licenceID_BPL AND postID_BPL = :postID_BPL') ;
                                				$stmt3->execute(array(
									':licenceID_BPL' => $row2['licenceID'], 
									':postID_BPL' => $row['postID']
								));
                                				$row3 = $stmt3->fetch();
				                                if($row3['licenceID_BPL'] == $row2['licenceID']){
                                				        $checked = 'checked=checked';
                                				} 
								else {
                                        				$checked = null;
                                				}
                            					echo "<input type='checkbox' style='float:left; width:auto;' name='licenceID[]' value='".$row2['licenceID']."' $checked> ".$row2['licenceTitle']."<br>";
                        				}
                        				?>
						</div>
					</div>

                			<br>
					<p class="text-right">
						<button class="btn btn-primary mb-2 mt-3" type="submit" name="submit">Envoyer</button>
                        			<button class="btn btn-primary ml-3 mb-2 mt-3" type="reset">Annuler</button>
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

<?php
	}
}
else {
	header('Location: ../');
	exit;
}
?>

<?php
include_once 'includes/config.php';

//Si pas connecté pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: /login.php?action=connecte');
}

$pagetitle = 'Ajouter un torrent';
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
				//Si le formulaire a été soumis = GO !
				if(isset($_POST['submit'])) {
				
					$image_torrent = $_FILES['imagetorrent']['name'];

					//si erreur de transfert
					if ($_FILES['imagetorrent']['error'] > 0) {
						$error[] = "Erreur lors du transfert";
					}

					// taille de l'image
					if ($_FILES['imagetorrent']['size'] > MAX_FILE_SIZE) {
						$error[] = "L'image est trop grosse.";
					}

					//$extensions_valides = array( 'jpg' , 'png' );
					//1. strrchr renvoie l'extension avec le point (« . »).
					//2. substr(chaine,1) ignore le premier caractère de chaine.
					//3. strtolower met l'extension en minuscules.
					$extension_upload = strtolower(substr(strrchr($_FILES['imagetorrent']['name'], '.')  ,1)  );

					if(!in_array($extension_upload,$EXTENSIONS_VALIDES)) {
						$error[] = "Extension d'image incorrecte (.png ou .jpg seulement !)";
					}

					$image_sizes = getimagesize($_FILES['imagetorrent']['tmp_name']);
					if ($image_sizes[0] > $WIDTH_MAX OR $image_sizes[1] > $HEIGHT_MAX) {
						$error[] = "Image trop grande (dimensions)";
					}

        				// On cherche si l'image n'existe pas déjà sous ce même nom
					$target_dir = $REP_IMAGES_TORRENTS;
					$target_file = $target_dir . basename($_FILES["imagetorrent"]["name"]);
					if (file_exists($target_file)) {
						$error[] = 'Désolé, cette image existe déjà. Veillez en choisir une autre ou tout simplement la renommer.';
					}	

					// si il y a bien un fichier .torrent, on poursuit ...
					if (!isset($_FILES["torrent"]) && empty($torrent)) {
        					$error[] = 'Veuillez choisir un fichier .torrent';
					}

					else {
						//Collecte des données ...
                				extract($_POST);
						$type_file = $_FILES['torrent']['type'];
                				$tmp_file = $_FILES['torrent']['tmp_name'];
                				$name_file = $_FILES['torrent']['name'];

						$fd = fopen($_FILES["torrent"]["tmp_name"], "rb");
			
						$length=filesize($_FILES["torrent"]["tmp_name"]);
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
							// multifiles torrent
							$size=0;
							foreach ($upfile["files"] as $file) {
								$size+=(float)($file["length"]);
                					}
						}
						else {
							$size = "0";
						}

						$announce=trim($array["announce"]);

						// on vérifie si le torrent existe dja : on compare les champs info_hash
						//$stmt = $db->query("SELECT * FROM xbt_files WHERE LOWER(hex('info_hash')) = '".$hash."'");
						$stmt = $db->query("SELECT * FROM xbt_files WHERE info_hash = 0x$hash");
						$exists = $stmt->fetch();
						if(!empty($exists)) {
        						$error[] = "Ce torrent existe dans la base.";
						}

						// on vérifie l'url d'announce
						if($array['announce'] != $ANNOUNCEURL) {
        						$error[] = 'Vous n\'avez pas fournit la bonne adresse d\'announce dans votre torrent : l\'url d\'announce doit etre '.$ANNOUNCEURL;
						}

						// si le nom du torrent n'a pas été fournit (facultatif), on récupère le nom public du fichier
    						if (empty($_POST['postTitle'])) {
    							// on calcule le nom du fichier SANS .torrent a la fin
    							$file = $_FILES['torrent']['name'];
    							$var = explode(".",$file);
    							$nb = count($var)-1;
    							$postTitle = substr($file, 0, strlen($file)-strlen($var[$nb])-1);
    						}
    						else {
    							// sinon on prend le nom fournit dans le formulaire d'upload
    							$postTitle = $_POST['postTitle'];
    						}

						// on vérifie la taille du fichier .torrent
						if ($_FILES['torrent']['size'] > $MAX_FILE_SIZE){
							$error[] = 'Le fichier .torrent est trop gros. Etes-vous certain qu\'il s\'agisse d\'un fichier .torrent ?';
						}

						if($_POST['postLink'] == ''){
                        				$error[] = 'Veuillez entrer un lien web pour le média proposé.';
                				}

                				if($_POST['postDesc'] == ''){
                       					$error[] = 'Veuillez entrer une courte description.';
                				}

                				if($_POST['postCont'] == ''){
                       					$error[] = 'Veuillez entrer un contenu.';
                				}

						if($_POST['catID'] == ''){
                       					$error[] = 'Veuillez choisir une catégorie.';
                				}

						if($_POST['licenceID'] == ''){
                       					$error[] = 'Veuillez choisir une licence.';
                				}

					}// fin if (isset($_FILES["torrent"]))

					// s'il n'y a pas d'erreur on y va !!!
					if(!isset($error)) {
						// on upload l'image
						if(!isset($error)) {
							if(!move_uploaded_file($_FILES['imagetorrent']['tmp_name'], $REP_IMAGES_TORRENTS.$_FILES['imagetorrent']['name'])) {
								$error[] = 'Problème de téléchargement de l\'image.';
							}
						}

						// on upload le fichier .torrent
						if(!move_uploaded_file($_FILES['torrent']['tmp_name'], $REP_TORRENTS.$_FILES['torrent']['name'])) {
							$error[] = 'Problème lors de l\'upload du fichier .torrent';
						}

						 try {
                                			$postSlug = slug($postTitle);
                                			$postAuthor = html($_SESSION['username']);

                                			//On insert les données dans la table blog_posts_seo
                                			$stmt = $db->prepare('INSERT INTO blog_posts_seo (postHash,postTitle,postAuthor,postSlug,postLink,postDesc,postCont,postTaille,postDate,postTorrent,postImage) VALUES (:postHash, :postTitle, :postAuthor, :postSlug, :postLink, :postDesc, :postCont, :postTaille, :postDate, :postTorrent, :postImage)') ;
                               	 			$stmt->execute(array(
								':postHash' => $hash,
                                        			':postTitle' => $postTitle,
                                        			':postAuthor' => $postAuthor,
                                        			':postSlug' => $postSlug,
								':postLink' => $postLink,
                                        			':postDesc' => $postDesc,
                                        			':postCont' => $postCont,
								':postTaille' => $size,
                                        			':postDate' => date('Y-m-d H:i:s'),
								':postTorrent' => $name_file,
								':postImage' => $image_torrent
                                			));

                                			$postID = $db->lastInsertId();

							//On insert les données dans la table xbt_files également
							$stmt2 = $db->query("INSERT INTO xbt_files SET info_hash=0x$hash, ctime=UNIX_TIMESTAMP() ON DUPLICATE KEY UPDATE flags=0");

							//On ajoute les données dans la table categories
                                			if(is_array($catID)){
                                        			foreach($_POST['catID'] as $catID){
                                                			$stmt = $db->prepare('INSERT INTO blog_post_cats (postID,catID)VALUES(:postID,:catID)');
                                                			$stmt->execute(array(
                                                        			':postID' => $postID,
                                                        			':catID' => $catID
                                                			));
                                        			}
                                			}

                                			//On ajoute les données dans la table licences
                                			if(is_array($licenceID)){
                                        			foreach($_POST['licenceID'] as $licenceID){
                                                			$stmt = $db->prepare('INSERT INTO blog_post_licences (postID_BPL,licenceID_BPL)VALUES(:postID_BPL,:licenceID_BPL)');
                                                			$stmt->execute(array(
                                                        			':postID_BPL' => $postID,
                                                        			':licenceID_BPL' => $licenceID
                                                			));
                                        			}
                                			}

                                			//On redirige vers la page torrents pour tout ajout de torrent
                                			header('Location: '.SITEURL.'/torrents.php?action=ajoute');
                                			exit;

						} 
						catch(PDOException $e) {
                            				echo $e->getMessage();
                        			}
                			}
        			}

				//S'il y a des erreurs, on les affiche
        			if(isset($error)){
                			foreach($error as $error){
                        			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">ERREUR : '.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                			}
        			}
        			?>	

				<!-- DEBUT du formulaire -->
				<div class="alert alert-info text-center"><h4>Ajouter un torrent</h4></div>
				<p class="lead text-center">URL d'annonce : <?php echo $ANNOUNCEURL; ?></p>
				<p class="alert alert-warning text-center small rounded"><span class="fas fa-warning"></span> Tous les champs sont obligatoires, sauf le titre</p>
				<br>

			<div class="container bg-light py-3 px-3 small">
				<form class="form-group" action="" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col">
							<div class="row">
								<div class="col-sm-12 form-group">
									<label for="torrent">
										<span class="font-weight-bolder">Fichier .torrent :</span>
		    								<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
		    								<input type="file" name="torrent" required>
									</label>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12 form-group">
									<label for="postTitle">
										<span class="font-weight-bolder">Titre (facultatif) :</span><br>
                								<input type="text" name="postTitle" style="width:770px;" value="<?php if(isset($error)) { echo html($_POST['postTitle']); } ?>">
									</label>
								</div>
							</div>
							<div class="row">
                                                                <div class="col-sm-6 form-group">
									<label for="postLink">
										<span class="font-weight-bolder">Lien web du projet, de l'oeuvre, ... (URL) :</span>
										<input type="text" name="postLink" style="width:770px;" value="<?php if(isset($error)) { echo html($_POST['postLink']); } ?>" required>
									</label>
								</div>
							</div>
							<div class="row">
                                                                <div class="col-sm-12 form-group">
									<label for="postDesc">
										<span class="font-weight-bolder">Courte description<br>(résumé de quelques lignes, <u>sans image</u>, qui servira de présentation sur la page d'accueil) :</span>
										<br><textarea class="form-control" name="postDesc" rows="5" cols="100" required><?php if(isset($error)) { echo html($_POST['postDesc']); } ?></textarea>
									</label>
								</div>
							</div>
							<div class="row">
                                                                <div class="col-sm-12 form-group">
									<label for="postCont">
										<span class="font-weight-bolder">Contenu<br>(Détails, images, etc.) :</span>
										<textarea class="form-control" id="tiny" name="postCont" rows="12" cols="100"><?php if(isset($error)) { echo html($_POST['postCont']); } ?></textarea>
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
									</label>
								</div>
							</div>
							<div class="row">
                                                                <div class="col-sm-12 form-group">
									<label for="imagetorrent">
										<span class="font-weight-bolder">Image d'illustration (page accueil et article) :</span><br>
										<span class="small">PNG ou JPG seulement | max. <?php echo makesize($MAX_SIZE_ICON); ?> | max. <?php echo $WIDTH_MAX_ICON; ?>px X <?php echo $HEIGHT_MAX_ICON; ?>px</span><br>
               									<input type="file" class="form-control btn-sm" name="imagetorrent" style="width:350px;" required>
									</label>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6 form-group">
									<span class="font-weight-bolder">Catégories :</span>
									<p class="small">Cochez une ou plusieurs catégories pour le torrent</p>
                							<?php
                							$stmt2 = $db->query('SELECT catID, catTitle FROM blog_cats ORDER BY catTitle');
									while($catrow = $stmt2->fetch()){
										echo '<div class="form-check">';
                									echo '<input class="form-check-input" type="checkbox" name="catID[]" value="'.$catrow['catID'].'">&nbsp;';
											echo '<label class="form-check-label" for="catID[]">'.$catrow['catTitle'].'</label><br>';
										echo '</div>';
                							}
                							?>
								</div>
								<div class="col-sm-6 form-group">
									<span class="font-weight-bolder">Licences :</span>
									<p class="small">Cochez une ou plusieurs licences pour le torrent</p>
                							<?php
                							$stmt3 = $db->query('SELECT licenceID, licenceTitle FROM blog_licences ORDER BY licenceTitle');
									while($licrow = $stmt3->fetch()){
										echo '<div class="form-check">';
                									echo '<input class="form-check-input" type="checkbox" name="licenceID[]" value="'.$licrow['licenceID'].'">&nbsp;';
											echo '<label class="form-check-label" for="licenceID[]">'.$licrow['licenceTitle'].'</label><br>';
										echo '</div>';
                							}
                							?>
								</div>
							</div>
           						<div class="row">	
								<div class="col-sm-12 form-group text-right">
									<button class="btn btn-primary mt-3" name="submit" type="submit">Envoyer</button>
									<button class="btn btn-secondary ml-3 mt-3" type="reset">Annuler</button>
								</div>
								<!--
		    						<input type="submit" class="button small orange" name="submit" value="Ajouter le torrent">
		    						&nbsp;
								<input type="reset" value="Annuler" class="button small grey">
								-->
							</div>
						</div>
					</div>
				</form>
			</div>
				<!-- FIN du formulaire -->


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

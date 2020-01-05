<?php
require_once 'includes/config.php';

//Si pas connecté pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: /login.php?action=connecte');
}

// titre de la page
$pagetitle= 'Ajouter un torrent';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<div class="first">
	<!-- ### -->

	<?php
	//Si le formulaire a été soumis = GO !
	if(isset($_POST['submit'])) {

		//Collecte des données ...
		//extract($_POST);

		// *****************************************
		// upload image torrent
		// *****************************************

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
		$extension_upload = strtolower(  substr(  strrchr($_FILES['imagetorrent']['name'], '.')  ,1)  );

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

		// on upload l'image
		/*
		if(!isset($error)) {
			if(!move_uploaded_file($_FILES['imagetorrent']['tmp_name'], $REP_IMAGES_TORRENTS.$_FILES['imagetorrent']['name'])) {
				$error[] = 'Problème de téléchargement de l\'image.';
			}
		}
		*/


		// ***************************************
		// fin image torrent upload
		// ***************************************

		// ***************************************
		// upload fichier torrent
		// ***************************************

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

			// On vérifie que l'url d'announce n'est pas vide
			//if(empty($array['announce'])) {
			//	$error[] = 'L\'url d\'announce est vide !';
			//}

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

			/*
			if(!strstr($type_file, 'torrent')){
        			$error[] = 'Le fichier n\'est pas un fichier .torrent !';
    			}	
			*/

			/*
                	if($postTitle ==''){
                       		$error[] = 'Veuillez entrer un titre.';
                	}
			*/

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


		// ---------------------------------------------------------
		// Upload fichier torrent + image
		// ---------------------------------------------------------

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

				//write_log('<span class="orange bold">Upload torrent : '.$postTitle.' par '.$postAuthor, $db .'</span>');

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

                        } catch(PDOException $e) {
                            echo $e->getMessage();
                        }

                }

        }

        //S'il y a des erreurs, on les affiche
        if(isset($error)){
                foreach($error as $error){
                        echo '<div class="alert-msg rnd8 error">ERREUR : '.$error.'</div>';
                }
        }
        ?>	


	<!-- DEBUT du formulaire -->

	<h2>Ajouter un torrent</h2>
	<p class="font-medium bold">URL d'annonce : <?php echo $ANNOUNCEURL; ?></p>
	<div class="alert-msg rnd8 warning"><span class="fa fa-warning font-large"></span> Tous les champs sont obligatoires, sauf le titre <a class="close" href="#">X</a></div>

	<br>
        <form class="rnd5" action="" method="post" enctype="multipart/form-data">
	    <div class="form-input clear">
		<label for="torrent"><span class="font-medium bold">Fichier .torrent :</span>
		    <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
		    <input type="file" name="torrent" style="width:350px;">
		</label>
		<br>

		<label for="postTitle"><span class="font-medium bold">Titre (facultatif) :</span>
                	<input type="text" size="50" name="postTitle" value="<?php if(isset($error)) { echo html($_POST['postTitle']); } ?>">
		</label>
		<br>

		<label for="postLink"><span class="font-medium bold">Lien web du projet, de l'oeuvre, ... (URL) :</span>
			<input type="text" size="50" name="postLink" value="<?php if(isset($error)) { echo html($_POST['postLink']); } ?>">
		</label>
		<br>

                <label for="postDesc"><span class="font-medium bold">Courte description (Résumé de quelques lignes, sans image) :</span>
			<br><span class="font-tiny">Vous pouvez éventuellement utiliser quelques éléments de mise en page BBCode : [b][/b], [i][/i], [u][/u], [url][/url]</span>
			<textarea name="postDesc" rows="10"><?php if(isset($error)) { echo html($_POST['postDesc']); } ?></textarea>
		</label>
		<br>

                <label for="postCont"><span class="font-medium bold">Contenu (Détails, images, etc.) :</span>
			<textarea id="editor" name="postCont" rows="20"><?php if(isset($error)) { echo html($_POST['postCont']); } ?></textarea>
		</label>
		<br>

		<label for="imagetorrent"><span class="font-medium bold">Image d'illustration (page accueil et article) :</span>
			<span class="font-tiny">PNG ou JPG seulement | max. <?php echo makesize($MAX_SIZE_ICON); ?> | max. <?php echo $WIDTH_MAX_ICON; ?>px X <?php echo $HEIGHT_MAX_ICON; ?>px</span><br>
               		<input type="file" name="imagetorrent" style="width:350px;">
		</label>
		<br><br>

		<div class="one_half first">
                	<span class="font-medium bold">Catégories :</span><br>
                			<?php
                			$stmt2 = $db->query('SELECT catID, catTitle FROM blog_cats ORDER BY catTitle');
                			while($catrow = $stmt2->fetch()){
                				echo '<input style="float:left; width:auto;" type="checkbox" name="catID[]" value="'.$catrow['catID'].'">&nbsp;';
						echo '<label for="catID[]">'.$catrow['catTitle'].'</label><br>';
                			}
                			?>
		</div>

		<div class="one_half">
               		<span class="font-medium bold">Licences :</span><br>
                			<?php
                			$stmt3 = $db->query('SELECT licenceID, licenceTitle FROM blog_licences ORDER BY licenceTitle');
                			while($licrow = $stmt3->fetch()){
                				echo '<input style="float:left; width:auto;" type="checkbox" name="licenceID[]" value="'.$licrow['licenceID'].'">&nbsp;';
						echo '<label for="licenceID[]">'.$licrow['licenceTitle'].'</label><br>';
                			}
                			?>
		</div>


	   </div>

           <br><br><br>
		<div class="fl_right">
		    <input type="submit" class="button small orange" name="submit" value="Ajouter le torrent">
		    &nbsp;
		    <input type="reset" value="Annuler" class="button small grey">
		</div>
        </form>
	<br>

	<!-- FIN du formulaire -->


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

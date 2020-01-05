<?php
require_once '../includes/config.php';

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

//Si pas l'auteur du post : on renvoie sur page principale 
//if($rowpost['postAuthor'] != $_SESSION['username']) {
//	header('Location: '.SITEURL);
//}


// -----------------------------------------------------------------------------------------------
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
include_once '../includes/header-logo.php';
include_once '../includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<?php include_once('menu.php'); ?>

	<div class="first">
	<!-- ### -->

	<h2>Edition du post : <?php echo $rowpost['postTitle']; ?></h2>


<?php

$id = $_GET['id'];

//if form has been submitted process it
if(isset($_POST['submit'])) {

	//collect form data
	//extract($_POST);

	if(isset($_FILES['icontorr']['name']) && !empty($_FILES['icontorr']['name'])) {
	//if(isset($_POST['icontorr']) && !empty($_POST['icontorr'])) {

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
			$error[] = 'L\'image envoyée n\'est pas une image !';
		}

		// on upload l'image s'il n'y a pas d'erreur
		if(!isset($error)) {
			if(!move_uploaded_file($_FILES['icontorr']['tmp_name'], $REP_IMAGES_TORRENTS.$_FILES['icontorr']['name'])) {
				$error[] = 'Problème de téléchargement de l\'icone de présentation du torrent.';
			}
		}

		}//fin de if(isset($_FILES['icontorr']['name']))

		// ***************************************
		// fin upload icone de présentation du torrent
		// ***************************************

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
                        echo '<div class="alert-msg error rnd8">'.$error.'</div>';
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

        <form action="" method="post" enctype="multipart/form-data">
		<div class="form-input clear">
                <input type="hidden" name="postID" value="<?php echo html($row['postID']);?>">
                <label for="postTitle">Titre
                	<input type="text" name="postTitle" value="<?php echo html($row['postTitle']);?>">
		</label>
		<br><label for="postLink">URL : lien web du média
                	<input type="text" name="postLink" value="<?php echo html($row['postLink']);?>">
		</label>
                <br><label for="postDesc">Description
                	<textarea name="postDesc" rows="15"><?php echo html($row['postDesc']); ?></textarea>
		</label>
                <br><label for="postCont">Contenu
                	<textarea id="editor" name="postCont" rows="40"><?php echo html($row['postCont']); ?></textarea>
		</label>
		<br><label for="icontorr">Icone de présentation (JPG ou PNG, <?php echo $WIDTH_MAX_ICON; ?> x <?php echo $HEIGHT_MAX_ICON; ?>, <?php echo makesize($MAX_SIZE_ICON); ?> max.)
                	<input type="file" name="icontorr">
		<br><br>Icone de présentation :
		<?php
		if(!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
			echo '<img style="max-width: 150px;" src="../images/imgtorrents/'.html($row['postImage']).'" alt="Icone de présentation de '.html($row['postTitle']).'" />';	
		?>
		<a href="javascript:delimage('<?php echo html($row['postID']);?>','<?php echo html($row['postImage']);?>')">Supprimer</a>
		<?php
		}
		else {
			echo '<img style="max-width: 150px;" src="../images/noimage.png" alt="Pas d\'icone de présentation pour '.html($row['postTitle']).'" />';
		}
		?>
		</label>
		<br><br>

			<div class="one_half first">
                        <span class="bold font-medium">Catégories</span><br>
                        <?php
                        $stmt2 = $db->query('SELECT catID, catTitle FROM blog_cats ORDER BY catTitle');
                        while($row2 = $stmt2->fetch()){

                                $stmt3 = $db->prepare('SELECT catID FROM blog_post_cats WHERE catID = :catID AND postID = :postID') ;
                                $stmt3->execute(array(':catID' => $row2['catID'], ':postID' => $row['postID']));
                                $row3 = $stmt3->fetch();

                                if($row3['catID'] == $row2['catID']){
                                        $checked = 'checked=checked';
                                } else {
                                        $checked = null;
                                }

                            echo "<input type='checkbox' style='float:left; width:auto;' name='catID[]' value='".$row2['catID']."' $checked> ".$row2['catTitle']."<br>";
                        }

			$stmt2->closeCursor();
                        ?>
			</div>
			
			<div class="one_half">
                        <span class="font-medium bold">Licences</span><br>
                        <?php
                        $stmt2 = $db->query('SELECT licenceID, licenceTitle FROM blog_licences ORDER BY licenceTitle');
                        while($row2 = $stmt2->fetch()) {

                                $stmt3 = $db->prepare('SELECT licenceID_BPL FROM blog_post_licences WHERE licenceID_BPL = :licenceID_BPL AND postID_BPL = :postID_BPL') ;
                                $stmt3->execute(array(
					':licenceID_BPL' => $row2['licenceID'], 
					':postID_BPL' => $row['postID']));
                                $row3 = $stmt3->fetch();

                                if($row3['licenceID_BPL'] == $row2['licenceID']){
                                        $checked = 'checked=checked';
                                } else {
                                        $checked = null;
                                }
                            echo "<input type='checkbox' style='float:left; width:auto;' name='licenceID[]' value='".$row2['licenceID']."' $checked> ".$row2['licenceTitle']."<br>";
                        }
                        ?>
			</div>
	
		</div>

                <br><p class="right">
		<input type='submit' class="button small orange" name='submit' value='Mettre à jour'>
		&nbsp;
		<input type="reset" class="button small grey" value="Annuler">
		</p>
        </form>



        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';

}
}
else {
  header('Location: ../');

}

?>

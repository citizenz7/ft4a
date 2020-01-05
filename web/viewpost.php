<?php
require_once 'includes/config.php';

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

	<?php 
	$stmt = $db->prepare('SELECT postID,postHash,postTitle,postSlug,postAuthor,postLink,postDesc,postCont,postTaille,postDate,postTorrent,postImage FROM blog_posts_seo WHERE postSlug = :postSlug');
	$stmt->bindValue(':postSlug', $id, PDO::PARAM_STR);
	$stmt->execute();
	$row = $stmt->fetch();

	echo '<h2>'.html($row['postTitle']).'</h2>';

	echo '<div class="one_third first push50" style="margin-right:20px;">';
	echo '<span class="bold font-large">Télécharger : <a href="download.php?id='.html($row['postID']).'"><span class="font-medium fa fa-download"></span></a></span><br>';
	echo 'Posté le</span> : <span class="font-tiny">'.date_fr('d-m-Y à H:i:s', strtotime($row['postDate'])).'</span><br>';
	echo 'Par : <span class="font-tiny"><a href="profil.php?membre='.html($row['postAuthor']).'">'.html($row['postAuthor']).'</a></span><br>';
	echo 'Dans : ';
		$stmt2 = $db->prepare('SELECT catTitle, catSlug FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID ORDER BY catTitle ASC');
		$stmt2->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
		$stmt2->execute();
		$catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		$links = array();
		foreach ($catRow as $cat) {
			$links[] = "<a href='c-".$cat['catSlug']."'>".$cat['catTitle']."</a>";
		}
		//echo '<span class="font-tiny">'.implode(", ", $links).'</span>';
		//$max = 500;
		$chaine = implode(", ", $links);
		/*
		if (strlen($chaine) >= $max) {
			$chaine = substr($chaine, 0, $max);
			$espace = strrpos($chaine, ", ");
			$chaine = substr($chaine, 0, $espace).' ...';
		}
		*/

	echo '<span class="font-tiny justify">'.$chaine.'</span>';
	echo '<br>';
	echo 'Lien web du média : <span class="font-tiny"><a target="_blank" href="'.html($row['postLink']).'">URL</a></span><br>';
	echo 'Taille : <span class="font-tiny">'.makesize($row['postTaille']).'</span><br>';

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


	$stmt3 = $db->prepare('SELECT * FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postID = :postID AND xbt_files.fid = blog_posts_seo.postID');
	$stmt3->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
        $stmt3->execute();
        $xbt = $stmt3->fetch();

	echo 'Trafic : ';
	echo '<span class="font-tiny">S : <a href="peers.php?hash='.html($row['postHash']).'">'.$xbt['seeders'].'</a> | '; 
	echo 'L : <a href="peers.php?hash='.html($row['postHash']).'">'.$xbt['leechers'].'</a> | ';

	// on met à jour le nb de vues de l'article
	$stmt33 = $db->query('UPDATE blog_posts_seo SET postViews = postViews+1 WHERE postID = '.$row['postID']);

	// on affiche le nombre de vue de l'article
	$stmt333 = $db->prepare('SELECT postViews FROM blog_posts_seo WHERE postID = :postID');
        $stmt333->execute(array(':postID' => $row['postID']));
        $views = $stmt333->fetch();

	echo 'T : '.$xbt['completed'].'</span><br>';
	echo 'Lu : <span class="font-tiny">'.$views['postViews'].' fois</span><br>';
	echo 'Licence(s) : ';	

	$stmt3 = $db->prepare('SELECT licenceID,licenceTitle,licenceSlug FROM blog_licences, blog_post_licences WHERE blog_licences.licenceID = blog_post_licences.licenceID_BPL AND blog_post_licences.postID_BPL = :postID_BPL ORDER BY licenceTitle ASC');
        $stmt3->execute(array(':postID_BPL' => $row['postID']));
	$licenceRow = $stmt3->fetchALL(PDO::FETCH_ASSOC);
	$liclist = array();

	/*
	foreach($licenceRow as $lic) {
		$liclist[] = $lic['licenceTitle'];
	}
	echo '<span class="font-tiny">'.implode(", ", $liclist).'</span>';
	*/
	foreach ($licenceRow as $lic) {
		$liclist[] = '<a href="l-'.html($lic['licenceSlug']).'">'.html($lic['licenceTitle']).'</a>';
	}

	//$max = 80;
	$chaine = implode(", ", $liclist);
	/*
	if (strlen($chaine) >= $max) {
		$chaine = substr($chaine, 0, $max);
		$espace = strrpos($chaine, ", ");
		$chaine = substr($chaine, 0, $espace).' ...';
	}
	*/

	echo '<span class="font-tiny justify">'.$chaine.'</span>';

	if(isset($_SESSION['username']) && isset($_SESSION['userid'])) {
		if(($row['postAuthor'] == $_SESSION['username']) || ($_SESSION['userid'] == 1)) {
	?>
		<br><br>
		<span>
		   <a href="admin/edit-post.php?id=<?php echo html($row['postID']); ?>"><input type="button" class="button small green" value="Edit."></a>
		   <a href="javascript:deltorr('<?php echo html($row['postID']); ?>','<?php echo html($row['postTitle']); ?>')"><input type="button" class="button small red" value="Supp."></a>
		</span>
	
	<?php
		}
	}

	// Réseaux sociaux	
	echo '<p><div class="font-large">';
		echo 'Partager :<br>';
		echo '<a href="https://www.facebook.com/sharer/sharer.php?u='.SITEURL.'/'.$row['postSlug'].'&display=popup&ref=plugin&src=like&kid_directed_site=0" target="_blanck" class="fab fa-facebook-square"></a>  ';
		echo '<a href="https://twitter.com/intent/tweet?hashtags=ft4a%2CLibre&original_referer=https%3A%2F%2Fwww.ft4a.fr%2F'.$row['postSlug'].'&ref_src=twsrc%5Etfw&text=A télécharger sur f4a.fr : '.$row['postTitle'].'&tw_p=tweetbutton&url='.SITEURL.'%2F'.$row['postTitle'].'" class="fab fa-twitter-square" target="_blanck"></a> ';
	echo '</div></p>';

	echo '</div>';

	echo '<div class="justify">';
        	if (!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
                	echo '<img src="images/imgtorrents/'.html($row['postImage']).'" alt="'.html($row['postTitle']).'" class="imgr boxholder" style="max-width: 150px; max-height: 150px;">';
                }
                else {
                	echo '<img src="images/noimage.png" alt="Image" class="imgl" style="max-width: 150px; max-height: 150px;">';
                }
            	echo '<p>'.BBCode2Html($row['postDesc']).'</p>';
                echo '<p>'.BBCode2Html($row['postCont']).'</p>';
	echo '</div>';

	//Infos fichiers torrent
	echo '<div class="first accordion-wrapper"><a href="javascript:void(0)" class="accordion-title orange">';
		if (isset($content['info']) && $content['info']) {
            		$thefile=$content['info'];
		}

		if($numfiles == 1) {
       			echo '<tr><td>Nb de fichier du torrent : '.$numfiles.'</td></tr>';
		}
		else {
			echo '<tr><td>Nb de fichiers du torrent : '.$numfiles.'</td></tr>';
		}
	echo '</span></a>';

		echo '<div class="accordion-content">';
			if (isset($thefile['files'])) {
				echo '<span class="bold">Fichiers du torrent : </span>';
				foreach($content['info']['files'] as $multiplefiles) {
					echo '<p><span class="fa fa-file"></span> '.implode('/',$multiplefiles['path']).'</p>';
				}
			}
                	else {
                		echo '<span class="bold">Fichier du torrent : </span>';
				echo '<span class="fa fa-file"></span> '.html($thefile['name']);
			}
		echo '</div>';
	echo '</div>';
	?>

	<div class="divider2"></div>

	<!-- COMMENTAIRES ORIGINAUX-->
	<a name="commentaires"></a><h2>Laisser un commentaire</h2>

	<?php
	// On affiche : commentaire supprimé ! 
        if(isset($_GET['action']) && $_GET['action'] == 'commsupprime') {
        	echo '<div class="alert-msg rnd8 success">Le commentaire a été supprimé.</div>';
        }

	if(!$user->is_logged_in()) {
        	echo '<p>Vous devez être <a href="/login.php">connecté(e)</a> pour rédiger un commentaire.</p>';
	}

	// il faut être connecté pour laisser un commentaire
        if($user->is_logged_in()) {
        ?>

	<form class="rnd5" action="" method="post">
		<div class="form-input clear">
			<div class="one_third first clear">
				<label for="username">Pseudo
			      		<input type="text" name="username" value="<?php echo $_SESSION['username']; ?>">
			   	</label>
			</div>
			<br><br><br>
			<label for="commentaire">Commentaire
				<br><span class="font-tiny">Vous pouvez éventuellement utiliser quelques éléments de mise en page BBCode : [b][/b], [i][/i], [u][/u], [url][/url]</span>
			   	<textarea name="commentaire" id="commentaire" rows="10"></textarea>
			</label>
			<label for="verif_box">Anti-spam :<br>
           			<div class="g-recaptcha" data-sitekey="6LfrmrUUAAAAAOU9sv-UO9A6joAVpLvrRB3sCbtt"></div>
        		</label>
		</div>
		<br><p class="right">
		   <input type="submit" class="button small orange" name="submitcomm" value="Envoyer le commentaire">
		   &nbsp;
                   <input type="reset" value="Annuler" class="button small grey">
		</p>
	</form>
	

	<div class="divider2"></div>

	<?php
        } // fin if

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
		$secret = "6LfrmrUUAAAAAGcsi7lz-SSW0XnZj8DMex4gBF0P";
		$response = $_POST['g-recaptcha-response'];
		$remoteip = $_SERVER['REMOTE_ADDR'];
		$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
			. $secret
			. "&response=" . $response
			. "&remoteip=" . $remoteip ;
		$decode = json_decode(file_get_contents($api_url), true);

		if ($decode['success'] == true) {

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
		} // /if decode success

		else {
			$error[] = 'Erreur antispam !';
		}

	}// fin if isset $_POST

	//check for any errors
        if(isset($error)){
        	foreach($error as $error){
			echo '<div class="alert-msg rnd8 error">'.$error.'</div>';
                }
        }

	$stmt = $db->prepare('SELECT * FROM blog_posts_comments LEFT JOIN blog_members ON blog_members.username = blog_posts_comments.cuser WHERE cid_torrent = :cid_torrent ORDER BY cadded ASC');
        $stmt->execute(array(':cid_torrent' => $row['postID']));
	$nbcomm = $stmt->rowCount();

	if ($nbcomm < 2) {
		echo '<p>Il y a '.$nbcomm.' commentaire pour "'.html($row['postTitle']).'" </p><br>';
	}
	else {
		echo '<p>Il y a '.$nbcomm.' commentaires pour "'.html($row['postTitle']).'" </p><br>';
	}

	while($comm = $stmt->fetch()) {
		echo '<div id="blog-posts">';
			if(!empty($comm['avatar'])) {
				echo '<img src="/images/avatars/'.$comm['avatar'].'" alt="" class="imgl" style="width: 40px; height: 40px;">';
			}
			else {
				echo '<img src="/images/avatars/avatar.png" alt="" class="imgl" style="height: 40px; border: 1px solid;">';
			}

			echo '<a href="/profil.php?membre='.html($comm['cuser']).'">'.html($comm['cuser']).'</a><br>';
			sscanf($comm['cadded'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
			echo '<span class="font-tiny">Le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</span>';
			echo '<br><br>';
			echo '<div class="justify">';
				echo '<span>' . nl2br(bbcode(($comm['ctext']))) . '</span>';
				$cuser = $comm['cuser'];
				if($user->is_logged_in() && $_SESSION['username'] == $cuser) {
					echo '<p class="right"><a href="commentaire_supprimer.php?cid='.$comm['cid'].'&cid_torrent='.$comm['cid_torrent'].'" onclick="return confirm(\'Êtes-vous certain de vouloir supprimer ce commentaire ?\')">
					<span class="fa fa-trash font-large"></span></a></p>';
				}
			echo '</div>';
		echo '</div><br>';
	} // /while


	//check for any errors
        if(isset($error)) {
        	foreach($c_error as $c_error){
			echo '<br /><div class="alert-msg rnd8 error">'.$c_error.'</div>';
                }
        }

	?>


	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

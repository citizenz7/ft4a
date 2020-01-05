<?php
require_once '../includes/config.php';

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

        header('Location: /admin/index.php?action=supprime');
        exit;

}//fin de if isset $_GET['delpost']

$pagetitle = 'Admin : page générale';

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

	 <?php
        //show message from add / edit page
        if(isset($_GET['action']) && $_GET['action'] == 'supprime'){
                echo '<div class="alert-msg rnd8 success">Le torrent a été supprimé avec succès.</div><br>';
        }

	if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                echo '<div class="alert-msg rnd8 success">Le torrent a été ajouté avec succès.</div><br>';
        }

	if(isset($_GET['message']) && $_GET['message'] == 'envoye') {
		echo '<div class="alert-msg rnd8 success">Message envoyé à tous les membres.</div><br>';
	}
        ?>

        <table>
        <thead><tr>
                <th>Titre</th>
                <th class="center">Date</th>
		<th class="center">Uploader</th>
                <th class="center">Action</th>
        </tr></thead>
        <?php
                try {

			$pages = new Paginator('10','p');

            		$stmt = $db->query('SELECT postID FROM blog_posts_seo');

            		//pass number of records to
            		$pages->set_total($stmt->rowCount());

                        $stmt = $db->query('SELECT postID, postTitle, postAuthor, postDate FROM blog_posts_seo ORDER BY postID DESC '.$pages->get_limit());
                        while($row = $stmt->fetch()){

                                echo '<tbody><tr>';
                                echo '<td style="width:55%;">'.$row['postTitle'].'</td>';
				sscanf($row['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
				echo '<td class="center">'.$jour.'-'.$mois.'-'.$annee.'</td>';
				echo '<td class="center"><a href="../profil.php?membre='.$row['postAuthor'].'">'.$row['postAuthor'].'</a></td>';
                                ?>

                                <td class="center">
                                        <a href="/admin/edit-post.php?id=<?php echo $row['postID'];?>"><input type="button" class="button small green" value="Edit."></a>&nbsp;
                                        <a href="javascript:delpost('<?php echo $row['postID'];?>','<?php echo $row['postTitle'];?>')"><input type="button" class="button small red" value="Supp."></a>
                                </td>

                                <?php
                                echo '</tr></tbody>';

                        }


                } catch(PDOException $e) {
                    echo $e->getMessage();
                }
        ?>
        </table>

	<br><p>	
		<?php echo $pages->page_links('/admin/?'); ?>
	</p>		

        </div>
		
	<div class="divider2"></div>
	
      </div>

<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

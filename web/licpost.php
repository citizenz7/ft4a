<?php
include_once 'includes/config.php';

$stmt = $db->prepare('SELECT licenceID, licenceTitle FROM blog_licences WHERE licenceSlug = :licenceSlug');
$stmt->bindValue(':licenceSlug', html($_GET['id']));
$stmt->execute();
$row = $stmt->fetch();

/*
//if post does not exists redirect user.
if($row['licenceID'] == ''){
        header('Location: ./');
        exit;
}
*/
if (!isset($row['licenceID']) || empty($row['licenceID'])) {
	header('Location: ./');
        exit();
} 

elseif (!filter_var($row['licenceID'], FILTER_VALIDATE_INT)) {
	header('Location: ./');
        exit();
}

$pagetitle = 'Licence : '.html($row['licenceTitle']);

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
        try {
 	       	$pages = new Paginator('8','p');
               	$stmt = $db->prepare('SELECT blog_posts_seo.postID FROM blog_posts_seo, blog_post_licences WHERE blog_posts_seo.postID = blog_post_licences.postID_BPL AND blog_post_licences.licenceID_BPL = :licenceID');
               	$stmt->execute(array(':licenceID' => $row['licenceID']));
		$count = $stmt->rowCount();

                //pass number of records to
                $pages->set_total($stmt->rowCount());

                $stmt = $db->prepare('
                	SELECT blog_posts_seo.postID, blog_posts_seo.postTitle, blog_posts_seo.postAuthor, blog_posts_seo.postSlug, blog_posts_seo.postDesc, blog_posts_seo.postDate,blog_posts_seo.postImage 
                        FROM blog_posts_seo,blog_post_licences
                        WHERE blog_posts_seo.postID = blog_post_licences.postID_BPL
                        AND blog_post_licences.licenceID_BPL = :licenceID
                        ORDER BY postID DESC '.$pages->get_limit());
                $stmt->execute(array(':licenceID' => $row['licenceID']));

		echo '<h2>'.$row['licenceTitle'].'</h2>';

		if (empty($count)) {
	                echo '<p>Aucun torrent pour cette licence.</p>';
                }

                while($row1 = $stmt->fetch()){
                	echo '<h2><a href="'.html($row1['postSlug']).'">'.html($row1['postTitle']).'</a></h2>';
			echo 'Posté le '.date_fr('l j F Y à H:i:s', strtotime($row1['postDate'])).' par ';		
		
                	$stmt2 = $db->prepare('SELECT licenceTitle, licenceSlug FROM blog_licences, blog_post_licences WHERE blog_licences.licenceID = blog_post_licences.licenceID_BPL AND blog_post_licences.postID_BPL = :postID_BPL');
                	$stmt2->execute(array(':postID_BPL' => $row1['postID']));
                	$licRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

			echo html($row1['postAuthor']).' dans ';
                		$links = array();
                		foreach ($licRow as $lic) {
                			$links[] = "<a href='l-".$lic['licenceSlug']."'>".$lic['licenceTitle']."</a>";
                		}
                	echo implode(", ", $links);
			echo '<p class="justify">';
                                echo '<img src="'.$WEB_IMAGES_TORRENTS.$row1['postImage'].'" alt="'.$row1['postTitle'].'" class="imgl boxholder" style="max-width:70px; max-height:70px;">';
                                echo bbcode($row1['postDesc']);
                        echo '</p>';
                        echo '<div class="divider2"></div>';

                }

		echo '<br><br>';
                echo $pages->page_links('l-'.html($_GET['id']).'&');

	} 

	catch(PDOException $e) {
        	echo $e->getMessage();
        }

        ?>

        </div>
		
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

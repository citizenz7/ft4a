<?php
include_once 'includes/config.php';

$stmt = $db->prepare('SELECT catID,catTitle FROM blog_cats WHERE catSlug = :catSlug');
$stmt->bindValue(':catSlug', html($_GET['id']));
$stmt->execute();
$row = $stmt->fetch();


if (!isset($row['catID']) || empty($row['catID'])) {
	header('Location: ./');
        exit();
}

elseif (!filter_var($row['catID'], FILTER_VALIDATE_INT)) {
        header('Location: ./');
        exit();
}

$pagetitle = 'Catégorie : '.html($row['catTitle']);


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

	<h2><?php echo html($row['catTitle']); ?></h2>

	<?php
        try {
        	$pages = new Paginator('5','p');
                $stmt = $db->prepare('SELECT blog_posts_seo.postID FROM blog_posts_seo, blog_post_cats WHERE blog_posts_seo.postID = blog_post_cats.postID AND blog_post_cats.catID = :catID');
		$stmt->bindValue(':catID', $row['catID'], PDO::PARAM_INT);
                $stmt->execute();

		$count = $stmt->rowCount();

		if (empty($count)) {
			echo '<p>Aucun torrent dans cette catégorie.</p>';
		}
	
                //pass number of records to
                $pages->set_total($stmt->rowCount());
                $stmt = $db->prepare('
               	SELECT blog_posts_seo.postID, blog_posts_seo.postHash, blog_posts_seo.postTitle, blog_posts_seo.postAuthor, blog_posts_seo.postSlug, blog_posts_seo.postLink, blog_posts_seo.postDesc, blog_posts_seo.postTaille, blog_posts_seo.postDate, blog_posts_seo.postViews, blog_posts_seo.postImage
               	FROM blog_posts_seo, blog_post_cats
                WHERE blog_posts_seo.postID = blog_post_cats.postID
                AND blog_post_cats.catID = :catID
                ORDER BY postID DESC '.$pages->get_limit());
               	$stmt->bindValue(':catID', $row['catID'], PDO::PARAM_INT);
		$stmt->execute();
				
		while($row = $stmt->fetch()){
                       	echo '<h2><a href="'.html($row['postSlug']).'">'.html($row['postTitle']).'</a></h2>';
			echo 'Posté le '.date_fr('l j F Y à H:i:s', strtotime($row['postDate'])).' par ';		
		
                        $stmt2 = $db->prepare('SELECT catTitle, catSlug FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID');
                        $stmt2->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
			$stmt2->execute();
                        $catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

			echo html($row['postAuthor']).' dans ';
	                        $links = array();
                                foreach ($catRow as $cat) {
                                	$links[] = "<a href='c-".html($cat['catSlug'])."'>".html($cat['catTitle'])."</a>";
                                }
                        echo implode(", ", $links);
			echo '<p class="justify">';
				echo '<img src="'.$WEB_IMAGES_TORRENTS.$row['postImage'].'" alt="'.$row['postTitle'].'" class="imgl boxholder" style="max-width:70px; max-height:70px;">';
				echo bbcode($row['postDesc']);
			echo '</p>';
			echo '<div class="divider2"></div>';
		} //while

		echo '<br><br>';
                echo $pages->page_links('c-'.html($_GET['id']).'&');

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

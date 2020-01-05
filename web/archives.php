<?php
include_once 'includes/config.php';

$pagetitle = 'Archives';

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
        try {

       		//collect month and year data
                $month = html($_GET['month']);
                $year = html($_GET['year']);

                //set from and to dates
                $from = date('Y-m-01 00:00:00', strtotime("$year-$month"));
                $to = date('Y-m-31 23:59:59', strtotime("$year-$month"));

                $pages = new Paginator('10','p');

                $stmt = $db->prepare('SELECT postID FROM blog_posts_seo WHERE postDate >= :from AND postDate <= :to');
                $stmt->execute(array(
 	               ':from' => $from,
        	       ':to' => $to
                ));

                //pass number of records to
                $pages->set_total($stmt->rowCount());

                $stmt = $db->prepare('SELECT postID, postTitle, postSlug, postAuthor, postDesc, postDate, postImage FROM blog_posts_seo WHERE postDate >= :from AND postDate <= :to ORDER BY postID DESC '.$pages->get_limit());
                $stmt->execute(array(
                	':from' => $from,
                	':to' => $to
                ));

		echo '<h2>Archives : '.$month.'-'.$year.'</h2><br />';

               while($row = $stmt->fetch()){
			echo '<h2><a href="'.html($row['postSlug']).'">'.html($row['postTitle']).'</a></h2>';
                        echo 'Posté le '.date_fr('l j F Y à H:i:s', strtotime(html($row['postDate']))).' par ';
			echo html($row['postAuthor']).' dans ';

                        $stmt2 = $db->prepare('SELECT catTitle, catSlug FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID');
			$stmt2->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
                        $stmt2->execute();
                        $catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

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

		}
		

                echo $pages->page_links("a-$month-$year&");

	} // /try 

	catch(PDOException $e) {
        	echo $e->getMessage();
	}
	?>


	<!-- ### -->
        </div>
		
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

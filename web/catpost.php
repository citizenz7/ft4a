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

			<h4><?php echo html($row['catTitle']); ?></h4>
			
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

				echo '<div class="container">';

				while($row = $stmt->fetch()){
					echo '<div class="card pl-2 pr-2 mb-2">';
                       				echo '<span class="lead"><a href="'.html($row['postSlug']).'">'.html($row['postTitle']).'</a></span>';
						echo '<p class="small">';
							echo 'Posté le '.date_fr('l j F Y à H:i:s', strtotime($row['postDate'])).' par ';
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
						echo '</p>';
						echo '<div class="text-justify small mb-3">';
							echo '<img src="'.$WEB_IMAGES_TORRENTS.$row['postImage'].'" alt="'.$row['postTitle'].'" class="thumbnail float-left rounded" style="max-width:50px; margin-right:10px;">';
							echo bbcode($row['postDesc']);
						echo '</div>';
					echo '</div>';
				} //while

				echo '<br><br>';
                		echo $pages->page_links('c-'.html($_GET['id']).'&');
			}//try

			catch(PDOException $e) {
        			echo $e->getMessage();
			}
			?>

			</div>

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

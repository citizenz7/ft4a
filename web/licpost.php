<?php
include_once 'includes/config.php';

$stmt = $db->prepare('SELECT licenceID, licenceTitle FROM blog_licences WHERE licenceSlug = :licenceSlug');
$stmt->bindValue(':licenceSlug', html($_GET['id']));
$stmt->execute();
$row = $stmt->fetch();

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

			<h4><?php echo html($row['licenceTitle']); ?></h4>
			
			<?php
        		try {
				$pages = new Paginator('8','p');
				$stmt = $db->prepare('SELECT blog_posts_seo.postID FROM blog_posts_seo, blog_post_licences WHERE blog_posts_seo.postID = blog_post_licences.postID_BPL AND blog_post_licences.licenceID_BPL = :licenceID');
               			$stmt->execute(array(':licenceID' => $row['licenceID']));
				$count = $stmt->rowCount();

				if (empty($count)) {
					echo '<p>Aucun torrent pour cette licence.</p>';
				}
	
                		//pass number of records to
               	 		$pages->set_total($stmt->rowCount());
				$stmt = $db->prepare('
                			SELECT blog_posts_seo.postID, blog_posts_seo.postTitle, blog_posts_seo.postAuthor, blog_posts_seo.postSlug, blog_posts_seo.postDesc, blog_posts_seo.postDate,blog_posts_seo.postImage 
                        		FROM blog_posts_seo,blog_post_licences
                        		WHERE blog_posts_seo.postID = blog_post_licences.postID_BPL
                        		AND blog_post_licences.licenceID_BPL = :licenceID
                        		ORDER BY postID DESC '.$pages->get_limit());
                		$stmt->execute(array(':licenceID' => $row['licenceID']));

				echo '<div class="container">';

				while($row = $stmt->fetch()){
					echo '<div class="card pl-2 pr-2 mb-2">';
                       				echo '<span class="lead"><a href="'.html($row['postSlug']).'">'.html($row['postTitle']).'</a></span>';
						echo '<p class="small">';
							echo 'Posté le '.date_fr('l j F Y à H:i:s', strtotime($row['postDate'])).' par ';
							echo html($row['postAuthor']).' dans ';
							$stmt2 = $db->prepare('SELECT licenceTitle, licenceSlug FROM blog_licences, blog_post_licences WHERE blog_licences.licenceID = blog_post_licences.licenceID_BPL AND blog_post_licences.postID_BPL = :postID_BPL');
                					$stmt2->execute(array(':postID_BPL' => $row['postID']));
                					$licRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);	
	                        			$links = array();
							foreach ($licRow as $lic) {
                						$links[] = "<a href='l-".$lic['licenceSlug']."'>".$lic['licenceTitle']."</a>";
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
                		echo $pages->page_links('l-'.html($_GET['id']).'&');
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

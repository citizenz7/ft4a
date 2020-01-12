<?php
include_once 'includes/config.php';

$pagetitle= 'Blog : les dernières infos du monde Libre';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<!-- ########################################## content.php ############################################# -->
<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">

      <div class="two_third first">

        <div class="first">

	<!-- ### ARTICLES ###-->
        <?php
        try {
                // Préparation de la pagination
                $pages = new Paginator('5','p');
                $stmt = $db->query('SELECT infoID FROM blog_infos');

                // On passe le nb d'enregistrements à $pages
                $pages->set_total($stmt->rowCount());

                $stmt = $db->query('SELECT infoID, infoTitle, infoSlug, infoCont, infoDate FROM blog_infos ORDER BY infoID DESC '.$pages->get_limit());


                while($row = $stmt->fetch()) {
        ?>

	 		<article class="push30 clear" id="blog-posts">
         			<h2 class="font-large"><a href="i-<?php echo html($row['infoSlug']); ?>"><?php echo html($row['infoTitle']); ?></a></h2>
				<p class="font-tiny" style="margin-top:-20px;">Posté le : <?php echo date_fr('d-m-Y à H:i:s', strtotime($row['infoDate'])); ?></p>
				<div class="justify left">
                    			<?php
                    			$max = 500;
                    			$chaine = $row['infoCont'];
                    			if (strlen($chaine) >= $max) {
                        			$chaine = substr($chaine, 0, $max);
                        			$espace = strrpos($chaine, " ");
                        			$chaine = substr($chaine, 0, $espace).' ...';
                    			}
                    			echo nl2br(bbcode($chaine)); ?>
                    			<a href="i-<?php echo html($row['infoSlug']); ?>" class="read-more">[Lire la suite...]</a><br>
                		</div>
          		</article>

		<?php } // /while

	} // /try

	catch(PDOException $e) {
		echo $e->getMessage();
	}

        echo '<div class="fl_center">';
                echo $pages->page_links();
        echo '</div>';

        ?>

        <!-- ### -->
        </div>

        <div class="divider2"></div>

</div>

<?php
	include_once 'includes/sidebar.php';
	include_once 'includes/footer.php';
?>

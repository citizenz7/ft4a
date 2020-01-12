<?php
include_once 'includes/config.php';

$pagetitle= 'Bienvenue sur '.SITENAMELONG.' !';

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

	<section class="calltoaction opt1 clear">
		<p class="font-medium bold">Bienvenue sur <?php echo SITENAME; ?> !</p><br>
		<p class="font-small justify" style="margin-left:20px;">
			<span class="font-medium bold">Les News :</span><br>
			<i class="far fa-newspaper"></i> [22/08/19] - bigbird a rédigé un <a href="https://lcdgg.thomascyrix.com/2019/08/22/ft4a-xyz-un-nouveau-site-de-torrent-legal/">article</a> sur son blog à propos de ft4a. Merci à lui<br>
			<i class="fab fa-github"></i> [20/08/19] - Le code du site est sur <a href="https://github.com/citizenz7/ft4a">Github</a><br>
		<br>
		</p>
	</section>

	<!-- ### ARTICLES ###-->
	<?php
	try {
		// Préparation de la pagination
		$pages = new Paginator('5','p');
		$stmt = $db->query('SELECT postHash FROM blog_posts_seo');

		// On passe le nb d'enregistrements à $pages
		$pages->set_total($stmt->rowCount());

		$stmt = $db->query('SELECT postID,postHash,postTitle,postAuthor,postSlug,postDesc,postDate,postImage,postViews FROM blog_posts_seo ORDER BY postDate DESC '.$pages->get_limit());

		while($row = $stmt->fetch()) {
	?>

        <article class="push30 clear" id="blog-posts">
           <h2 class="font-large"><a href="<?php echo html($row['postSlug']); ?>"><?php echo html($row['postTitle']); ?></a></h2>
	   
		<div class="font-tiny" style="margin-top:-20px;">
		    <?php sscanf($row['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde); ?>
		    <span class="fa fa-calendar"></span> <?php echo $jour; ?>-<?php echo $mois; ?>-<?php echo $annee; ?> | 
		    <span class="fa fa-user"></span> <a href="profil.php?membre=<?php echo html($row['postAuthor']); ?>"><?php echo html($row['postAuthor']); ?></a> | 

		    <?php
		    // Catégories
		    $stmt2 = $db->prepare('
			SELECT catTitle, catSlug FROM blog_cats, blog_post_cats 
			WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID');
		    $stmt2->bindValue(':postID', $row['postID'], PDO::PARAM_INT);
		    $stmt2->execute();
		    $catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

		    $links = array();
		    foreach ($catRow as $cat) {
			//$links[] = $cat['catTitle'];
			$links[] = '<a href="c-'.html($cat['catSlug']).'">'.html($cat['catTitle']).'</a>';
		    }
		
		    $max = 120;
		    $chaine = implode(", ", $links);
		    if (strlen($chaine) >= $max) {
			$chaine = substr($chaine, 0, $max);
			$espace = strrpos($chaine, ", ");
			$chaine = substr($chaine, 0, $espace).' - ...';
		    }
		
		    echo '<span class="fa fa-tag"></span> '.$chaine.' | ';
		
		    // Licences
		    $stmt4 = $db->prepare('
			SELECT licenceID,licenceTitle,licenceSlug FROM blog_licences, blog_post_licences 
			WHERE blog_licences.licenceID = blog_post_licences.licenceID_BPL AND blog_post_licences.postID_BPL = :postID_BPL 
			ORDER BY licenceTitle ASC');
		    $stmt4->bindValue(':postID_BPL', $row['postID'], PDO::PARAM_INT);
		    $stmt4->execute();
		    $licenceRow = $stmt4->fetchALL(PDO::FETCH_ASSOC);

		    $liclist = array();
		    foreach($licenceRow as $lic) {
			//$liclist[] = $lic['licenceTitle'];
			$liclist[] = '<a href="l-'.html($lic['licenceSlug']).'">'.html($lic['licenceTitle']).'</a>';
		    }

		    $max = 120;
		    $chaine = implode(", ", $liclist);
		    if (strlen($chaine) >= $max) {
			$chaine = substr($chaine, 0, $max);
			$espace = strrpos($chaine, ", ");
			$chaine = substr($chaine, 0, $espace).' [...] ';
		    }
		
		    echo '<i class="fab fa-creative-commons"></i> '.$chaine.' | ';
		
		    // Nb de commentaires
		    $stmt3 = $db->prepare('SELECT cid FROM blog_posts_comments WHERE cid_torrent = :cid_torrent');
		    $stmt3->execute(array(':cid_torrent' => $row['postID']));
		    $commRow = $stmt3->rowCount();
		    ?>

		    <?php if($commRow == 0) { ?>

		    <i class="fas fa-comment"></i> <?php echo $commRow; ?> |

		    <?php }
		    else { ?>
			<span class="fas fa-comments"></span> <a href="<?php echo html($row['postSlug']); ?>#commentaires"><?php echo $commRow; ?></a> | 
		    <?php } ?>

		    <span class="fas fa-eye"></span> <?php echo html($row['postViews']); ?>
		</div>

		<br>

	    	<?php
		if (!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
			echo '<img class="imgl boxholder" style="max-width:100px;" src="'.$WEB_IMAGES_TORRENTS.html($row['postImage']).'" alt="'.html($row['postTitle']).'">';
		}
		else {
			echo '<img class="imgl boxholder" src="images/noimage.png" alt="Pas d\'image" style="max-width:100px;">';
		}
		?>

                <div class="justify left">
		    <?php
	   	    $max = 500;
		    $chaine = $row['postDesc'];
		    if (strlen($chaine) >= $max) {
			$chaine = substr($chaine, 0, $max);
			$espace = strrpos($chaine, " ");
			$chaine = substr($chaine, 0, $espace).' ...';
		    }

		    echo nl2br(bbcode($chaine)); ?>
		    <a href="<?php echo html($row['postSlug']); ?>" class="read-more">[Lire la suite...]</a><br>
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
	
	<h2 class="font-large"><?php echo SITENAMELONG; ?> vous recommande :</h2>

	<table>
	   <tr>		
              <article id="recommande" class="one_half">
		<?php
		$stmt = $db->query('SELECT postID,postHash,postTitle,postAuthor,postSlug,postDesc,postDate,postImage,postViews FROM blog_posts_seo ORDER BY RAND() DESC LIMIT 2');
		while($row = $stmt->fetch()) {
		?>

		   <td width="50%;">
		       <h2 class="font-small" style="margin:5px;;">
		       <!--<a href="<?php //echo html($row['postSlug']); ?>"><?php //echo html($row['postTitle']); ?></a>-->
		       <?php
		       $max = 50;
		       $title = $row['postTitle'];
		       if (strlen($title) >= $max) {
			 $title = substr($title, 0, $max);
			 $espace = strrpos($title, " ");
			 $title = substr($title, 0, $espace).' ...';
		       }
		       echo '<a href="'.html($row['postSlug']).'">'.nl2br(bbcode($title)).'</a>';
		       ?>
	               </h2>
	 	       <?php
         	       if (!empty($row['postImage']) && file_exists($REP_IMAGES_TORRENTS.$row['postImage'])) {
         	         echo '<img class="imgl boxholder" style="max-width:50px;" src="'.$WEB_IMAGES_TORRENTS.html($row['postImage']).'" alt="'.html($row['postTitle']).'">';
         	       }
         	       else {
         		 echo '<img class="imgl boxholder" src="images/noimage.png" alt="Pas d\'image">';
         	       }
         	       ?>

		       <p class="justify font-content">
        	         <?php
        	         $max = 150;
       	 	         $chaine = $row['postDesc'];
        	         if (strlen($chaine) >= $max) {
        		    $chaine = substr($chaine, 0, $max);
        		    $espace = strrpos($chaine, " ");
        		    $chaine = substr($chaine, 0, $espace).' ...';
       	 	         }
       		         echo nl2br(bbcode($chaine));
		         ?>
       		         <span class="fl_right"><a class="font-tiny" href="<?php echo html($row['postSlug']); ?>">Lire la suite &raquo;</a></span>
                       </p>
		   </td>
	
		   <?php
	           } // /while
	           ?>
               </article>
	   </tr>
	</table>

      </div>

<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

<?php
include_once 'includes/config.php';

$pagetitle = 'Liste des torrents';

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

	<section>
	
	<h2>Liste des torrents</h2>

	<?php
        // On affiche : torrent ajouté ! 
        if(isset($_GET['action']) && $_GET['action'] == 'ajoute') {
                echo '<div class="alert-msg rnd8 success">Le torrent a été ajouté avec succès ! <a class="close" href="#">X</a></div>';
        }

	// Pas d'accès direct à la page download sans file ID
        if(isset($_GET['action']) && $_GET['action'] == 'nodirect') {
                echo '<div class="alert-msg rnd8 error">ERREUR : Vous ne pouvez pas accéder directement à cette page sans préciser le torrent à télécharger... <a class="close" href="#">X</a></div>';
        }

	// Pas d'accès à la page download si le file ID n'existe pas
        if(isset($_GET['action']) && $_GET['action'] == 'noexist') {
                echo '<div class="alert-msg rnd8 error"><span class="fa fa-warning font-large"></span>&nbsp;ERREUR : Ce torrent n\'existe pas ! <a class="close" href="#">X</a></div>';
        }

	//On affiche le message de suppression
	if(isset($_GET['delpost'])){
		echo '<div class="alert-msg rnd8 success">Le torrent a été supprimé avec succès ! <a class="close" href="#">X</a></div>';
	}
	?>

	<table>
	  <thead>
            <tr>
        	<th><a href="torrents.php?tri=postTitle&ordre=desc">&#x2191;</a>Nom<a href="torrents.php?tri=postTitle&ordre=asc">&#x2193;</a></th>
		<th style="width:9%;"><a href="torrents.php?tri=postTaille&ordre=desc">&#x2191;</a>Taille<a href="torrents.php?tri=postTaille&ordre=asc">&#x2193;</a></th>
		<th><a href="torrents.php?tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a href="torrents.php?tri=postDate&ordre=asc">&#x2193;</a></th>
		<!--<th><a href="torrents.php?tri=postAuthor&ordre=desc">&#x2191;</a>Par<a href="torrents.php?tri=postAuthor&ordre=asc">&#x2193;</a></th>-->
		<th>Catégorie(s)</th>
		<th><a href="torrents.php?tri=seeders&ordre=desc">&#x2191;</a>S<a href="torrents.php?tri=seeders&ordre=asc">&#x2193;</a></th>
		<th><a href="torrents.php?tri=leechers&ordre=desc">&#x2191;</a>L<a href="torrents.php?tri=leechers&ordre=asc">&#x2193;</a></th>
		<th><a href="torrents.php?tri=completed&ordre=desc">&#x2191;</a>T<a href="torrents.php?tri=completed&ordre=asc">&#x2193;</a></th>
	     </tr>
          </thead>

	<?php
        	try {
			// On affiche 15 torrents par page
			$pages = new Paginator(NBTORRENTS,'page');

			$stmt = $db->query('SELECT postHash FROM blog_posts_seo');
			$pages->set_total($stmt->rowCount());


			// On met en place le tri--------------------------------------------
			if(isset($_GET['tri'])) {
				$tri = html($_GET['tri']);
			}
			else {
				$post_tri = 'postDate';
				$tri = html($post_tri);
			}

			if(isset($_GET['ordre'])) {
				$ordre = html($_GET['ordre']);
			}
			else {
				$ordre_tri = 'desc';
				$ordre = html($ordre_tri);
			}

			// Protection du tri -----------------------------------------------
			if (!empty($_GET['tri']) && !in_array($_GET['tri'], array('postID','postHash', 'postTitle', 'postViews', 'postTaille', 'postDate', 'postAuthor', 'seeders', 'leechers', 'completed'))) {
				header('Location: index.php');
				exit();
			}
			if (!empty($_GET['ordre']) && !in_array($_GET['ordre'], array('asc','desc','ASC','DESC'))) {
				header('Location: index.php');
				exit();
			}
			// -----------------------------------------------------------------

			$stmt = $db->query('SELECT * FROM blog_posts_seo b LEFT JOIN xbt_files x ON x.fid = b.postID ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit());

			echo '<tbody>';

	            	while($row = $stmt->fetch()){

				$stmt2 = $db->prepare('SELECT catTitle, catSlug FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID ORDER BY catTitle ASC');
				$stmt2->execute(array(':postID' => $row['postID']));
			
				$catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

				echo '<tr>';
                       			echo '<td><img src="/images/imgtorrents/'.$row['postImage'].'" style="width:25px; height:25px;" class="left boxholder" alt=""> <a href="'.html($row['postSlug']).'">'.html($row['postTitle']).'</a></td>';
					echo '<td class="font-tiny center">'.html(makesize($row['postTaille'])).'</td>';

					sscanf($row['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
					echo '<td class="font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
			
					//echo '<td><a href="admin/profil.php?membre='.html($row['postAuthor']).'">'.html($row['postAuthor']).'</a></td>';

					$links = array();
					foreach ($catRow as $cat) {
						$links[] = '<a href="c-'.html($cat['catSlug']).'">'.html($cat['catTitle']).'</a>';
                    			}

					$max = 300;
					$chaine = implode(", ", $links);
					if (strlen($chaine) >= $max) {
						$chaine = substr($chaine, 0, $max);
						$espace = strrpos($chaine, ", ");
						$chaine = substr($chaine, 0, $espace).' ...';
					}

					echo '<td class="font-tiny center" style="width:150px;">'.$chaine.'</td>';

					$exa = '0x';
					$hash = $exa.$row['postHash'];
			
					$stmt3 = $db->prepare('SELECT * FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postHash = :postHash AND xbt_files.info_hash = '.$hash);
					$stmt3->execute(array(':postHash' => $row['postHash']));
					$xbt = $stmt3->fetch();

					echo '<td class="center"><a class="green" href="peers.php?hash='.html($row['postHash']).'">'.$xbt['seeders'].'</a></td>';
					echo '<td class="center"><a class="red" href="peers.php?hash='.html($row['postHash']).'">'.$xbt['leechers'].'</a></td>';
					echo '<td class="center">'.$xbt['completed'].'</td>';

				echo '</tr>';
			} // /while

		echo '</tbody>';

		} // /try
		
 
		catch(PDOException $e) {
			echo $e->getMessage();
		}
        ?>
        
        </table>
	</section>


	<?php echo $pages->page_links('torrents.php?tri='.$tri.'&ordre='.$ordre.'&'); ?>

	<br>
        <p class="center font-tiny"><span class="bold">Légende</span> : S = Nb de Seeders, L = Nb de Leechers, T = Nb de Téléchargements</p>



	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

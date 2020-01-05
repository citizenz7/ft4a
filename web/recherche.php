<?php
include_once 'includes/config.php';

$pagetitle = 'Connexion membres';

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
	if(isset($_POST['requete']) && $_POST['requete'] != NULL) {
       		$requete = html($_POST['requete']);
    		$req = $db->prepare('SELECT * FROM blog_posts_seo WHERE postTitle LIKE :requete ORDER BY postDate DESC');
    		$req->execute(array('requete' => '%'.$requete.'%'));
      
    		$nb_resultats = $req->rowCount();
    
		if($nb_resultats != 0) {
	?>

		<h2>Résultats de votre recherche de torrents</h2>

    		<p>Nous avons trouvé 
		<?php
		echo $nb_resultats;

		if($nb_resultats > 1) {
			echo ' résultats :';
		}
		else {
			echo ' résultat :';
		}
    		?>
		</p><br>


		<table>
		   <thead>
		      <th>Nom du torrent</th>
		   </thead>
		   <tbody>
    			<?php
    			while($donnees = $req->fetch()) {
    			?>
			   <tr>
			      <td><a style="font-weight:bold; font-size:14px;" href="<?php echo html($donnees['postSlug']); ?>"><?php echo html($donnees['postTitle']); ?><a></td>
			   </tr>
		
    <?php
    } // fin de la boucle
    ?>
		   </tbody>
    		</table>

	<!-- <a href="recherche.php" style="text-decoration: none;"><input type="button" class="button" value="Faire une nouvelle recherche" /></a> -->

    		<?php
    		} // Fin d'affichage des résultats

    		else {
    		?>
    
    		<h2>Aucun résultat ! ;(</h2>
    		<p>Nous n'avons trouvé aucun résultat pour votre requête "<?php echo html($_POST['requete']); ?>".
    		<!-- <a href="recherche.php" style="text-decoration: none;"><input type="button" class="button" value="Faire une recherche avec un autre mot-clé" /></a> -->
    		</p>
    
    		<?php
    		}// fin de l'affichage des erreurs

    		$req->closeCursor(); // on ferme mysql
    	}

	else { // formulaire html
	?>
	
	<p>Vous allez faire une recherche sur notre site concernant les noms des torrents. Tapez une requête pour réaliser une recherche.</p>

	<form class="rnd5" action="recherche.php" method="Post">
		<div class="form-input clear">
		   <input type="text" name="requete" size="40">
		</div>
		<br><p>
		   <input type="submit" class="button small orange" value="Recherche">
		   &nbsp;
		   <input type="reset" value="Reset" class="button small grey">
		</p>
	</form>

	<?php
	} // fin
	?>


	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

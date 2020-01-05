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
        if(isset($_POST['requete']) && $_POST['requete'] != NULL){
        	$requete = html($_POST['requete']);
        	$req = $db->prepare('SELECT * FROM blog_members WHERE username LIKE :requete AND username != "Visiteur" ORDER BY memberID DESC');
        	$req->execute(array('requete' => '%'.$requete.'%'));

                $nb_resultats = $req->rowCount();
                if($nb_resultats != 0) {
        ?>

    	<h2>Résultats de votre recherche de membre</h2>

    	<p>Nous avons trouvé 

	<?php echo $nb_resultats;
            if($nb_resultats > 1) { echo ' résultats :'; } else { echo ' résultat :'; }
    	?>

    	<br>

    	<ul class="list none">
    		<?php
    			while($donnees = $req->fetch()) {
    		?>
                <li><a href="/profil.php?membre=<?php echo html($donnees['username']); ?>"><?php echo html($donnees['username']); ?></a></li>

    		<?php
    		} // fin de la boucle
    		?>
    	</ul>

        <!-- <a href="recherche.php" style="text-decoration: none;"><input type="button" class="button" value="Faire une nouvelle recherche" /></a> -->

    	<?php
    	} // Fin d'affichage des résultats

    	else {
	?>

    	<h2>Pas de résultat</h2>
    	<p>Nous n'avons trouvé aucun pseudo de membre pour votre requête : "<?php echo $requete; ?>".
    	   <br><br>
	   <div class="right"><a href="/recherche_membres.php"><input type="button" class="button small green" value="Faire une autre recherche" /></a></div>
     	</p>

    	<?php
    	}// fin de l'affichage des erreurs

    	$req->closeCursor(); // on ferme mysql
    
	} // /if isset post requete

        else
        { // formulaire html
        ?>

        <p>Rechercher des membres inscrits :</p>
        <form class="rnd5" action="recherche_membres.php" method="Post">
	   <div class="form-input clear">
              <input type="text" name="requete" style="width:350px;" placeholder="Tapez le pseudo du membre">
	   </div>
	   <br><p class="right">
              <input type="submit" class="button small orange" value="Recherche">
	      &nbsp;
	      <input type="reset" value="Annuler" class="button small grey">
           </p>
        </form>

	<?php
	} // /else
	?>


	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

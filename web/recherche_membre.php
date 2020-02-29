<?php
include_once 'includes/config.php';
$pagetitle = 'Recherche de membres';
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
			
			<!-- Liste des membres -->
			<div class="col-sm-9 mb-4">
				<?php
        			if(isset($_POST['requete']) && $_POST['requete'] != NULL){
        				$requete = html($_POST['requete']);
        				$req = $db->prepare('SELECT * FROM blog_members WHERE username LIKE :requete AND username != "Visiteur" ORDER BY memberID DESC');
        				$req->execute(array('requete' => '%'.$requete.'%'));

                			$nb_resultats = $req->rowCount();
                			if($nb_resultats != 0) {
        			?>

						<h4>Résultats de votre recherche de membre</h4>
    						<p>Nous avons trouvé 

						<?php echo $nb_resultats;
						if($nb_resultats > 1) { 
							echo ' résultats :'; } else { echo ' résultat :'; 
						}
    						?>
						<br>
    						<ul class="list-group">
    							<?php
    							while($donnees = $req->fetch()) {
    							?>
                						<li class="list-group-item"><i class="fas fa-user"></i> <a href="/profil.php?membre=<?php echo html($donnees['username']); ?>"><?php echo html($donnees['username']); ?></a></li>
    							<?php
    							} // fin de la boucle
    							?>
    						</ul>
    					<?php
    					} // Fin d'affichage des résultats

    					else {
					?>
						<h4>Pas de résultat</h4>
    						<p>Nous n'avons trouvé aucun pseudo de membre pour votre requête : "<?php echo $requete; ?>".
    	   					<br><br>
	   					<div class="text-right"><a href="/recherche_membre.php"><button class="xmall btn btn-primary btn-sm">Faire une autre recherche</button></a></div>
     						</p>
    					<?php
    					}// fin de l'affichage des erreurs

    					$req->closeCursor(); // on ferme mysql
    
				} // /if isset post requete

				else {
        			// formulaire html
        			?>
				<p>Rechercher des membres inscrits :</p>
        			<form class="form-group" method="post" action="recherche_membre.php">
					<input type="text" name="requete" class="form-control" placeholder="Tapez le pseudo du membre">
					<div class="text-right mt-2">
              					<button type="submit" class="btn btn-primary btn-sm">Recherche</button>
	      					<button type="reset" class="btn btn-primary btn-sm">Annuler</button>
					</div>
        			</form>

				<?php
				} // /else
				?>

			</div>
			
			<!-- sidebar -->
			<?php include_once 'includes/sidebar.php'; ?>

		</div> <!-- //row -->
		
	<!-- footer -->
	<?php include_once 'includes/footer.php'; ?>

</div> <!-- //container -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html> 

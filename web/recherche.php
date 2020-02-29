<?php
include_once 'includes/config.php';
$pagetitle = 'Recherche';
?>

<?php
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

			<?php
			if(isset($_POST['requete']) && $_POST['requete'] != NULL) {
       				$requete = html($_POST['requete']);
    				$req = $db->prepare('SELECT * FROM blog_posts_seo WHERE postTitle LIKE :requete ORDER BY postDate DESC');
    				$req->execute(array('requete' => '%'.$requete.'%'));
      
    				$nb_resultats = $req->rowCount();
    
				if($nb_resultats != 0) {
				?>
					<h4>Résultats de votre recherche de torrents</h4>
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
					</p>
					<table class="table table-hover">
		   				<thead class="thead-dark">
		      					<th>Nom du torrent</th>
		   				</thead>
		   				<tbody>
    							<?php
    							while($donnees = $req->fetch()) {
    							?>
			   					<tr>
			      						<td><i class="fas fa-file-upload"></i> <a href="<?php echo html($donnees['postSlug']); ?>"><?php echo html($donnees['postTitle']); ?></a></td>
			   					</tr>
							<?php
							} // fin de la boucle
							?>
		   				</tbody>
    					</table>

				<?php
    				} // Fin d'affichage des résultats

    				else {
    				?>
					<h4>Aucun résultat ! ;(</h4>
    					<p>Nous n'avons trouvé aucun résultat pour votre requête "<?php echo html($_POST['requete']); ?>".</p>
				<?php
    				}// fin de l'affichage des erreurs

    				$req->closeCursor(); // on ferme mysql
    			}

			else { // formulaire html
			?>

				<p>Vous allez faire une recherche sur notre site concernant les noms des torrents. Tapez une requête pour réaliser une recherche.</p>
				<form class="form-group" action="recherche.php" method="Post">
					<input type="text" class="form-control" placeholder="Taper le nom du torrent à rechercher" name="requete" size="40">
					<p class="text-right mt-2">	
						<button type="submit" class="btn btn-primary btn-sm">Rechercher</button>
						<button type="reset" class="btn btn-primary btn-sm">Annuler</button>
					</p>
				</form>

			<?php
			} // fin
			?>

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

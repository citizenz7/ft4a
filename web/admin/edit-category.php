<?php
include_once '../includes/config.php';
//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: /admin/login.php');
}

if(isset($_SESSION['userid']) && $_SESSION['userid'] != 1) {
        header('Location: ../');
}

// titre de la page
$pagetitle= 'Admin : Edition des catégories';
include_once '../includes/header.php';
?>

<body id="top">

<div class="container">

        <header>

                <!-- titre -->
                <?php include_once '../includes/header-title.php'; ?>

                <!-- navbar -->
                <?php include_once '../includes/navbar.php'; ?>

        </header>

        <div class="container p-3 my-3 border">
                <div class="row">
                        <div class="col-sm-9">

				<?php include_once('menu.php'); ?>

				<p><a href="/admin/categories.php">Catégories Index</a></p>
			        <h4>Edition de la catégorie</h4>

				<?php
        			//if form has been submitted process it
        			if(isset($_POST['submit'])){
			                $_POST = array_map( 'stripslashes', $_POST );
			                //collect form data
                			extract($_POST);
			                //very basic validation
                			if($catID ==''){
                        			$error[] = 'Ce post possède un ID invalide !.';
                			}
			                if($catTitle ==''){
                        			$error[] = 'Veuillez entrer un titre.';
                			}
	
					if(!isset($error)){

						try {
                                			$catSlug = slug($catTitle);
			                                //insert into database
                        			        $stmt = $db->prepare('UPDATE blog_cats SET catTitle = :catTitle, catSlug = :catSlug WHERE catID = :catID') ;
                                			$stmt->execute(array(
                                        			':catTitle' => $catTitle,
                                        			':catSlug' => $catSlug,
                                        			':catID' => $catID
                                			));
			                                //redirect to index page
                        			        header('Location: /admin/categories.php?action=updated');
                                			exit;
                        			} 
						catch(PDOException $e) {
                            				echo $e->getMessage();
                        			}
			                }
			        }

        			//check for any errors
        			if(isset($error)){
                			foreach($error as $error){
                        			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                			}
        			}

                		try {
		                        $stmt = $db->prepare('SELECT catID, catTitle FROM blog_cats WHERE catID = :catID') ;
                		        $stmt->execute(array(':catID' => $_GET['id']));
                        		$row = $stmt->fetch();
		                } 
				catch(PDOException $e) {
                    			echo $e->getMessage();
                		}
			        ?>

        			<form class="form-group py-2 px-2" action="" method="post">
					<div class="row">
						<div class="col">
                					<input type='hidden' name='catID' value='<?php echo $row['catID'];?>'>
                					<label for="catTitle">Titre</label>
                					<input class="form-control" type='text' name='catTitle' value='<?php echo $row['catTitle'];?>' required>
						</div>
					</div>

					<br>
					<p class="text-right">
						<button class="btn btn-primary mb-2 mt-3" type="submit" name="submit">Envoyer</button>
                        			<button class="btn btn-primary ml-3 mb-2 mt-3" type="reset">Annuler</button>
					</p>
        			</form>

			</div> <!-- //col-sm-9 -->
			
			<!-- sidebar -->
                        <?php include_once '../includes/sidebar.php'; ?>

		</div> <!-- //row -->

		<!-- footer -->
        	<?php include_once '../includes/footer.php'; ?>

	</div> <!-- //container coprs -->

</div> <!-- //container global -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

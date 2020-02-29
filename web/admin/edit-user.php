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
$pagetitle = 'Admin : édition du profil de '.$_SESSION['username'];
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

				<?php
				// Activation du compte du membre
				if(isset($_GET['action']) && $_GET['action'] == 'activer'){
					$stmt = $db->prepare('UPDATE blog_members SET active = "yes" WHERE memberID = :memberID') ;
                			$stmt->execute(array(
                				':memberID' => html($_GET['id'])
                			));
   					echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">Le compte du membre a été activé avec succès.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><br>';
		}

				// Désactivation du compte du membre
        			if(isset($_GET['action']) && $_GET['action'] == 'desactiver'){
        				$stmt = $db->prepare('UPDATE blog_members SET active = NULL WHERE memberID = :memberID') ;
                			$stmt->execute(array(
                				':memberID' => html($_GET['id'])
                			));
                			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">Le compte du membre a été désactivé avec succès.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        			}
        			?>

				<p><a href="/admin/users.php">Liste des membres</a></p>
			        <h4>Edition du profil membre</h4>
				<?php
        			//if form has been submitted process it
        			if(isset($_POST['submit'])){
					//collect form data
                			extract($_POST);
					//very basic validation
                			if($username ==''){
                        			$error[] = 'Veuillez entrer un pseudo.';
                			}

					if( strlen($password) > 0){
                        			if($password ==''){
                                			$error[] = 'Veuillez entrer un mot de passe.';
                        			}
                        			if($passwordConfirm ==''){
                                			$error[] = 'Veuillez confirmer le mot de passe.';
                        			}
			                        if($password != $passwordConfirm){
                                			$error[] = 'Les mots de passe ne concordent pas.';
                        			}
			                }

					if($email ==''){
                        			$error[] = 'Veuillez entrer une adresse e-mail.';
                			}

					if(!isset($error)){
						try {
							if(isset($password)){
								$hashedpassword = $user->password_hash($password, PASSWORD_BCRYPT);
								//update into database
								$stmt = $db->prepare('UPDATE blog_members SET username = :username, password = :password, email = :email WHERE memberID = :memberID') ;
                                        			$stmt->execute(array(
                                                			':username' => $username,
                                                			':password' => $hashedpassword,
                                                			':email' => $email,
                                                			':memberID' => $memberID
                                        			));

							} 
							
							else {
			                                        //update database
                        			                $stmt = $db->prepare('UPDATE blog_members SET username = :username, email = :email WHERE memberID = :memberID') ;
                                        			$stmt->execute(array(
                                                			':username' => $username,
                                                			':email' => $email,
                                                			':memberID' => $memberID
                                        			));
                                			}

							//redirect to index page
                                			header('Location: /admin/users.php?action=updated');
                                			exit;
						}
						catch(PDOException $e) {
                            				echo $e->getMessage();
                        			}
					}//if !isset error	
        			}//if isset post submiy

				//check for any errors
        			if(isset($error)){
                			foreach($error as $error){
                        			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                			}
        			}

				try {
                        		$stmt = $db->prepare('SELECT memberID, username, email, active FROM blog_members WHERE memberID = :memberID') ;
                        		$stmt->execute(array(':memberID' => $_GET['id']));
                        		$row = $stmt->fetch();
		                }
				catch(PDOException $e) {
                    			echo $e->getMessage();
                		}
			        ?>

				<div class="container card bg-light">
				<form class="form-group pt-4" action="" method="post">
					<div class="row">
						<div class="col-sm-6">
                					<input type='hidden' name='memberID' value='<?php echo $row['memberID'];?>'>
							<label for="username">Pseudo</label>
						</div>
						<div class="col-sm-6">
                					<input type='text' name='username' value='<?php echo $row['username'];?>'>
                				</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label for="password">Mot de passe (seulement en cas de changement)</label>
						</div>
						<div class="col-sm-6">
                					<input type='password' name='password' value=''>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label for="passwordConfirm">Confirmez le mot de passe</label>
						</div>
						<div class="col-sm-6">
                					<input type='password' name='passwordConfirm' value=''>
                				</div>
					</div>
					<div class="row">
                                                <div class="col-sm-6">
							<label for="email">E-mail</label>
						</div>
						<div class="col-sm-6">
                					<input type='text' name='email' value='<?php echo $row['email'];?>'>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<label>Statut du compte</label> :
							<?php
							if($row['active'] == 'yes') {
								echo '<span style="color:green; font-weight:bold;">Actif</span>';
							}
							else {
								echo '<span style="color:red; font-weight:bold;">Inactif</span>';	
							}
							if($row['active'] != 'yes') {
								echo '&nbsp;&nbsp;<a class="button small green" href="/admin/edit-user.php?id='.$row['memberID'].'&action=activer">(Activer le compte)</a>';
							}
						 	if($row['active'] == 'yes') {
                                				echo '&nbsp;&nbsp;<a class="button small red" href="/admin/edit-user.php?id='.$row['memberID'].'&action=desactiver">(Désactiver le compte)</a>';
                        				}
                        				?>
						</div>
					</div>

                			<p class="text-right">
						<button class="btn btn-primary mb-2 mt-3" type="submit" name="submit">Mise à jour du profil membre</button>
                        			<button class="btn btn-secondary ml-3 mb-2 mt-3" type="reset">Annuler</button>
					</p>
        			</form>
				</div>

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

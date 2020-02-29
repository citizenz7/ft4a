<?php
include_once '../includes/config.php';
//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: ../login.php?action=connecte');
}

if(isset($_SESSION['userid']) && $_SESSION['userid'] != 1) {
        header('Location: ../');
}

// titre de la page
$pagetitle= 'Admin : ajouter un membre';
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

				<h4>Ajouter un membre</h4>
				<?php
        			//if form has been submitted process it
        			if(isset($_POST['submit'])){
		                //collect form data
                		extract($_POST);
		                //very basic validation
                		if($username ==''){
                        		$error[] = 'Veuillez entrer un pseudo.';
                		}
		                if($password ==''){
                		        $error[] = 'Veuillez entrer un mot de passe.';
                		}
		                if($passwordConfirm ==''){
                		        $error[] = 'Veuillez confirmer le mot de passe.';
                		}
		                if($password != $passwordConfirm){
               			        $error[] = 'Les mots de passe concordent pas.';
                		}
		                if($email ==''){
                		        $error[] = 'Veuillez entrer une adresse e-mail.';
                		}
		
		
				if(!isset($error)){
		                        $hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);
		                        try {
		                                //insert into database
               			                $stmt = $db->prepare('INSERT INTO blog_members (username,password,email) VALUES (:username, :password, :email)') ;
                                		$stmt->execute(array(
                                        		':username' => $username,
                                        		':password' => $hashedpassword,
                                        		':email' => $email
                                		));
		                                //redirect to index page
                		                header('Location: /admin/users.php?action=ajoute');
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
        		?>

        		<form class="form-group py-2 px-2" action="" method="post">
				<div class="row">
					<div class="col">
                				<label for="username">Pseudo</label>
                				<input class="form-control"  type='text' name='username' value='<?php if(isset($error)){ echo html($_POST['username']);}?>' required>
						<br>
                				<label for="password">Mot de passe</label>
                				<input class="form-control"  type='password' name='password' value='<?php if(isset($error)){ echo html($_POST['password']);}?>' required>
						<br>
                				<label for="passwordConfirm">Confirmation mot de passe</label>
				                <input class="form-control"  type='password' name='passwordConfirm' value='<?php if(isset($error)){ echo html($_POST['passwordConfirm']);}?>' required>
						<br>
                				<label for="email">E-mail</label>
                				<input class="form-control"  type='text' name='email' value='<?php if(isset($error)){ echo html($_POST['email']);}?>' required>
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

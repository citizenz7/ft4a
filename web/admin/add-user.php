<?php
require_once '../includes/config.php';

//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: ../login.php?action=connecte');
}

if(isset($_SESSION['userid'])) {
        if($_SESSION['userid'] != 1) {
                header('Location: ../login.php?action=pasledroit');
        }
}

// titre de la page
$pagetitle= 'Admin : ajouter un membre';

include_once '../includes/header.php';
include_once '../includes/header-logo.php';
include_once '../includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<?php include_once('menu.php'); ?>

	<div class="first">
	<!-- ### -->

	<br>
	<h2>Ajouter un membre</h2>
	
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

                        } catch(PDOException $e) {
                            echo $e->getMessage();
                        }

                }

        }
        //check for any errors
        if(isset($error)){
                foreach($error as $error){
                        echo '<div class="alert-msg error rnd8">'.$error.'</div>';
                }
        }
        ?>

        <form action='' method='post'>
		<div class="form-input clear">
                <label for="username">Pseudo
                	<input type='text' name='username' value='<?php if(isset($error)){ echo html($_POST['username']);}?>'>
		</label>
		<br>
                <label for="password">Mot de passe
                	<input type='password' name='password' value='<?php if(isset($error)){ echo html($_POST['password']);}?>'>
		</label>
		<br>
                <label for="passwordConfirm">Confirmation mot de passe
                	<input type='password' name='passwordConfirm' value='<?php if(isset($error)){ echo html($_POST['passwordConfirm']);}?>'>
		</label>
		<br>
                <label for="email">E-mail
                	<input type='text' name='email' value='<?php if(isset($error)){ echo html($_POST['email']);}?>'>
		</label>
		</div>

                <br><p>
		<input type='submit' class="button small orange" name='submit' value='Ajouter un membre'>
		&nbsp;
		<input type="reset" class="button small grey" value="Annuler">
		</p>

        </form>


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

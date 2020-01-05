<?php
require_once '../includes/config.php';

//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: /admin/login.php');
}

if(isset($_SESSION['userid'])) {
        if($_SESSION['userid'] != 1) {
                header('Location: ../');
        }
}

// titre de la page
$pagetitle = 'Admin : édition du profil de '.$_SESSION['username'];

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

	<?php
	// Activation du compte du membre
	if(isset($_GET['action']) && $_GET['action'] == 'activer'){
		$stmt = $db->prepare('UPDATE blog_members SET active = "yes" WHERE memberID = :memberID') ;
                $stmt->execute(array(
                	':memberID' => html($_GET['id'])
                ));
   		echo '<div class="alert-msg rnd8 success">Le compte du membre a été activé avec succès.</div><br>';
		}

	// Désactivation du compte du membre
        if(isset($_GET['action']) && $_GET['action'] == 'desactiver'){
        	$stmt = $db->prepare('UPDATE blog_members SET active = NULL WHERE memberID = :memberID') ;
                $stmt->execute(array(
                	':memberID' => html($_GET['id'])
                ));
                echo '<div class="alert-msg rnd8 success">Le compte du membre a été désactivé avec succès.</div><br>';
        }
        ?>

        <p><a href="/admin/users.php">Liste des membres</a></p>

        <h2>Edition du profil membre</h2>

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


                                } else {

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

                        } catch(PDOException $e) {
                            echo $e->getMessage();
                        }

                }

        }

        ?>
        <?php
        //check for any errors
        if(isset($error)){
                foreach($error as $error){
                        echo '<div class="alert-msg error rnd8">'.$error.'</div>';
                }
        }

                try {

                        $stmt = $db->prepare('SELECT memberID, username, email, active FROM blog_members WHERE memberID = :memberID') ;
                        $stmt->execute(array(':memberID' => $_GET['id']));
                        $row = $stmt->fetch();

                } catch(PDOException $e) {
                    echo $e->getMessage();
                }

        ?>

        <form action='' method='post'>
		<div class="form-input clear">
                <input type='hidden' name='memberID' value='<?php echo $row['memberID'];?>'>
                <label for="username">Pseudo
                	<input type='text' name='username' value='<?php echo $row['username'];?>'>
		</label>
                <br><label for="password">Mot de passe (seulement en cas de changement)</label>
                	<input type='password' name='password' value=''>
		</label>
                <br><label for="passwordConfirm">Confirmez le mot de passe
                	<input type='password' name='passwordConfirm' value=''>
		</label>
                <br><label for="email">E-mail
                	<input type='text' name='email' value='<?php echo $row['email'];?>'>
		</label>
		<br><label>Statut du compte</label> :
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
		</label>
		</div>

                <br><br><p class="right">
		<input type='submit' class="button small orange" name='submit' value='Mise à jour du profil membre'>
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

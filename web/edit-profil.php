<?php
include_once 'includes/config.php';

//Si pas connecté pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: /admin/login.php');
}

if(isset($_SESSION['username']) && $_SESSION['username'] != $_GET['membre']) {
	header('Location: ../');
}


// Suppression de l'avatar...
if(isset($_GET['delavatar'])) {

	$delavatar = html($_GET['delavatar']);

	// on supprime le fichier image
	$stmt = $db->prepare('SELECT avatar FROM blog_members WHERE memberID = :memberID');
	$stmt->execute(array(
		':memberID' => $delavatar
	));
	$sup = $stmt->fetch();

	$file = $REP_IMAGES_AVATARS.$sup['avatar']; 
	if (file_exists($file)) {
		unlink($file);
	}

	//puis on supprime l'avatar dans la base
	$stmt = $db->prepare('UPDATE blog_members SET avatar = NULL WHERE memberID = :memberID');
	$stmt->execute(array(
                ':memberID' => $delavatar
        ));

	if(isset($_SESSION['username'])) {
		header('Location: /profil.php?action=ok&membre='.html($_SESSION['username']));
	}
}

// titre de la page
if(isset($_SESSION['username'])) {
	$pagetitle = 'Edition du profil de '.html($_SESSION['username']);
}

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
	<!-- ### -->

	<h2>Edition du profil membre de <?php echo html($_GET['membre']); ?></h2>

        <?php
	$username = html($_GET['membre']);


        //if form has been submitted process it
        if(isset($_POST['submit'])) {

		//collect form data
                extract($_POST);

		if(isset($_FILES['avatar']['name']) && !empty($_FILES['avatar']['name'])) {

                	$target_dir = $REP_IMAGES_AVATARS;
                	$target_file = $target_dir . basename($_FILES["avatar"]["name"]);
                	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

                	if ($_FILES['avatar']['error'] > 0) {
                        	$error[] = 'Erreur lors du transfert de l\'avatar membre.';
                	}

                	// On cherche si l'image n'existe pas déjà sous ce même nom
                	if (file_exists($target_file)) {
                        	$error[] = 'Désolé, cet avatar membre existe déjà. Veillez en choisir un autre ou tout simplement changer son nom.';
                	}

                	// Poids de l'image
                	if ($_FILES['avatar']['size'] > $MAX_SIZE_AVATAR) {
                        	$error[] = 'Avatar membre trop gros. Taille maxi : '.makesize($MAX_SIZE_AVATAR);
                	}

                	// format de l'image
                	if($imageFileType != "jpg" && $imageFileType != "png") {
                        	$error[] = 'Désolé : seuls les fichiers jpg et png sont autorisés !';
                	}

                	// Dimensions de l'image
                	$image_sizes = getimagesize($_FILES['avatar']['tmp_name']);
                	if ($image_sizes[0] > $WIDTH_MAX_AVATAR OR $image_sizes[1] > $HEIGHT_MAX_AVATAR) {
                        	$error[] = 'Avatar trop grand : '.$WIDTH_MAX_AVATAR.' x '.$HEIGHT_MAX_AVATAR.' maxi !';
                	}

                	// on vérifie que c'est bien une image
                	if($image_sizes == false) {
                        	$error[] = 'L\'image envoyée n\'est pas une image !';
                	}

                	// on upload l'image s'il n'y a pas d'erreur
                	if(!isset($error)) {
				$avatarmembre = $username . '-avatar-' . $_FILES['avatar']['name'];
                        	//if(!move_uploaded_file($_FILES['avatar']['tmp_name'], $REP_IMAGES_AVATARS.$_FILES['avatar']['name'])) {
				if(!move_uploaded_file($_FILES['avatar']['tmp_name'], $REP_IMAGES_AVATARS.$avatarmembre)) {
                                	$error[] = 'Problème de téléchargement de l\'avatar membre.';
                        	}
                	}

		}//fin de if(isset($_FILES['avatar']['name']))

		//-------------------------------------------------------
		//On ne touche pas au pseudo qui ne peut être changé
		//-------------------------------------------------------

		/*
                if($username ==''){
                        $error[] = 'Veuillez entrer un pseudo.';
                }

		// On cherche si le pseudo fait moins de 4 caractères et s'il est déjà dans la base
                if (strlen($_POST['username']) < 4) {
                        $error[] = 'Le pseudo est trop court ! (4 caractères minimum)';
                }

		// Le username ne peut pas contenir de caractères spéciaux, balises, etc.
                if (!preg_match("/^[a-zA-Z0-9]+$/",$username)) {
                        $error[] = 'Le pseudo ne peut contenir que des lettres et des chiffres !';
                }
		*/

		// On vérifie le mot de passe
		if(!empty($password)) {

			if(strlen($password) < 6) {
                        	$error[] = 'Le mot de passe est trop court ! (6 caractères minimum)';
                	}

                	if($passwordConfirm ==''){
                        	$error[] = 'Veuillez confirmer le mot de passe.';
                	}

                	if($password != $passwordConfirm){
                        	$error[] = 'Les mots de passe ne concordent pas.';
                	}
		}

		// On vérifie l'adresse e-mail
                if($email =='') {
                        $error[] = 'Veuillez entrer une adresse e-mail.';
                }

                if(!isset($error)) {

                        try {
                                if(isset($password) && !empty($password)){

                                        $hashedpassword = $user->password_hash($password, PASSWORD_BCRYPT);

                                        //Mise à jour de la base avec le nouveau mot de passe
                                        $stmt = $db->prepare('UPDATE blog_members SET password = :password, email = :email WHERE username = :username') ;
                                        $stmt->execute(array(
                                                ':username' => $username,
                                                ':password' => $hashedpassword,
                                                ':email' => $email
                                        ));
                                }

				elseif(isset($_FILES['avatar']['name']) && !empty($_FILES['avatar']['name'])) {
					//Mise à jour de la base avec le nouvel avatar 
                                        $stmt = $db->prepare('UPDATE blog_members SET email = :email, avatar = :avatar WHERE username = :username') ;
                                        $stmt->execute(array(
                                                ':username' => $username,
                                                ':avatar' => $avatarmembre,
						':email' => $email
                                        ));
				}			

				else {
                                        //Mise à jour de la base avec adresse e-mail seulement. Aucun nouveau mot de passe n'a été soumis ni aucun avatar
                                        $stmt = $db->prepare('UPDATE blog_members SET email = :email WHERE username = :username') ;
                                        $stmt->execute(array(
                                                ':username' => $username,
                                                ':email' => $email
                                        ));
                                }

				write_log('<span class="orange bold">Edition profil utilisateur :</span> '.$username, $db);

                                //redirect to page
                                header('Location: '.SITEURL.'/profil.php?action=ok&membre='.$username);
                                exit;

				$stmt->closeCursor();

                        }

			catch(PDOException $e) {
                        	echo $e->getMessage();
                        }

                }

        }

        //check for any errors
        if(isset($error)) {
                foreach($error as $error) {
                        echo '<div class="alert-msg error rnd8">'.$error.'</div>';
                }
        }

                try {

                        $stmt = $db->prepare('SELECT memberID,username,email,avatar FROM blog_members WHERE username = :username') ;
                        $stmt->execute(array(':username' => $username));
                        $row = $stmt->fetch();

                }

		catch(PDOException $e) {
                    echo $e->getMessage();
                }

        ?>

        <form class="rnd5" action="" method="post" enctype="multipart/form-data">
	<div class="form-input clear">
		 <label for="username">Pseudo <span class="font-tiny">(ne peut être changé. Sinon, recréez un compte)</span>
                	<input style="width:250px;" type="text" name="username" value="<?php echo html($row['username']); ?>">
		</label>
		<br>
                <label for="password">Mot de passe <span class="font-tiny">(seulement en cas de changement - 6 caractères minimum)</span>
                	<input style="width:250px;" type="password" name="password" value="">
		</label>
		<br>
                <label for="passwordConfirm">Confirmez le mot de passe
                	<input style="width:250px;" type="password" name="passwordConfirm" value="">
		</label>
		<br>
                <label for="email">E-mail
                	<input style="width:250px;" type="text" name="email" value="<?php echo html($row['email']);?>">
		</label>
		<br>
		<label for="avatar">Avatar <span class="font-tiny">(PNG ou JPG | max. <?php echo makesize($MAX_SIZE_AVATAR); ?> | max. <?php echo $WIDTH_MAX_AVATAR; ?> x <?php echo $HEIGHT_MAX_AVATAR; ?> pix.)</span>
                	<input style="width:350px;" type="file" name="avatar">
		</label>
		<br><br>Avatar actuel :
		<?php
		if(!empty($row['avatar']) && file_exists($REP_IMAGES_AVATARS.$row['avatar'])) {
			echo '<img style="max-width: 100px;" src="/images/avatars/'.html($row['avatar']).'" alt="Avatar de '.html($row['username']).'" />';
			?>
			<a href="javascript:delavatar('<?php echo html($row['memberID']);?>','<?php echo html($row['avatar']);?>')"><i class="fas fa-trash-alt"></i> Supprimer</a>
			<?php
			}
			else {
				echo '<img style="max-width:100px;" src="/images/noimage.png" alt="Pas d\'avatar pour '.html($row['username']).'" />';
			}
			?>
	</div>		
		<br>
		<p class="right">
			<input type="submit" class="button small orange" name="submit" value="Mettre à jour">
			&nbsp;
			<input type="reset" class="button small grey" value="Annuler">
		</p>

        </form>


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

<?php
require_once '../includes/config.php';

try {
        $stmt = $db->prepare('SELECT * FROM blog_infos WHERE infoID = :infoID');
        $stmt->execute(array(':infoID' => $_GET['id']));
        $rowpost = $stmt->fetch();
}

catch(PDOException $e) {
        echo $e->getMessage();
}

//Si pas connecté : on renvoie sur page principale 
if(!$user->is_logged_in()) {
        header('Location: ../login.php');
}

// -----------------------------------------------------------------------------------------------
// si c'est l'admin, on donne les droits d'édition
if(isset($_SESSION['username']) && isset($_SESSION['userid'])) {
        if(($_SESSION['userid'] == 1)) {

		// titre de la page
		$pagetitle = 'Admin : édition info '.$rowpost['postTitle'];

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
					<h2>Edition de l'info : <?php echo $rowpost['infoTitle']; ?></h2>

					<?php
					$id = $_GET['id'];

					//if form has been submitted process it
					if(isset($_POST['submit'])) {

						//collect form data
						extract($_POST);

						//very basic validation
						if($infoID == ''){
							$error[] = 'Cette info possède un ID invalide !';
						}

						if($infoTitle == ''){
							$error[] = 'Veuillez entrer un titre.';
						}

						if($infoCont == ''){
							$error[] = 'Veuillez entrer un contenu.';
						}

						if(!isset($error)){
							try {
								$infoSlug = slug($infoTitle);

                                //insert into database
                                $stmt = $db->prepare('UPDATE blog_infos SET infoTitle = :infoTitle, infoSlug = :infoSlug, infoCont = :infoCont WHERE infoID = :infoID') ;
                                $stmt->execute(array(
									':infoTitle' => $infoTitle,
									':infoSlug' => $infoSlug,
									':infoCont' => $infoCont,
									':infoID' => $_GET['id']
                                ));

								//redirect to index page
								header('Location: infos.php');
								exit;

							} // fin de try

							catch(PDOException $e) {
                                echo $e->getMessage();
							}

                        } // fin de if(!isset($error))

					} // fin if(isset($_POST['submit']))

					//check for any errors
					if(isset($error)){
						foreach($error as $error){
							echo '<div class="alert-msg error rnd8">'.$error.'</div>';
						}
					}
					
					try {
						$stmt = $db->prepare('SELECT infoID, infoTitle, infoCont FROM blog_infos WHERE infoID = :infoID') ;
						$stmt->execute(array(
							':infoID' => $id
						));
						$row = $stmt->fetch();
					}

					catch(PDOException $e) {
						echo $e->getMessage();
					}
					?>

					<form action="" method="post" enctype="multipart/form-data">
						<div class="form-input clear">
							<input type="hidden" name="infoID" value="<?php echo html($row['infoID']);?>">
							<label for="infoTitle">Titre
                                <input type="text" name="infoTitle" value="<?php echo html($row['infoTitle']);?>">
							</label>
							<br>
							<label for="infoCont">Contenu
                                <textarea id="editor" name="infoCont" rows="40"><?php echo html($row['infoCont']); ?></textarea>
							</label>
							<br><br>
						</div>

						<br>
						<p class="right">
							<input type='submit' class="button small orange" name='submit' value='Mettre à jour'>
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

}
}

else {
  header('Location: ../');
}
?>

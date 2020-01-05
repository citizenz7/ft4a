<?php
require_once '../includes/config.php';

//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: ../login.php');
}

if(isset($_SESSION['userid'])) {
        if($_SESSION['userid'] != 1) {
                header('Location: ../');
        }
}

// titre de la page
$pagetitle = 'Admin : ajouter une catégorie';

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

	<p><a href="/admin/categories.php">Categories Index</a></p>
	<h2>Ajouter une catégorie</h2>

	<?php
	//if form has been submitted process it
	if(isset($_POST['submit'])){
		$_POST = array_map( 'stripslashes', $_POST );

		//collect form data
		extract($_POST);

		//very basic validation
		if($catTitle ==''){
			$error[] = 'Veuillez entrer un nom de catégorie.';
		}

		if(!isset($error)){

		try {
			$catSlug = slug($catTitle);
			//insert into database
			$stmt = $db->prepare('INSERT INTO blog_cats (catTitle,catSlug) VALUES (:catTitle, :catSlug)') ;
			$stmt->execute(array(
				':catTitle' => $catTitle,
				':catSlug' => $catSlug
			));

			//redirect to index page
			header('Location: /admin/categories.php?action=ajoute');
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
			echo '<div class="alert-msg error rnd8">'.$error.'</div>';
		}
	}
	?>

<form action='' method='post'>
	<div class="form-input clear">
	<label for="catTitle">Titre</label>
		<input type='text' name='catTitle' value='<?php if(isset($error)){ echo html($_POST['catTitle']); } ?>'>
	</label>
	</div>

	<br><p class="right">
	<input type='submit' name='submit' class="button small orange" value='Ajouter la catégorie'>
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

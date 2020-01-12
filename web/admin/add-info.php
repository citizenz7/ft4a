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
$pagetitle = 'Admin : ajouter une info';

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

	<h2>Ajouter une info</h2>

	<?php
	//if form has been submitted process it
	if(isset($_POST['submit'])){
		$_POST = array_map( 'stripslashes', $_POST );
		//collect form data
		extract($_POST);
		//very basic validation
		if($infoTitle ==''){
			$error[] = 'Veillez entrer un titre';
		}
		if($infoCont ==''){
			$error[] = 'Veillez entrer le contenu principal';
		}
		if(!isset($error)){
			try {
				$infoSlug = slug($infoTitle);
				//insert into database
				$stmt = $db->prepare('INSERT INTO blog_infos (infoTitle,infoSlug,infoCont,infoDate) VALUES (:infoTitle, :infoSlug, :infoCont, :infoDate)') ;
				$stmt->execute(array(
					':infoTitle' => $infoTitle,
					':infoSlug' => $infoSlug,
					':infoCont' => $infoCont,
					':infoDate' => date('Y-m-d H:i:s')
				));
				$infoID = $db->lastInsertId();
				//redirect to index page
				header('Location: infos.php?action=ajoute');
				exit;
			} catch(PDOException $e) {
			    echo $e->getMessage();
			}
		}
	}
	//check for any errors
	if(isset($error)){
		foreach($error as $error){
			echo '<p class="error">'.$error.'</p>';
		}
	}
	?>

	<form action='' method='post'>

		<p><label>Titre</label><br />
		<input type='text' name='infoTitle' value='<?php if(isset($error)){ echo $_POST['infoTitle'];}?>'></p>
		<br>
		<p><label>Contenu (texte principal)</label>
		<textarea id="editor" name='infoCont' cols='60' rows='20'><?php if(isset($error)){ echo $_POST['infoCont'];}?></textarea></p>

		<br>
		<p class="right">
			<input type='submit' class="button small orange" name='submit' value='Envoyer'>
			&nbsp;
			<input type="reset" class="button small grey" value="Annuler">
		</p>

	</form>
        </div>
		
      </div>

<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

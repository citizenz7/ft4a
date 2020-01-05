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
$pagetitle= 'Admin : Edition des catégories';

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

	<p><a href="/admin/categories.php">Catégories Index</a></p>

        <h2>Edition de la catégorie</h2>
	
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

                        $stmt = $db->prepare('SELECT catID, catTitle FROM blog_cats WHERE catID = :catID') ;
                        $stmt->execute(array(':catID' => $_GET['id']));
                        $row = $stmt->fetch();

                } catch(PDOException $e) {
                    echo $e->getMessage();
                }

        ?>

        <form action='' method='post'>
		<div class="form-input clear">
                <input type='hidden' name='catID' value='<?php echo $row['catID'];?>'>
                <label for="catTitle">Titre
                	<input type='text' name='catTitle' value='<?php echo $row['catTitle'];?>'>
		</label>
		</div>

                <br><p>
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
?>

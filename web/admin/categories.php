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

//show message from add / edit page
if(isset($_GET['delcat'])){

        $stmt = $db->prepare('DELETE FROM blog_cats WHERE catID = :catID') ;
        $stmt->execute(array(':catID' => html($_GET['delcat'])));

        header('Location: /admin/categories.php?action=supprime');
        exit;
}

// titre de la page
$pagetitle= 'Admin : gestion des catégories';

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
        //show message from add / edit page
        if(isset($_GET['action']) && $_GET['action'] == 'supprime'){
                echo '<div class="alert-msg success rnd8">La catégorie a été supprimée avec succès.</div>';
        }
	if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                echo '<div class="alert-msg success rnd8">La catégorie a été ajoutée avec succès.</div>';
        }

        ?>

        <table>
        <thead><tr>
                <th>Titre</th>
                <th>Action</th>
        </tr></thead>
        <?php
                try {
			$pages = new Paginator('10','p');
                        $stmt = $db->query('SELECT catID FROM blog_cats');
			//pass number of records to
			$pages->set_total($stmt->rowCount());

			$stmt = $db->query('SELECT catID, catTitle, catSlug FROM blog_cats ORDER BY catTitle ASC '.$pages->get_limit());

                        while($row = $stmt->fetch()){

                                echo '<tbody><tr>';
                                echo '<td style="width: 77%;">'.html($row['catTitle']).'</td>';
                                ?>

                                <td class="center">
                                        <a href="/admin/edit-category.php?id=<?php echo html($row['catID']);?>"><input type="button" class="button small green" value="Edit."></a> |
                                        <a href="javascript:delcat('<?php echo html($row['catID']);?>','<?php echo html($row['catSlug']);?>')"><input type="button" class="button small red" value="Supp."</a>
                                </td>

                                <?php
                                echo '</tr></tbody>';
                        }

                } catch(PDOException $e) {
                    echo $e->getMessage();
                }
        ?>
        </table>

	<br>
	<p class="right"><a href="/admin/add-category.php"><input type="button" class="button small orange" value="Ajouter une catégorie" /></a></p>

	<?php
		echo $pages->page_links('/admin/categories.php?');
	?>


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

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
if(isset($_GET['delinfo'])){

        $stmt = $db->prepare('DELETE FROM blog_infos WHERE infoID = :infoID') ;
        $stmt->execute(array(':infoID' => html($_GET['delinfo'])));

        header('Location: /admin/infos.php?action=supprime');
        exit;
}

// titre de la page
$pagetitle= 'Admin : gestion des infos';

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
                echo '<div class="alert-msg success rnd8">L\'info a été supprimée avec succès <a class="close" href="#">X</a></div>';
        }
	if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                echo '<div class="alert-msg success rnd8">L\'info a été ajoutée avec succès <a class="close" href="#">X</a></div>';
        }

        ?>

        <table>
        <thead><tr>
                <th>Titre</th>
                <th>Action</th>
        </tr></thead>
        <?php
                try {
			$pages = new Paginator('7','p');
                        $stmt = $db->query('SELECT infoID FROM blog_infos');
			//pass number of records to
			$pages->set_total($stmt->rowCount());

			$stmt = $db->query('SELECT infoID, infoTitle, infoSlug FROM blog_infos ORDER BY infoDate DESC '.$pages->get_limit());

                        while($row = $stmt->fetch()){

                                echo '<tbody><tr>';
                                echo '<td style="width: 77%;">'.html($row['infoTitle']).'</td>';
                                ?>

                                <td class="center">
                                        <a href="/admin/edit-info.php?id=<?php echo html($row['infoID']);?>"><input type="button" class="button small green" value="Edit."></a> |
                                        <a href="javascript:delinfo('<?php echo html($row['infoID']);?>','<?php echo html($row['infoSlug']);?>')"><input type="button" class="button small red" value="Supp."</a>
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
	<p class="right"><a href="/admin/add-info.php"><input type="button" class="button small orange" value="Ajouter une info" /></a></p>

	<?php
		echo $pages->page_links('/admin/infos.php?');
	?>


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

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
if(isset($_GET['dellicence'])){

        $stmt = $db->prepare('DELETE FROM blog_licences WHERE licenceID = :licenceID') ;
        $stmt->execute(array(':licenceID' => $_GET['dellicence']));

        header('Location: /admin/licences.php?action=supprime');
        exit;
}

// titre de la page
$pagetitle= 'Admin : gestion des licences';

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
                echo '<div class="alert-msg success rnd8">La licence a été supprimée avec succès.</div>';
        }
	if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                echo '<div class="alert-msg success rnd8">La licence a été ajoutée avec succès.</div>';
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
                        $stmt = $db->query('SELECT licenceID FROM blog_licences');
			//pass number of records to
			$pages->set_total($stmt->rowCount());

			$stmt = $db->query('SELECT licenceID, licenceTitle, licenceSlug FROM blog_licences ORDER BY licenceTitle ASC '.$pages->get_limit());

                        while($row = $stmt->fetch()){

                                echo '<tbody><tr>';
                                echo '<td style="width: 77%;">'.html($row['licenceTitle']).'</td>';
                                ?>

                                <td class="center">
                                        <a href="/admin/edit-licence.php?id=<?php echo html($row['licenceID']);?>"><input type="button" class="button small green" value="Edit."</a> |
                                        <a href="javascript:dellicence('<?php echo html($row['licenceID']);?>','<?php echo html($row['licenceSlug']);?>')"><input type="button" class="button small red" value="Supp."</a>
                                </td>

                                <?php
                                echo '</tr></tbody>';
                        }

                } catch(PDOException $e) {
                    echo $e->getMessage();
                }
        ?>
        </table>

	<br />
	<p class="right"><a href="/admin/add-licence.php"><input type="button" class="button small orange" value="Ajouter une licence" /></a></p>

	<?php
		echo $pages->page_links('/admin/licences.php?');
	?>

        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

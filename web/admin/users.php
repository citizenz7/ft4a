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
if(isset($_GET['deluser'])){

        //if user id is 1 ignore
        if($_GET['deluser'] !='1'){

		// On supprime l'avatar du membre
                $stmt = $db->prepare('SELECT avatar FROM blog_members WHERE memberID = :memberID');
                $stmt->execute(array(':memberID' => $_GET['deluser']));
                $sup = $stmt->fetch();
                $file = $REP_IMAGES_AVATARS.$sup['avatar'];
                if (!empty($sup['avatar'])) {
                        unlink($file);
                }

		// on supprime le membre
                $stmt = $db->prepare('DELETE FROM blog_members WHERE memberID = :memberID') ;
                $stmt->execute(array(':memberID' => $_GET['deluser']));

		// on supprime les données torrent du membre
		$stmt1 = $db->prepare('DELETE FROM xbt_users WHERE uid = :uid') ;
		$stmt1->execute(array(':uid' => $_GET['deluser']));

		// on supprime les commentaires du membre
		//$delname = html($_GET['delname']);
		//$stmt2 = $db->prepare('DELETE FROM blog_posts_comments WHERE cuser = :cuser') ;
                //$stmt2->execute(array(':cuser' => $delname));

                header('Location: /admin/users.php?action=supprime');
                exit;

        }
}

// titre de la page 
$pagetitle= 'Admin : gestion des membres';

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
        		//show message from add / edit user 
        		if(isset($_GET['action']) && $_GET['action'] == 'supprime'){
                		echo '<div class="alert-msg success rnd8">Le membre a été supprimé avec succès.</div>';
        		}
			if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                                echo '<div class="alert-msg success rnd8">Le membre a été ajouté avec succès.</div>';
                        }
        		?>

        <table>
        <thead><tr>
		<th>ID</th>
                <th>Pseudo</th>
		<th>PID</th>
                <th>Email</th>
		<th class="center">Inscription</th>
		<th>Val.</th>
                <th class="center">Action</th>
        </tr></thead>
        <?php
                try {
			$pages = new Paginator('12','p');

			$stmt = $db->query('SELECT memberID FROM blog_members');

			//pass number of records to
			$pages->set_total($stmt->rowCount());

                        $stmt = $db->query('SELECT memberID,username,pid,email,memberDate,active FROM blog_members ORDER BY memberID DESC '.$pages->get_limit());
                        while($row = $stmt->fetch()){

                                echo '<tbody><tr>';
				echo '<td class="center font-tiny">'.html($row['memberID']).'</td>';
                                echo '<td class="center font-tiny">'.html($row['username']).'</td>';
				echo '<td class="font-tiny">'.html($row['pid']).'</td>';
                                echo '<td class="font-tiny">'.html($row['email']).'</td>';

				sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
				echo '<td class="center" style="font-size:9px;">'.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.'</td>';
                                ?>

				<?php
				echo '<td class="center font-tiny">';
				//	if($row['memberID'] != 32) {
						if($row['active'] == 'yes') {
							echo 'oui';
						}
						elseif($row['active'] != 'yes' || $row['active'] == 'no') {
							echo 'non';
						}
				//	}
				echo '</td>';
				?>

                                <td class="center">
					<?php //if($row['memberID'] != 32) { ?>
                                        	<a href="/admin/edit-user.php?id=<?php echo html($row['memberID']);?>">
						<input type="button" class="button font-tiny green" value="Edit." /></a>
                                        	<?php if($row['memberID'] != 1){?>
                                                	| <a href="javascript:deluser('<?php echo html($row['memberID']);?>','<?php echo html($row['username']);?>')">
							<input type="button" class="button font-tiny red" value="Supp." /></a>
                                        	<?php } ?>
					<?php //} ?>
                                </td>

				</tr></tbody>
                                <?php

                        }

                } catch(PDOException $e) {
                    echo $e->getMessage();
                }
        ?>
        </table>

	<br>

	<?php
	echo $pages->page_links('/admin/users.php?');
	?>

	<p class="right">
		<a href="/admin/add-user.php"><input type="button" class="button small orange" value="Ajouter un membre" /></a>
	</p>


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

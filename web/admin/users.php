<?php
include_once '../includes/config.php';

//Si pas connecté OU si le membre n'est pas admin, pas de connexion à l'espace d'admin --> retour sur la page login
if(!$user->is_logged_in()) {
        header('Location: ../login.php');
}

if(isset($_SESSION['userid']) && $_SESSION['userid'] != 1) {
        header('Location: ../');
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

$pagetitle= 'Admin : gestion des membres';

include_once '../includes/header.php';
?>

<body id="top">

<div class="container">

        <header>

                <!-- titre -->
                <?php include_once '../includes/header-title.php'; ?>

                <!-- navbar -->
                <?php include_once '../includes/navbar.php'; ?>

        </header>

        <div class="container p-3 my-3 border">
                <div class="row">
                        <div class="col-sm-9">

				<?php include_once('menu.php'); ?>

				<?php
        			//show message from add / edit user 
        			if(isset($_GET['action']) && $_GET['action'] == 'supprime'){
                			echo '<div class="alert-msg success rnd8">Le membre a été supprimé avec succès.</div>';
        			}
				if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                                	echo '<div class="alert-msg success rnd8">Le membre a été ajouté avec succès.</div>';
                        	}
        			?>

        			<table class="table table-striped table-bordered table-hover small table-sm">
        				<thead>
						<tr>
							<th>ID</th>
                					<th>Pseudo</th>
							<th>PID</th>
                					<th>Email</th>
							<th class="text-center">Inscription</th>
							<th>Val.</th>
                					<th class="text-center">Action</th>
        					</tr>
					</thead>
        				<?php
                			try {
					$pages = new Paginator('12','p');
					$stmt = $db->query('SELECT memberID FROM blog_members');
					//pass number of records to
					$pages->set_total($stmt->rowCount());
		                        $stmt = $db->query('SELECT memberID,username,pid,email,memberDate,active FROM blog_members ORDER BY memberID DESC '.$pages->get_limit());
					echo '<tbody>';
					while($row = $stmt->fetch()){
                                		echo '<tr>';
						echo '<td class="text-center small align-middle">'.html($row['memberID']).'</td>';
                                		echo '<td class="text-center small align-middle">'.html($row['username']).'</td>';
						echo '<td class="small align-middle">'.html($row['pid']).'</td>';
                                		echo '<td class="small align-middle">'.html($row['email']).'</td>';
						sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
						echo '<td class="text-center small align-middle">'.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.'</td>';
						echo '<td class="text-center small align-middle">';
							//if($row['memberID'] != 32) {
							if($row['active'] == 'yes') {
								echo 'oui';
							}
							else {
								echo 'non';
							}
						echo '</td>';
					?>
                                	<td class="text-center">
						<a href="/admin/edit-user.php?id=<?php echo html($row['memberID']);?>" class="btn btn-primary btn-sm active small" role="button" aria-pressed="true"><i class="fas fa-edit small"></i></a>
						<?php if($row['memberID'] != 1){?>
							<a href="javascript:deluser('<?php echo html($row['memberID']);?>','<?php echo html($row['username']);?>')" class="btn btn-danger btn-sm active small" role="button" aria-pressed="true"><i class="fas fa-trash-alt small"></i></a>
						<?php } ?>
                                	</td>
					</tr>
                                	<?php
                        		}
					echo '</tbody>';

					}
					catch(PDOException $e) {
                    				echo $e->getMessage();
                			}
       	 				?>
        			</table>
				<?php
				echo $pages->page_links('/admin/users.php?');
				?>

				<p class="text-right">
                                        <a href="/admin/add-user.php" class="btn btn-primary btn-sm active small" role="button" aria-pressed="true">Ajouter un membre</a>
                                </p>



			</div> <!-- //col-sm-9 -->
			
			<!-- sidebar -->
                        <?php include_once '../includes/sidebar.php'; ?>

		</div> <!-- //row -->

		<!-- footer -->
        	<?php include_once '../includes/footer.php'; ?>

	</div> <!-- //container coprs -->

</div> <!-- //container global -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

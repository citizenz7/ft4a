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
if(isset($_GET['delcat'])){

        $stmt = $db->prepare('DELETE FROM blog_cats WHERE catID = :catID') ;
        $stmt->execute(array(':catID' => html($_GET['delcat'])));

        header('Location: /admin/categories.php?action=supprime');
        exit;
}
$pagetitle= 'Admin : gestion des catégories';
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
        			//show message from add / edit page
        			if(isset($_GET['action']) && $_GET['action'] == 'supprime'){
                			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">La catégorie a été supprimée avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        			}
				if(isset($_GET['action']) && $_GET['action'] == 'ajoute'){
                			echo '<div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">La catégorie a été ajoutée avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        			}
        			?>
				<table class="table table-striped table-bordered table-hover small table-sm">
        				<thead>
						<tr>
                					<th>Titre</th>
                					<th class="text-center">Action</th>
        					</tr>
					</thead>
        				<?php
                			try {
						$pages = new Paginator('25','p');
                        			$stmt = $db->query('SELECT catID FROM blog_cats');
						//pass number of records to
						$pages->set_total($stmt->rowCount());
						$stmt = $db->query('SELECT catID, catTitle, catSlug FROM blog_cats ORDER BY catTitle ASC '.$pages->get_limit());
						echo '<tbody>';
                        			while($row = $stmt->fetch()){
                                			echo '<tr>';
                                			echo '<td class="align-middle">'.html($row['catTitle']).'</td>';
                                	?>

							<td class="text-center align-middle">
								<!--
                                        			<a href="/admin/edit-category.php?id=<?php echo html($row['catID']);?>"><input type="button" class="button font-tiny green" value="Edit."></a> |
								<a href="javascript:delcat('<?php echo html($row['catID']);?>','<?php echo html($row['catSlug']);?>')"><input type="button" class="button font-tiny red" value="Supp."></a>
								-->
								<a href="/admin/edit-category.php?id=<?php echo html($row['catID']);?>" class="btn btn-primary btn-sm active small" role="button" aria-pressed="true"><i class="fas fa-edit small"></i></a>
								<a href="javascript:delcat('<?php echo html($row['catID']);?>','<?php echo html($row['catSlug']);?>')" class="btn btn-danger btn-sm active small" role="button" aria-pressed="true"><i class="fas fa-trash-alt small"></i></a>
                                			</td>
                                			<?php
                                			echo '</tr>';
						}
						echo '</tbody>';
					} 
					catch(PDOException $e) {
                    				echo $e->getMessage();
                			}
        				?>
        			</table>

				<p class="text-right">
					<a href="/admin/add-category.php" class="btn btn-primary btn-sm active small" role="button" aria-pressed="true">Ajouter une catégorie</a>
				</p>

				<?php
					echo $pages->page_links('/admin/categories.php?');
				?>


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

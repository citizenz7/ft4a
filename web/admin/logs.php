<?php
include_once '../includes/config.php';

if(!$user->is_logged_in()) {
        header('Location: ../login.php?action=connecte');
}

//Il n'y a que l'admin qui accède à cette page
if(isset($_SESSION['userid']) && $_SESSION['userid'] != 1) {
	header('Location: ../');
}

$pagetitle = 'Page de logs du site';

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

				<table class="table table-striped table-bordered table-hover small table-sm">
					<thead>
		  				<th>Id</th>
						<th>IP</th>
						<th>Resquest uri</th>
						<th>Message</th>
						<th>Time</th>
					</thead>

					<?php
					try {
					//Pagination
					$pages = new Paginator('30','p');
					$query = $db->query('SELECT * FROM blog_logs');
					$pages->set_total($query->rowCount());
					//On cherche tous les logs 
					$query = $db->query('SELECT * FROM blog_logs ORDER BY log_id DESC '.$pages->get_limit());
					echo '<tbody>';
					while($logs = $query->fetch()) {
						echo '<tr style="font-size:9px;">';
						echo '<td class="align-middle">'.$logs['log_id'].'</td>';
						echo '<td class="align-middle">'.$logs['remote_addr'].'</td>';
						echo '<td class="align-middle">'.$logs['request_uri'].'</td>';
						echo '<td class="align-middle">'.$logs['message'].'</td>';
						sscanf($logs['log_date'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
						echo '<td class="align-middle">'.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</td>';
						echo '</tr>';
					} //while
					echo '</tbody>';
				echo '</table><br>';

				echo $pages->page_links('/admin/logs.php?');
					}

				catch(PDOException $e) {
					echo $e->getMessage();
				}
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

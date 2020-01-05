<?php
require_once '../includes/config.php';

if(!$user->is_logged_in()) {
        header('Location: ../login.php?action=connecte');
}

//Il n'y a que l'admin qui accède à cette page
if(isset($_SESSION['userid'])) {
        if($_SESSION['userid'] != 1) {
                header('Location: ../login.php?action=pasledroit');
        }
}

$pagetitle = 'Page de logs du site';

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

	<table>
		<thead>
		  	<th>Id</th>
			<th>IP</th>
			<th style="width:40%;">Resquest uri</th>
			<th>Message</th>
			<th style="width:9%;">Date</th>
		</thead>

			<?php
			try {
				//Pagination
				$pages = new Paginator('25','p');

				$query = $db->query('SELECT * FROM blog_logs');
				$pages->set_total($query->rowCount());

				//On cherche tous les logs 
				$query = $db->query('SELECT * FROM blog_logs ORDER BY log_id DESC '.$pages->get_limit());

				while($logs = $query->fetch()) {

					echo '<tbody><tr class="font-tiny">';
						echo '<td>'.$logs['log_id'].'</td>';
						echo '<td>'.$logs['remote_addr'].'</td>';
						echo '<td>'.$logs['request_uri'].'</td>';
						echo '<td>'.$logs['message'].'</td>';
						sscanf($logs['log_date'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
						echo '<td>'.$jour.'-'.$mois.'-'.$annee.'<br>à '.$heure.':'.$minute.':'.$seconde.'</td>';
					echo '</tr></tbody>';

				} //while

				echo '</table><br>';

				echo $pages->page_links('/admin/logs.php?');

			}

			catch(PDOException $e) {
				echo $e->getMessage();
			}
			?>


        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

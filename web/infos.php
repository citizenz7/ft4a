<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? $_GET['id'] : NULL;

$stmt = $db->prepare('SELECT infoID, infoTitle, infoSlug, infoCont, infoDate FROM blog_infos WHERE infoSlug = :infoSlug');
$stmt->bindValue(':infoSlug', $id, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();

$pagetitle = html($row['infoTitle']);

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<div class="wrapper row3">
	<div id="container">
	<!-- ### -->
		<div id="homepage" class="clear">
			<div class="two_third first">
				<div class="first justify">
					<?php
        					echo '<h2>'.html($row['infoTitle']).'</h2>';
						echo '<p class="font-tiny" style="margin-top:-20px;">Posté le : '.date_fr('d-m-Y à H:i:s', strtotime($row['infoDate'])).'</p>';
						echo '<p>'.BBCode2Html($row['infoCont']).'</p>';
						echo '<br><p class="right"><a href="blog.php">Retourner sur le blog</a></p>';
					?>
				</div>

				<div class="divider2"></div>

			</div>

<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

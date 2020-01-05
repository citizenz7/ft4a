<?php
require_once 'includes/config.php';

$pagetitle = 'Messagerie interne';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<div class="first">
	<!-- ### -->

	<?php
	//On affiche le résultat de l'envoi de message interne
	if(isset($_GET['message'])) {
		echo '<div class="alert-msg rnd8 success">Le message a été envoyé avec succès ! <a class="close" href="#">X</a></div>';
	}

	if(isset($_GET['action']) && $_GET['action'] == 'messupprime'){
		echo '<div class="alert-msg rnd8 success">Le message a été supprimé de votre messagerie ! <a class="close" href="#">X</a></div>';
	}

	try {
		$stmt = $db->prepare('SELECT * FROM blog_members,xbt_users WHERE blog_members.memberID = xbt_users.uid AND username = :username');
		$stmt->bindvalue('username', $_GET['membre'], PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch();
	}

	catch(PDOException $e) {
		echo $e->getMessage();
	}
	
	
	$pages = new Paginator('10','m');
	$stmt = $db->prepare('SELECT messages_id FROM blog_messages WHERE messages_id_destinataire = :destinataire');
	$stmt->execute(array(
		':destinataire' => $row['memberID']
	));
	$pages->set_total($stmt->rowCount());

	// on prépare une requete SQL cherchant le titre, la date, l'expéditeur des messages pour le membre connecté
	$stmt = $db->prepare('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_members.username as expediteur, blog_messages.messages_id as id_message, blog_messages.messages_lu FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = :id_destinataire AND blog_messages.messages_id_expediteur = blog_members.memberID ORDER BY blog_messages.messages_date DESC '.$pages->get_limit());
	$stmt->bindValue(':id_destinataire', $row['memberID'], PDO::PARAM_INT);
	$stmt->execute();
	?>

	<p class="bold font-medium">Messagerie interne : 
		<a class="imgr" href="<?php echo SITEURL; ?>/messages_envoyer.php"><input type="button" class="button small orange font-tiny" value="Envoyer un message à un membre" /></a>
	</p>
	<br>

	<table>
	<tr>
	    <thead>
		<th style="width:20%;">Date</th>
		<th>Titre</th>
                <th>Expéditeur</th>
	    </thead>
	</tr>

	<?php
	while($data = $stmt->fetch()){
		echo '<tbody><tr>';
			sscanf($data['messages_date'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        echo '<td class="font-tiny center">le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</td>';
			echo '<td>';
				if($data['messages_lu'] == 0) {
					echo '<span class="fa fa-envelope"></span>&nbsp;';	
				}				
				echo '<a href="'.SITEURL.'/messages_lire.php?id_message='.$data['id_message'].'">'.html(trim($data['messages_titre'])).'</a>';
			echo '</td>';
			echo '<td class="center">'.html(trim($data['expediteur'])).'</td>';
		echo '</tbody></tr>';
	}
	?>
</table>

<?php
	echo '<div class="center">';
		echo $pages->page_links('messagerie.php?membre='.html($row['username']).'&');
	echo '</div>';
?>

        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

<?php
require_once '../includes/config.php';

if(!$user->is_logged_in()) {
        header('Location: login.php');
}

//Il n'y a que l'admin qui accède à cette page
if(isset($_SESSION['userid'])) {
        if($_SESSION['userid'] != 1) {
                header('Location: ../');
        }
}

$pagetitle = 'Message groupé à tous les membres';

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
// on teste si le formulaire a bien été soumis
if (isset($_POST['go']) && $_POST['go'] == 'Envoyer') {

	if (empty($_POST['titre'])) {
                $error[] = 'Veuillez entrer un titre pour votre message.';
        }

	if (empty($_POST['message'])) {
                $error[] = 'Votre message est vide ??!';
        }

	try {

	//On cherche tous les membres sauf l'admin (1) et le Visiteur (32) 
	$getusers = $db->query('SELECT * FROM blog_members WHERE memberID != 1 AND memberID != 32 AND active = "yes"');

	while($result = $getusers->fetch(PDO::FETCH_ASSOC)) {

		$stmt = $db->prepare('INSERT INTO blog_messages (messages_id_expediteur,messages_id_destinataire,messages_date,messages_titre,messages_message) VALUES (:messages_id_expediteur,:messages_id_destinataire,:messages_date,:messages_titre,:messages_message)');
		$stmt->execute(array(
			':messages_id_expediteur' => 1,
			':messages_id_destinataire' => $result['memberID'],
			':messages_date' => date("Y-m-d H:i:s"),
			':messages_titre' => html($_POST['titre']),
			':messages_message' => html($_POST['message'])
		));

	} //while

	header('Location: /admin/index.php?&message=envoye');
	$stmt->closeCursor();
	exit();
	}

	catch(PDOException $e) {
		echo $e->getMessage();
	}
}
?>

<form action="/admin/messages_envoyer_tous.php" method="post">

<h2>Envoyer un message à tous les membres : </h2>

<?php
//S'il y a des erreurs, on les affiche
if(isset($error)){
	foreach($error as $error){
		echo '<div class="alert-msg rnd8 error">ERREUR : '.$error.'</div>';
	}
}
?>

<br>

Titre du message :<br>
<input type="text" name="titre" size="50" value="<?php if (isset($_POST['titre'])) echo html(trim($_POST['titre'])); ?>">
<br>
Message : <textarea rows="15" name="message"><?php if (isset($_POST['message'])) echo trim($_POST['message']); ?></textarea>
<br>
<p class="right">
	<input type="submit" class="button small orange" name="go" value="Envoyer">
	&nbsp;
	<input type="reset" class="button small grey" value="Annuler">
</p>
</form>





        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once '../includes/sidebar.php';
include_once '../includes/footer.php';
?>

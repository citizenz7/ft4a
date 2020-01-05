<?php
require_once 'includes/config.php';

//collect values from the url
$memberID = trim($_GET['x']);
$active = trim($_GET['y']);

//if id is number and the active token is not empty carry on
if(is_numeric($memberID) && !empty($active)){

    //update users record set the active column to Yes where the memberID and active value match the ones provided in the array
    $stmt = $db->prepare("UPDATE blog_members SET active = 'yes' WHERE memberID = :memberID AND active = :active");
    $stmt->execute(array(
        ':memberID' => $memberID,
        ':active' => $active
    ));

    //if the row was updated redirect the user
    if($stmt->rowCount() == 1){

	$stmt = $db->prepare("SELECT username FROM blog_members WHERE memberID = :memberID");
    	$stmt->execute(array(
        	':memberID' => $memberID,
    	));
	$row = $stmt->fetch();

	$username = $row['username'];

        //write log and redirect to login page : success
	write_log('<span style="color:#00cc99; font-weight:bold;">Nouveau membre :</span> '.$username, $db);
        header('Location: /login.php?action=active');
        exit;

    }

    else {
	header('Location: '.SITEURL.'/login.php?action=echec');
        exit;
    }
    
} // /if is_numeric
?>

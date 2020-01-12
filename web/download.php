<?php
require_once 'includes/config.php';

//Pas d'accès direct
if(!isset($_GET['id']) || empty($_GET['id']) || $_GET['id'] == '') {
	header('Location: /torrents.php?action=nodirect');
	exit;	
}
elseif(!is_numeric($_GET['id'])) {
	header('Location: /torrents.php?action=noexist');
        exit;
}

//on détermine l'id du fichier
$fid = html($_GET['id']);

// Si une session membre est lancée, on détermine l'id du membre
if(isset($_SESSION['username'])) {
	$stmt = $db->prepare('SELECT * FROM blog_members WHERE username = :username');
	$stmt->execute(array(':username' => html($_SESSION['username'])));
	$row = $stmt->fetch();
	$uid = $row['memberID'];
}
else {
	// Si non on renvoie sur la page de login
	// il faut avoir un compte actif et être connecté pour télécharger ou uploader
	header('Location: /login.php?action=compte');
        exit;
}

/*
//on recherche le hash dans la base xbt_files
$stmt1 = $db->prepare('SELECT * FROM xbt_files WHERE fid = :fid');
$stmt1->execute(array(':fid' => $fid));
$row1 = $stmt1->fetch();
*/

//on recherche le torrent dans la base blog_posts_seo
$stmt2 = $db->prepare('SELECT * FROM blog_posts_seo WHERE postID = :postID');
$stmt2->execute(array(':postID' => $fid));
$row2 = $stmt2->fetch();

if(empty($row2)) {
	header('Location: /torrents.php?action=noexist');
        exit;
}

$torrent = $row2['postTorrent'];

$torrentfile = $REP_TORRENTS.'/'.$torrent;

// On décode le fichier torrent
$fd = fopen($torrentfile, "rb");
$alltorrent = fread($fd, filesize($torrentfile));
$array = BDecode($alltorrent);
fclose($fd);

//On cherche le pid
$stmt3 = $db->prepare('SELECT * FROM blog_members WHERE memberID = :uid');
$stmt3->execute(array(':uid' => $uid));
$row3 = $stmt3->fetch();

// Il n'y a que les membres qui peuvent télécharger
// pas de userid vide 
if ($row3['pid'] == '') {
	header('Location: ./');
}

/*
if ($row3['pid'] == '00000000000000000000000000000000') {
	$pid = '00000000000000000000000000000000';
}
 */

else {
	$pid = $row3['pid'];
}

$tracker_announce_urls = SITEURL.':'.ANNOUNCEPORT.'/announce';

// On construit la nouvelle announce avec le pid (passkey ...)
$array["announce"] = SITEURL.":".ANNOUNCEPORT."/".$pid."/announce";

$alltorrent=BEncode($array);

// On construit le header
header("Content-Type: application/x-bittorrent");
header('Content-Disposition: attachment; filename="['.SITENAMELONG.']'.$torrent.'"');
print($alltorrent);

if(isset($_SESSION['username'])) {
	write_log('<span class="blue bold">Download torrent :</span> '.html($row2['postTitle']).' par '.html($_SESSION['username']), $db);
}

//else {
//	write_log('<span class="blue bold">Download torrent :</span> '.html($row2['postTitle']).' par Visiteur', $db);
//}

?>

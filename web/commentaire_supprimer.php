<?php
require_once 'includes/config.php';

if(!$user->is_logged_in()) { 
	header('Location: login.php');
}

$cid = isset($_GET['cid']) ? $_GET['cid'] : NULL;
$cid_torrent = isset($_GET['cid_torrent']) ? $_GET['cid_torrent'] : NULL;


$stmt = $db->prepare('SELECT * FROM blog_posts_comments WHERE cid = :cid');
$stmt->bindValue(':cid', $cid, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();

$cuser = $row['cuser'];

if ($_SESSION['username'] == $cuser) {

	$stmt = $db->prepare('DELETE FROM blog_posts_comments WHERE cid = :cid AND cuser = :cuser');
	$stmt->execute(array(
		':cid' => $cid,
		':cuser' => $_SESSION['username']
	));

	$req = $db->prepare('SELECT * FROM blog_posts_comments LEFT JOIN blog_posts_seo ON blog_posts_seo.postID = blog_posts_comments.cid_torrent WHERE cid_torrent = :cid_torrent');
	$req->execute(array(':cid_torrent' => $cid_torrent));
	$slug = $req->fetch();

	header('Location: /' . $slug['postSlug'] . '&action=commsupprime');
	exit();
}
?>

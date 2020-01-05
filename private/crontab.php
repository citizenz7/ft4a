<?php
include_once '../web/includes/sql.php';
$stmt = $db->query('DELETE FROM xbt_announce_log WHERE mtime < UNIX_TIMESTAMP(NOW() - INTERVAL 5 MINUTE)');
?>

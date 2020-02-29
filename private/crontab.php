<?php
require_once '/var/www/ft4a.fr/web/includes/sql.php';
$stmt = $db->query('DELETE FROM xbt_announce_log WHERE mtime < UNIX_TIMESTAMP(NOW() - INTERVAL 10 MINUTE)');
?>

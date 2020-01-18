<?php
require_once '/var/www/ft4a.fr/web/includes/sql.php';

//on supprime les comptes non activés depuis 7 jours
$stmt = $db->query('DELETE FROM blog_members WHERE active != "yes" AND memberDate < NOW() - INTERVAL 7 DAY');
?>

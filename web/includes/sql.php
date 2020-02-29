<?php
define('DBHOST','localhost');
define('DBUSER','xxxxxxxxxxx');
define('DBPASS','xxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('DBNAME','xxxxxxxxxxxxxxxxx');

try {
        $db = new PDO("mysql:host=".DBHOST.";port=8889;dbname=".DBNAME, DBUSER, DBPASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
        //show error
        echo '<p>'.$e->getMessage().'</p>';
        exit;
}
?>

<?php
define('DBHOST','localhost');
define('DBUSER','example');
define('DBPASS','xxxxxxxxxxxxxxxxxxxxxxxx');
define('DBNAME','example_sql');

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

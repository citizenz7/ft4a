<?php
//Sessions
ob_start();
session_start();

//-----------------------------------------------------
//SQL
//-----------------------------------------------------
include_once 'sql.php';

//-----------------------------------------------------
//Paramètres du site
//-----------------------------------------------------
define('SITENAME','ft4a');
define('SITENAMELONG','ft4a.fr');
define('WEBPATH','/var/www/'.SITENAMELONG.'/web/'); //Chemin complet pour les fichiers du site
define('SITESLOGAN','Free Torrents For All');
define('SITEDESCRIPTION','Tracker Bittorrent exclusivement réservé aux media sous licence libre ou licence de libre diffusion');
define('SITEKEYWORDS','bittorrent,torrent,'.SITENAME.'partage,échange,peer,p2p,licence,license,medias,libre,free,opensource,gnu,téléchargement,download,upload,xbt,tracker,php,mysql,linux,bsd,os,système,system,exploitation,debian,arch,fedora,ubuntu,manjaro,mint,film,movie,picture,video,mp3,musique,music,mkv,avi,mpeg,gpl,creativecommons,cc,mit,apache,cecill,artlibre');
define('SITEURL','http://www.'.SITENAMELONG);
define('SITEURLHTTPS','https://www.'.SITENAMELONG);

//-----------------------------------------------------
//MAIL
//-----------------------------------------------------
define('SITEMAIL','contact@example.com');
define('SITEMAILPASSWORD','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('SMTPHOST','mail.example.com');
define('SMTPPORT','587');

define('SITEOWNORNAME','John Doe');
define('SITEAUTOR','jdoe777');
define('SITEOWNORADDRESS','xxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('ANNOUNCEPORT','55555'); //Port pour l'announce
define('SITEVERSION','3.0.4');
define('SITEDATE','19/02/20');
define('COPYDATE','2020');
define('CHARSET','UTF-8');
define('NBTORRENTS','15'); //Nb de torrents sur la page torrents.php

//-----------------------------------------------------
//Torrent and website Settings
//-----------------------------------------------------

//URL + port pour l'announce
$ANNOUNCEURL = SITEURL.':'.ANNOUNCEPORT.'/announce';

//Chemin complet pour le répertoire des images
$REP_IMAGES = '/var/www/'.SITENAMELONG.'/web/images/';

//Paramètres pour le fichier torrent (upload.php)
define('MAX_FILE_SIZE', 1048576); //Taille maxi en octets du fichier .torrent
$WIDTH_MAX = 500; //Largeur max de l'image en pixels
$HEIGHT_MAX = 500; //Hauteur max de l'image en pixels
$REP_TORRENTS = '/var/www/'.SITENAMELONG.'/web/torrents/'; //Répertoire des fichiers .torrents

//Paramètres pour l'icone de présentation du torrent (index.php, edit-post.php, ...)
$WIDTH_MAX_ICON = 150; //largeur maxi de l'icone de présentation dut orrent
$HEIGHT_MAX_ICON = 150; //Hauteur maxi de l'icone de présentation du torrent
$MAX_SIZE_ICON = 30725; //Taille max en octet de l'icone de présentation du torrent (30 Ko)
$REP_IMAGES_TORRENTS = '/var/www/'.SITENAMELONG.'/web/images/imgtorrents/'; //Chemin complet du répertoire des images torrents
$WEB_IMAGES_TORRENTS = 'images/imgtorrents/'; //Chemin web pour les images torrents

//Paramètres pour l'avatar membre (profile.php, edit-profil.php, ...)
$MAX_SIZE_AVATAR = 51200; //Taille max en octets du fichier (50 Ko)
$WIDTH_MAX_AVATAR = 200; //Largeur max de l'image en pixels
$HEIGHT_MAX_AVATAR = 200; //Hauteur max de l'image en pixels
$EXTENSIONS_VALIDES = array( 'jpg' , 'png' ); //extensions d'images valides
$REP_IMAGES_AVATARS = '/var/www/'.SITENAMELONG.'/web/images/avatars/'; //Répertoires des images avatar des membres


// -----------------------------------------------------
// CLASSES
// -----------------------------------------------------

//load classes as needed
function __autoload($class) {
   
   $class = strtolower($class);

   //if call from within assets adjust the path
   $classpath = 'classes/class.'.$class . '.php';
   if ( file_exists($classpath)) {
      require_once $classpath;
   }  
   
   //if call from within admin adjust the path
   $classpath = '../classes/class.'.$class . '.php';
   if ( file_exists($classpath)) {
      require_once $classpath;
   }
   
   //if call from within admin adjust the path
   $classpath = '../../classes/class.'.$class . '.php';
   if ( file_exists($classpath)) {
      require_once $classpath;
   }     
    
}

$user = new User($db); 


//Deconnexion auto au bout de 10 min 
if($user->is_logged_in()) {
	if (isset($_SESSION['time'])) {

                // after 10 minutes (60 sec x 10 = 600) the user gets logged out
                $idletime=600;
                if (time()-$_SESSION['time']>$idletime){
                        header('Location: '.SITEURLHTTPS.'/logout.php?action=deco');
                }
                else {
                        $_SESSION['time'] = time();
                }
        }
        else {
                $_SESSION['time'] = time();
        }
}


//On inclut le fichier de fonctions et les fichiers d'encodage et de décodage des torrents 
require_once('functions.php');
require_once('BDecode.php');
require_once('BEncode.php');

?>
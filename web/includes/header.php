<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="<?php echo CHARSET; ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="/">
  <title><?php echo $pagetitle; ?></title>
  <meta name="author" content="<?php echo SITEAUTOR; ?>">
  <meta name="description" content="<?php echo SITEDESCRIPTION; ?>">
  <meta name="keywords" content="<?php echo SITEKEYWORDS; ?>">

  <?php
  if($user->is_logged_in()) {
  	//Deconnexion auto du membre au bout de 10 min d'inactivité
  	echo '<meta http-equiv="refresh" content="600;url='.SITEURLHTTPS.'/logout.php?action=deco">';
  } ?>

  <link rel="apple-touch-icon" sizes="57x57" href="/images/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/images/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/images/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/images/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/images/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/images/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/images/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/images/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="/images/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
  <link rel="manifest" href="/images/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/images/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <meta property='og:description' content="<?php echo SITEDESCRIPTION; ?>">
  <meta property='og:title' content="<?php echo SITENAMELONG; ?>">
  <meta property='og:type' content='article'>
  <meta property='og:url' content="<?php echo SITEURLHTTPS; ?>">
  <meta property="og:image" content="<?php echo SITEURLHTTPS; ?>/images/logo.png" />
  <meta property="og:site_name" content="<?php echo SITENAMELONG; ?>" />
  <meta property="article:published_time" content="2020-02-14 20:14:58" />
  <meta property="article:modified_time" content="2020-02-16 17:20:44" />

  <meta name="twitter:card" content="summary">
  <meta name="twitter:site" content="@ft4afr">
  <meta name="twitter:title" content="<?php echo SITENAME; ?>">
  <meta name="twitter:description" content="<?php echo SITEDESCRIPTION; ?>">
  <meta name="twitter:creator" content="@citizenz">
  <meta name="twitter:image" content="<?php echo SITEURLHTTPS; ?>/images/logo.png">

  <meta name="robots" content="index, follow"> 
  <link href="<?php echo SITEURLHTTPS; ?>" rel="canonical">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

  <link rel="stylesheet" href="/css/style.css">

  <!-- reCaptcha -->
  <script src="https://www.google.com/recaptcha/api.js?hl=fr"></script>

  <!-- TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/gk4pigiwl5hhx8u98brglmc25uneryx7p7qgg6ou5300mcqa/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

  <!-- Suppression d'un post (torrent) par son auteur -->
  <script language="JavaScript" type="text/javascript">
  function deltorr(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = '../viewpost.php?deltorr=' + id;
    }
  }
  </script>

  <!-- Suppression d'un post (torrent) par l'Admin -->
  <script language="JavaScript" type="text/javascript">
  function delpost(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = '/admin/index.php?delpost=' + id;
    }
  }
  </script>

  <!-- Suppression d'une catégorie par l'Admin -->
  <script language="JavaScript" type="text/javascript">
  function delcat(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = '/admin/categories.php?delcat=' + id;
    }
  }
  </script>

  <!-- Suppression d'une licence par l'Admin -->
  <script language="JavaScript" type="text/javascript">
  function dellicence(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = '/admin/licences.php?dellicence=' + id;
    }
  }
  </script>

  <!-- Suppression d'un membre par l'Admin -->
  <script language="JavaScript" type="text/javascript">
  function deluser(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = '/admin/users.php?deluser=' + id + '&delname=' + title;
    }
  }
  </script>

  <!-- Suppression de l'avatar du membre -->
  <script language="JavaScript" type="text/javascript">
  function delavatar(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = 'edit-profil.php?delavatar=' + id + '&delname=' + title;
    }
  }
  </script>

  <!-- Suppression de l'image du torrent -->
  <script language="JavaScript" type="text/javascript">
  function delimage(id, title) {
    if (confirm("Etes-vous certain de vouloir supprimer '" + title + "'")) {
      window.location.href = '/admin/edit-post.php?delimage=' + id;
    }
  }
  </script>

</head>

<?php
    function getmicrotime(){
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
    $debut = getmicrotime();
?>

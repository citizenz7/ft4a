<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="<?php echo CHARSET; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="x-dns-prefetch-control" content="off">
	<base href="/">
	<title><?php echo $pagetitle; ?></title>
	<meta name="description" content="<?php echo SITEDESCRIPTION; ?>">
	<meta name="keywords" content="<?php echo SITEKEYWORDS; ?>">
	<meta name="author" content="<?php echo SITEAUTOR; ?>">

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

	<link href="layout/styles/main.css" rel="stylesheet" type="text/css" media="all">
	<link href="layout/styles/mediaqueries.css" rel="stylesheet" type="text/css" media="all">
	
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

	<!--[if lt IE 9]>
	<link href="layout/styles/ie/ie8.css" rel="stylesheet" type="text/css" media="all">
	<script src="layout/scripts/ie/css3-mediaqueries.min.js"></script>
	<script src="layout/scripts/ie/html5shiv.min.js"></script>
	<![endif]-->

	<!-- IE9 Placeholder Support -->
	<script src="layout/scripts/jquery.placeholder.min.js"></script>

	<!-- jQuery -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

	<!-- reCaptcha -->
	<script src="https://www.google.com/recaptcha/api.js?hl=fr"></script>

	<!-- Wysibb -->
	<script src="/layout/scripts/wysibb/jquery.wysibb.min.js"></script>
	<link rel="stylesheet" href="/layout/scripts/wysibb/theme/default/wbbtheme.css"/>
	<script src="/layout/scripts/wysibb/lang/fr.js"></script>
	<script>
		$(function() {
		$("#editor").wysibb();
		})
	</script>

	<!-- Password -->	
   	<script type="text/javascript" language="javascript">
	jQuery(document).ready(function() {
		$('#username').keyup(function(){$('#result').html(passwordStrength($('#password').val(),$('#username').val()))})
		$('#password').keyup(function(){$('#result').html(passwordStrength($('#password').val(),$('#username').val()))})
	})
	function showMore()
	{
		$('#more').slideDown()
	}
   	</script>

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

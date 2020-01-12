<!-- ################################### header-logo.php ############################################ -->
<body id="top" class="">
<div class="wrapper row1">
	
<div class="bgded overlay" style="background-image:url('layout/styles/images/back.jpg');">

  <header id="header" class="full_width clear">
    <div id="hgroup" class="logo">
      <h1><i class="fas fa-share-square"></i> <a href="./"><?php echo SITENAMELONG; ?></a></h1>
      <h2><?php echo SITESLOGAN; ?></h2>
    </div>
    <div id="header-contact">
      <ul class="list none">
	    <?php if($pagetitle == 'Bienvenue sur '.SITENAMELONG.' !') {echo '<li class="active">';} else {echo '<li>';} ?><span class="fa fa-home"></span> <a href="./">Accueil</a></li>
	    <?php if($pagetitle == 'Blog : les dernières infos du monde Libre') {echo '<li class="active">';} else {echo '<li>';} ?><span class="far fa-newspaper"></span> <a href="/blog.php">Blog</a></li>
	    <?php if($pagetitle == 'Nous contacter') {echo '<li class="active">';} else {echo '<li>';} ?><i class="fas fa-envelope"></i> <a href="/contact.php">Nous contacter</a></li>
            <?php if($pagetitle == 'A propos') {echo '<li class="active">';} else {echo '<li>';} ?><span class="fa fa-info"></span> <a href="/apropos.php">A propos</a></li>

	   <li><span class="fa fa-lock"></span>&nbsp;<a href="<?php echo SITEURLHTTPS; ?>">Version HTTPS</a></li>
      </ul>
      <div class="fl_right">
	  <a class="font-large" href="https://github.com/citizenz7/ft4a"><i class="fab fa-github"></i></a>&nbsp;&nbsp;
	  <a class="font-large" href="/rss.php"><i class="fas fa-rss"></i></a>
      </div>
    </div> <!-- /header-contact -->
  </header>

</div> <!-- /class bgded overlay -->

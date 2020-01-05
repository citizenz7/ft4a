<!-- ######## header-nav.php ####### -->
<div class="wrapper row2">
  <nav id="topnav">
    <ul class="clear">
	  <?php if($pagetitle == 'Liste des torrents') {echo '<li class="active">';} else {echo '<li>';} ?><a class="font-medium" href="/torrents.php" title="Torrents"><span class="fa fa-download"></span> Liste des torrents</a></li>
	  <?php if($pagetitle == 'Liste des membres') {echo '<li class="active">';} else {echo '<li>';} ?><a class="font-medium" href="/membres.php" title="Membres"><span class="fa fa-user"></span> Liste des membres</a></li>
	  <?php if($pagetitle == 'Stats torrents') {echo '<li class="active">';} else {echo '<li>';} ?><a class="font-medium" href="/stats.php" title="Stats"><span class="fa fa-bar-chart"></span> Stats torrents</a></li>
    </ul>
  </nav>
</div>

</div>

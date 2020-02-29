<nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded">
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">

			<?php
			$page = $_SERVER['REQUEST_URI'];
			?>

			<li <?php if($page=="/index.php" || $page=="/") {echo 'class="nav-link active"';} else {echo 'class="nav-link"';} ?>>
				<a class="nav-link" href="index.php"><i class="fas fa-home"></i> Accueil</a>
			</li>
			<li <?php if($page=="/torrents.php") {echo 'class="nav-link active"';} else {echo 'class="nav-link"';} ?>>
				<a class="nav-link" href="torrents.php"><i class="fas fa-list"></i> Torrents</a>
			</li>     
			<li <?php if($page=="/membres.php") {echo 'class="nav-link active"';} else {echo 'class="nav-link"';} ?>>
				<a class="nav-link" href="membres.php"><i class="fas fa-users"></i> Membres</a>
			</li>
			<li <?php if($page=="/contact.php") {echo 'class="nav-link active"';} else {echo 'class="nav-link"';} ?>>
				<a class="nav-link" href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
			</li>
			<li <?php if($page=="/apropos.php") {echo 'class="nav-link active"';} else {echo 'class="nav-link"';} ?>>
				<a class="nav-link" href="apropos.php"><i class="fas fa-address-card"></i> A propos</a>
			</li>
		</ul>
			
		<!-- recherche -->
		<form class="form-inline" method="post" action="../recherche.php">
			<input class="form-control form-control-sm mr-sm-2" type="search" name="requete" placeholder="Rechercher un torrent&hellip;" aria-label="Search">
			<button class="btn btn-outline-success btn-sm my-sm-2" type="submit"><i class="fas fa-search"></i></button>
		</form>
		
	</div>
</nav> 

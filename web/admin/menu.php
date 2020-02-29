<h4>Administration</h4>

		<?php
		$link = $_SERVER['REQUEST_URI'];
		?>

		<ul class="nav nav-tabs">
			<?php
			if ($link == "/admin/index.php" || $link == "/admin/") {
				echo '<li class="nav-item"><a class="nav-link active" href="/admin/index.php">Liste torrents</a></li>';
			}
			else {
				echo '<li class="nav-item"><a class="nav-link" href="/admin/index.php">Liste torrents</a></li>';
			}
			?>
			<?php
                        if ($link == "/admin/categories.php") {
                                echo '<li class="nav-item"><a class="nav-link active" href="/admin/categories.php">Catégories</a></li>';
                        }
                        else {
                                echo '<li class="nav-item"><a class="nav-link" href="/admin/categories.php">Catégories</a></li>';
                        }
                        ?>

                        <?php
                        if ($link == "/admin/licences.php") {
                                echo '<li class="nav-item"><a class="nav-link active" href="/admin/licences.php">Licences</a></li>';
                        }
                        else {
                                echo '<li class="nav-item"><a class="nav-link" href="/admin/licences.php">Licences</a></li>';
                        }
                        ?>
                        <?php
                        if ($link == "/admin/users.php") {
                                echo '<li class="nav-item"><a class="nav-link active" href="/admin/users.php">Membres</a></li>';
                        }
                        else {
                                echo '<li class="nav-item"><a class="nav-link" href="/admin/users.php">Membres</a></li>';
                        }
                        ?>
                        <?php
                        if ($link == "/admin/messages_envoyer_tous.php") {
                                echo '<li class="nav-item"><a class="nav-link active" href="/admin/messages_envoyer_tous.php">Message à tous</a></li>';
                        }
                        else {
                                echo '<li class="nav-item"><a class="nav-link" href="/admin/messages_envoyer_tous.php">Message à tous</a></li>';
                        }
                        ?>
                        <?php
                        if ($link == "/admin/logs.php") {
                                echo '<li class="nav-item"><a class="nav-link active" href="/admin/logs.php">Logs</a></li>';
                        }
                        else {
                                echo '<li class="nav-item"><a class="nav-link" href="/admin/logs.php">Logs</a></li>';
                        }
                        ?>
		</ul>
<br>

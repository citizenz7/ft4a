<?php
include_once 'includes/config.php';

$pagetitle = 'Liste des membres';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<div class="first">

	<?php
        //Message de création de compte...
        if(isset($_GET['action']) && $_GET['action'] == 'activation'){
		echo '<div class="alert-msg rnd8 success">Merci ! Votre compte est en cours de création.<br>Vous allez recevoir, par e-mail, un lien afin de l\'activer.</div>';
        }

	if(isset($_GET['action']) && $_GET['action'] == 'noexistmember'){
		echo '<div class="alert-msg rnd8 error"><span class="fa fa-warning font-large"></span>&nbsp;Erreur : ce membre n\'existe pas.</div>';
	}
        ?>

	<br>

	<div class="center">
	   <h2>Rechercher un membre</h2>
	   <form class="rnd5" action="recherche_membres.php" method="post" id="rechercher" name="rechercher">
	      <div class="form-input clear">
		 <input type="text" alt="" name="requete" placeholder="Rechercher un membre ...">
	      </div>
	      <br><p>
	      <input type="submit" alt="" value="Rechercher" class="button small orange">
	      &nbsp;
	      <input type="reset" value="Annuler" class="button small grey">
	      </p>
	   </form>
	   <br>
	</div>

	<br>

	<h2>Liste des membres :</h2>

	<table>
        <thead>
	   <tr>
		<th style="width: 52%;"><a href="membres.php?tri=username&ordre=desc">&#x2191;</a>Pseudo<a href="membres.php?tri=username&ordre=asc">&#x2193;</a></th>
		<th><a href="membres.php?tri=memberDate&ordre=desc">&#x2191;</a>Inscription le<a href="membres.php?tri=memberDate&ordre=asc">&#x2193;</a></th>
                <th class="center"><a href="membres.php?tri=uploaded&ordre=desc">&#x2191;</a>Envoyé<a href="membres.php?tri=uploaded&ordre=asc">&#x2193;</a></th>
                <th class="center"><a href="membres.php?tri=downloaded&ordre=desc">&#x2191;</a>Téléchargé<a href="membres.php?tri=downloaded&ordre=asc">&#x2193;</a></th>
		<th class="center">Ratio</th>
	   </tr>
        </thead>

	<tbody>

	<?php
                try {
			// On affiche 15 membres par page
			$pages = new Paginator('15','p');

			$stmt = $db->query('SELECT memberID FROM blog_members');
			$pages->set_total($stmt->rowCount());

			// On met en place le tri
			if(isset($_GET['tri'])) {
                                $tri = html($_GET['tri']);
                        }
                        else {
				$memberID_tri = 'memberID';
                                $tri = html($memberID_tri);
                        }

                        if(isset($_GET['ordre'])) {
                                $ordre = html($_GET['ordre']);
                        }
                        else {
                                $ordre_tri = 'DESC';
				$ordre = html($ordre_tri);
                        }

			// Protection du tri -------------------------
			if (!empty($_GET['tri']) && !in_array($_GET['tri'], array('memberID','username', 'memberDate', 'uploaded', 'downloaded'))) {
				header('Location: index.php');
				exit();
			}

			if (!empty($_GET['ordre']) && !in_array($_GET['ordre'], array('asc','desc','ASC','DESC'))) {
				header('Location: index.php');
				exit();
			}


			// --------------------------------------------

			$stmt = $db->query('SELECT * FROM blog_members,xbt_users WHERE blog_members.memberID=xbt_users.uid AND blog_members.username != "visiteur" AND blog_members.active = "yes" ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit());
                        while($row = $stmt->fetch()) {
				echo '<tr>';
				if (!empty($row['avatar'])) {
					if ($row['memberID'] == 1) {
						$admin = '<span style="color:red; font-style:italic; font-size:10px;">(Admin)</span>';
					}
					else { }
					echo '<td><img src="/images/avatars/'.html($row['avatar']).'" style="width:40px; height:40px;" alt="'.html($row['username']).'" class="left boxholder">&nbsp;<a href="/profil.php?membre='.html($row['username']).'">'.html($row['username']).'</a>'.$admin.'</td>';
				}
				else {
					echo '<td><img src="/images/avatars/avatar.png" style="width:40px; height:40px;" alt="'.html($row['username']).'" class="left boxholder">&nbsp;<a href="/profil.php?membre='.html($row['username']).'">'.html($row['username']).'</a></td>';
				}			

				sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
				echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde.'</td>';
                                echo '<td class="center font-tiny">'.makesize($row['uploaded']).'</td>';
                                echo '<td class="center font-tiny">'.makesize($row['downloaded']).'</td>';

				if (intval($row["downloaded"])>0) {
					$ratio=number_format($row["uploaded"]/$row["downloaded"],2);
				}
				else {
					$ratio='&#8734;';
				}

				echo '<td class="center font-tiny">'.$ratio.'</td>';
                                echo '</tr>';

                        }

                } 

		catch(PDOException $e) {
                    echo $e->getMessage();
                }
        ?>
	</tbody>
        </table>

	<?php
		echo $pages->page_links('membres.php?tri='.$tri.'&ordre='.$ordre.'&');
	?>


	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

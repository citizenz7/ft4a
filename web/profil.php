<?php
include_once 'includes/config.php';

//Si pas connecté OU si le profil n'appartient pas au membre = pas d'accès
if(!$user->is_logged_in()) {
        header('Location: /login.php?action=connecteprofil');
}

if(!isset($_GET['membre'])) {
         header('Location: ./');
}

$stmt = $db->prepare('SELECT * FROM blog_members WHERE username = :username');
$stmt->bindValue(':username', $_GET['membre'], PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();

if($row['username'] == '') {
        header('Location: /membres.php?action=noexistmember');
}

// Il n'y a pas de page profil pour le compte Visiteur
if($_GET['membre'] == 'Visiteur') {
        header('Location: ./');
}


// C'est parti !!!
else {

// titre de la page
$pagetitle = 'Page Profil de '.html($_GET['membre']);

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
	<!-- ### -->

	 <?php
                //On affiche le résultat de l'édition du profil
                if(isset($_GET['action'])){
                        echo '<div class="alert-msg rnd8 success">Votre profil a été mis à jour ! <a class="close" href="#">X</a></div>';
                }

                //On affiche le résultat de l'envoi de message interne
                if(isset($_GET['message'])){
                        echo '<div class="alert-msg rnd8 success">Le message a été envoyé ! <a class="close" href="#">X</a></div>';
                }


                try {
                        $stmt = $db->prepare('SELECT * FROM blog_members,xbt_users WHERE blog_members.memberID = xbt_users.uid AND username = :username');
                        $stmt->bindValue(':username', $_GET['membre'], PDO::PARAM_STR);
                        $stmt->execute();
                        $row = $stmt->fetch();
                }
                catch(PDOException $e) {
                    echo $e->getMessage();
                }
                ?>


        <?php
        if(isset($_SESSION['username']) && $_SESSION['username'] != $_GET['membre']) {
        ?>

	<table>
        	<tr>
                	<th>ID de membre : </th><td><?php echo html($row['memberID']); ?></td>
                        <?php
                        if(empty($row['avatar'])) {
                        ?>
                        	<td rowspan="6" class="center" style="vertical-align:middle;"><img style="max-width:100px;" src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo html($row['username']); ?>" /></td>
                        <?php }
                        else {
                        ?>
                        	<td rowspan="7" class="center" style="vertical-align:middle;"><img style="max-width:100px;" src="/images/avatars/<?php echo html($row['avatar']); ?>" alt="Avatar de <?php echo html($row['username']); ?>" /></td>
                        <?php } ?>
                        </tr>
                        <tr><th>Pseudo :</th><td><?php echo html($row['username']); ?> <a href="/messages_envoyer.php?destid=<?php echo html($row['memberID']); ?>&destuser=<?php echo html($row['username']); ?>"> <i class="fas fa-envelope"></i></span></a>
                        <?php
                        if($row['memberID'] == 1) {
                        	//echo '<span style="font-weight: bold; color: green;"> [ Webmaster ]</span> | Jabber : mumbly_58 AT jabber.fr';
                                echo '<span class="green font-tiny"> [ Webmaster ] </span><span class="font-tiny"> [ <a href="mailto:mumbly_58@jabber.fr">Jabber</a> ]</span>';
                                }
                                ?>
                        </td></tr>

                        <tr><th>Date d'inscription : </th><td>

                        <?php
                        sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        echo 'Le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde;
                        ?>

                        </td></tr>

                        <tr><th>Envoyé :</th><td><?php echo makesize($row['uploaded']); ?></td></tr>
                        <tr><th>Téléchargé :</th><td><?php echo makesize($row['downloaded']); ?></td></tr>

                        <?php
                        //Peer Ratio
                        if (intval($row["downloaded"])>0) {
                                $ratio=number_format($row["uploaded"]/$row["downloaded"],2);
                        }
                        else {
                                $ratio='&#8734;';
                        }
                        ?>

                        <tr><th>Ratio de partage :</th><td><?php echo $ratio; ?></td></tr>
                </table>

                <!-- Historique téléchargements -->
		<br><h2 id="historique">Ses Téléchargements :</h2>
		<table>
        		<?php
        		$pages = new Paginator('5','d');
       	 		$stmt = $db->prepare('SELECT fid FROM xbt_files_users WHERE uid = :uid');
        		$stmt->bindValue(':uid', $row['memberID'], PDO::PARAM_INT);
        		$stmt->execute();

        		$pages->set_total($stmt->rowCount());


        		// Tri de colonnes
        		$tri = 'postTitle';
        		$ordre = 'DESC';

        		if(isset($_GET['tri'])) {
                		// Les valeurs authorisee
                		$columns = array('postTitle','postDate','postTaille','seeders','leechers','xf.completed');
                		$direction = array('ASC','DESC','asc','desc');
                		if(in_array($_GET['tri'],$columns)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                        		$tri = htmlentities($_GET['tri']);
                		}
                		if(isset($_GET['ordre']) and in_array($_GET['ordre'],$direction)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                        		$ordre = htmlentities($_GET['ordre']);
                		}
        		}

        		$stmtorr1 = $db->prepare('
                		SELECT * FROM xbt_files_users xfu
                		LEFT JOIN blog_posts_seo bps ON bps.postID = xfu.fid
                		LEFT JOIN xbt_files xf ON xf.fid = bps.postID
                		WHERE xfu.uid = :uid
                		ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit()
                	);
        		$stmtorr1->execute(array(
                		':uid' => $row['memberID']
        		));
        		?>
                	<thead><tr>
  				<th style="width: 420px;"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        	<th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        	<th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        	<th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        	<th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        	<th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                	</tr></thead>

                <?php
                while($rowtorr = $stmtorr1->fetch()) {
                ?>
                	<tbody><tr>
                         	<td>
                                	<a href="/<?php echo $rowtorr['postSlug']; ?>"><?php echo $rowtorr['postTitle'];?></a>
                        	</td>
                        	<?php
                        	sscanf($rowtorr['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        	echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        	?>
                        	<td class="center font-tiny"><?php echo makesize($rowtorr['postTaille']); ?></td>
                        	<td class="center"><a class="green" href="../peers.php?hash=<?php echo $rowtorr['postHash']; ?>"><?php echo $rowtorr['seeders']; ?></a></td>
                        	<td class="center"><a class="red" href="../peers.php?hash=<?php echo $rowtorr['postHash']; ?>"><?php echo $rowtorr['leechers']; ?></a></td>
                        	<td class="center"><?php echo $rowtorr['completed']; ?></td>
                	</tr></tbody>
                <?php } ?>

	</table>
	<!-- //historique téléchargements -->

<?php
        echo '<div class="center">';
                //echo $pages->page_links('?membre='.$row['username'].'&');
                echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        echo '</div>';
?>

<br>
<!-- Historique uploads -->
        <h2 id="historique">Ses Uploads :</h2>
	<table>
        <?php
        $pages = new Paginator('5','u');
        $stmt = $db->prepare('SELECT postID FROM blog_posts_seo WHERE postAuthor = :postAuthor');
        //$stmt = $db->prepare('SELECT fid FROM xbt_files_users WHERE uid = :uid');
        $stmt->execute(array(
                ':postAuthor' => $row['username']
         ));
        $pages->set_total($stmt->rowCount());

        /*
        // TRI
        if(isset($_GET['tri'])) {
                $tri = htmlentities($_GET['tri']);
        }
        else {
                $tri = 'postID';
        }
        if(isset($_GET['ordre'])) {
                $ordre = htmlentities($_GET['ordre']);
        }
        else {
                $ordre = 'DESC';
        }
        */

        if(isset($_GET['tri'])) {
              // Les valeurs authorisee
              $columns = array('postTitle','postDate','postTaille','seeders','leechers','xf.completed');
              $direction = array('ASC','DESC','asc','desc');
              if(in_array($_GET['tri'],$columns)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                      $tri = htmlentities($_GET['tri']);
              }
              if(isset($_GET['ordre']) and in_array($_GET['ordre'],$direction)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
              		$ordre = htmlentities($_GET['ordre']);
              }
      }

        $stmtorr2 = $db->prepare('
                SELECT * FROM blog_posts_seo
                LEFT JOIN xbt_files xf ON xf.fid = blog_posts_seo.postID
                WHERE blog_posts_seo.postAuthor = :postAuthor
                ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit()
                );
        $stmtorr2->execute(array(
                ':postAuthor' => $row['username']
        ));
        ?>
                <thead><tr>
			<th style="width: 420px;"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        <th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        <th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                </tr></thead>

               <?php
                while($rowtorr2 = $stmtorr2->fetch()) {
                ?>
                <tbody><tr>
                        <td>
                                <a href="/<?php echo $rowtorr2['postSlug']; ?>"><?php echo $rowtorr2['postTitle'];?></a>
                        </td>
                        <?php
                        sscanf($rowtorr2['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        ?>
                        <td class="center font-tiny"><?php echo makesize($rowtorr2['postTaille']); ?></td>
                        <td class="center"><a class="green" href="/peers.php?hash=<?php echo $rowtorr2['postHash']; ?>"><?php echo $rowtorr2['seeders']; ?></a></td>
                        <td class="center"><a class="red" href="/peers.php?hash=<?php echo $rowtorr2['postHash']; ?>"><?php echo $rowtorr2['leechers']; ?></a></td>
                        <td class="center"><?php echo $rowtorr2['completed']; ?></td>
                </tr></tbody>
                <?php } ?>

</table>
<!-- //historique téléchargements -->

<?php
        echo '<div class="center">';
        echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        echo '</div>';
?>

<br />
	<?php
        }// fin if($_SESSION)


        else {
        ?>

                <span class="font-large bold">Profil membre de : <?php echo $row['username']; ?></span>
                <div class="fl_right">
		     [ <span class="fa fa-user"></span>&nbsp;<a href="/edit-profil.php?membre=<?php echo $row['username']; ?>">&nbsp;Editer votre profil</a>
                     &nbsp;|&nbsp;
		     <span class="fa fa-envelope"></span>&nbsp;<a href="/messagerie.php?membre=<?php echo $row['username']; ?>">&nbsp;Messagerie interne</a> ]
			

		</div>
                <br><br>

                <table>
                        <tr>
                                <th>ID de membre : </th><td><?php echo $row['memberID']; ?></td>

                                <?php
                                if(empty($row['avatar'])) {
                                ?>
                                        <td rowspan="7" class="center" style="vertical-align:middle;"><img style="max-width:100px;" src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo $row['username']; ?>" /></td>
                                <?php }
                                else {
                                ?>
                                        <td rowspan="7" class="center" style="vertical-align:middle;"><img style="max-width:100px;" src="/images/avatars/<?php echo $row['avatar']; ?>" alt="Avatar de <?php echo $row['username']; ?>" /></td>
                                <?php } ?>
                        </tr>
                        <tr><th>E-mail : </th><td><?php echo $row['email']; ?></td></tr>
                        <tr><th>Pid : </th><td><?php echo $row['pid']; ?></td></tr>
                        <tr><th>Date d'inscription : </th><td>

                        <?php
                                sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                                echo 'Le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde;
                        ?>
  </td></tr>
                        <tr><th>Envoyé :</th><td><?php echo makesize($row['uploaded']); ?></td></tr>
                        <tr><th>Téléchargé :</th><td><?php echo makesize($row['downloaded']); ?></td></tr>

                        <?php
                        //$ratio = $row['uploaded'] / $row['downloaded'];
                        //$ratio = number_format($ratio, 2);
                        if (intval($row["downloaded"])>0) {
                                $ratio=number_format($row["uploaded"]/$row["downloaded"],2);
                        }
                        else {
                                $ratio='&#8734;';
                        }
                        ?>

                        <tr><th>Ratio de partage :</th><td><?php echo $ratio; ?></td></tr>

                </table>

<br>

<!-- Historique téléchargements -->
        <h2 id="historique">Mes Téléchargements :</h2>
        <?php
        $pages = new Paginator('5','d');
        $stmt = $db->prepare('SELECT fid FROM xbt_files_users WHERE uid = :uid');
        $stmt->execute(array(
                ':uid' => $row['memberID']
         ));

        $pages->set_total($stmt->rowCount());

        /*
        // TRI
        if(isset($_GET['tri'])) {
                $tri = htmlentities($_GET['tri']);
        }
        else {
                $tri = 'postID';
        }
        if(isset($_GET['ordre'])) {
                $ordre = htmlentities($_GET['ordre']);
        }
        else {
                $ordre = 'DESC';
        }
        */

        // Tri de colonnes
                        $tri = 'postDate';
                        $ordre = 'DESC';
                        if(isset($_GET['tri'])) {
              // Les valeurs authorisee
              $columns = array('postTitle','postDate','postTaille','seeders','leechers','xf.completed');
              $direction = array('ASC','DESC','asc','desc');
              if(in_array($_GET['tri'],$columns)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                      $tri = htmlentities($_GET['tri']);
              }
              if(isset($_GET['ordre']) and in_array($_GET['ordre'],$direction)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                                $ordre = htmlentities($_GET['ordre']);
              }
      }

        $stmtorr1 = $db->prepare('
                SELECT * FROM xbt_files_users xfu
                LEFT JOIN blog_posts_seo bps ON bps.postID = xfu.fid
                LEFT JOIN xbt_files xf ON xf.fid = bps.postID
                WHERE xfu.uid = :uid
                ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit()
                );
        $stmtorr1->execute(array(
                ':uid' => $row['memberID']
        ));
        ?>
        <table>
	        <thead><tr>
 			<th style="width: 420px;"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        <th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        <th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        <th style="text-align: center;"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                </tr></thead>

                <?php
                while($rowtorr = $stmtorr1->fetch()) {
                ?>
                <tbody><tr>
                        <td>
                                <a href="/<?php echo $rowtorr['postSlug']; ?>"><?php echo $rowtorr['postTitle'];?></a>
                        </td>
                        <?php
                        sscanf($rowtorr['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        ?>
                        <td class="center font-tiny"><?php echo makesize($rowtorr['postTaille']); ?></td>
                        <td class="center"><a class="green" href="../peers.php?hash=<?php echo $rowtorr['postHash']; ?>"><?php echo $rowtorr['seeders']; ?></a></td>
                        <td class="center"><a class="red" href="../peers.php?hash=<?php echo $rowtorr['postHash']; ?>"><?php echo $rowtorr['leechers']; ?></a></td>
                        <td class="center"><?php echo $rowtorr['completed']; ?></td>
                </tr></tbody>
                <?php } ?>

</table>
<!-- //historique téléchargements -->

<?php
        echo '<div class="center">';
                echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        echo '</div>';
?>
<!-- Historique uploads -->
        <br><h2 id="historique">Mes Uploads :</h2>
        <?php
        $pages = new Paginator('5','u');

        // On initialise la variable
        $sessionuser = isset($_SESSION['username']) ? $_SESSION['username'] : NULL;

        $stmt = $db->prepare('SELECT postID FROM blog_posts_seo WHERE postAuthor = :postAuthor');
        $stmt->bindValue(':postAuthor',$sessionuser,PDO::PARAM_STR);
        $stmt->execute();
        $pages->set_total($stmt->rowCount());


        // Tri de colonnes
        $tri = 'postDate';
        $ordre = 'DESC';

        if(isset($_GET['tri'])) {
                // Les valeurs authorisee
                $columns = array('postTitle','postDate','postTaille','seeders','leechers','xf.completed');
                $direction = array('ASC','DESC','asc','desc');
                if(in_array($_GET['tri'],$columns)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                        $tri = htmlentities($_GET['tri']);
                }
                if(isset($_GET['ordre']) and in_array($_GET['ordre'],$direction)){ //Une des valeurs authorisee, on la set. Sinon ca sera la veleurs par defaut fixee au dessus
                        $ordre = htmlentities($_GET['ordre']);
                }
        }

        $stmtorr2 = $db->prepare('
                SELECT * FROM blog_posts_seo
                LEFT JOIN xbt_files xf ON xf.fid = blog_posts_seo.postID
                WHERE blog_posts_seo.postAuthor = :postAuthor
                ORDER BY '.$tri.' '.$ordre.' '.$pages->get_limit()
                );
        $stmtorr2->execute(array(
                ':postAuthor' => $row['username']
        ));
        ?>
	<table>
                <thead><tr>
  			<th style="width: 420px;"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        <th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        <th><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        <th class="center"><a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                </tr></thead>

               <?php
                while($rowtorr2 = $stmtorr2->fetch()) {
                ?>
                <tbody><tr>
                        <td>
                                <a href="/<?php echo $rowtorr2['postSlug']; ?>"><?php echo $rowtorr2['postTitle'];?></a>
                        </td>
                        <?php
                        sscanf($rowtorr2['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        ?>
                        <td class="center font-tiny"><?php echo makesize($rowtorr2['postTaille']); ?></td>
                        <td class="center"><a class="green" href="../peers.php?hash=<?php echo $rowtorr2['postHash']; ?>"><?php echo $rowtorr2['seeders']; ?></a></td>
                        <td class="center"><a class="red" href="../peers.php?hash=<?php echo $rowtorr2['postHash']; ?>"><?php echo $rowtorr2['leechers']; ?></a></td>
                        <td class="center"><?php echo $rowtorr2['completed']; ?></td>
                </tr></tbody>
                <?php } ?>

</table>
<!-- //historique téléchargements -->

<?php
        echo '<div class="center">';
        //echo $pages->page_links('?membre='.$row['username'].'&');
        echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        echo '</div>';
?>

<br />

 <?php
        }// fin else
        ?>

	</div>

	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';

} // /else

?>

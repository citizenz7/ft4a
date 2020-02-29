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

$pagetitle = 'Page Profil de '.html($_GET['membre']);
include_once 'includes/header.php';
?>

<body id="top">

<div class="container">

        <header>

                <!-- titre -->
                <?php include_once 'includes/header-title.php'; ?>

                <!-- navbar -->
                <?php include_once 'includes/navbar.php'; ?>

        </header>

        <div class="container p-3 my-3 border">
                <div class="row">
                        <div class="col-sm-9">

			<?php
                	//On affiche le résultat de l'édition du profil
			if(isset($_GET['action'])){
				switch ($_GET['action']) {
                			case 'ok':
					$message = '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Votre profil a été mis à jour !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					break;
				}
			}
			
                	//On affiche le résultat de l'envoi de message interne
			if(isset($_GET['message'])){
				switch($_GET['message']) {
					case 'ok':
                        		$message = '<div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">Le message a été envoyé !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					break;
				}
			}

			if(isset($message)) {
				echo $message;
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
                	

			if(isset($_SESSION['username']) && $_SESSION['username'] != $_GET['membre']) {
        		?>

			<table class="table table-striped small">
				<tr>
					<th>ID de membre : </th><td><?php echo html($row['memberID']); ?>
                        		<?php
                        		if(empty($row['avatar'])) {
                        		?>
                        			<td rowspan="6" class="text-center" style="vertical-align:middle;"><img style="max-width:100px;" class="rounded-circle" src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo html($row['username']); ?>" /></td>
                        		<?php }
                        		else {
                        		?>
                        			<td rowspan="7" class="text-center" style="vertical-align:middle;"><img style="max-width:100px;" class="rounded-circle" src="/images/avatars/<?php echo html($row['avatar']); ?>" alt="Avatar de <?php echo html($row['username']); ?>" /></td>
                        		<?php } ?>
                        	</tr>
                        	<tr>
				<th>Pseudo :</th>
					<td><?php echo html($row['username']); ?> <a href="/messages_envoyer.php?destid=<?php echo html($row['memberID']); ?>&destuser=<?php echo html($row['username']); ?>"> <i class="fas fa-envelope"></i></span></a>
                        		<?php
                        		if($row['memberID'] == 1) {
                        			//echo '<span style="font-weight: bold; color: green;"> [ Webmaster ]</span> | Jabber : mumbly_58 AT jabber.fr';
                                		echo '<span class="small alert-success"> [ Admin ] </span><span class="small"> [ <a href="mailto:mumbly_58@jabber.fr">Jabber</a> ]</span>';
                                	}
                                	?>
					</td>
				</tr>
				<tr>
				<th>Date d'inscription : </th>
					<td>
		                        <?php
                        		sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        		echo 'Le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde;
                        		?>
                        		</td>
				</tr>
                        	<tr>
				<th>Envoyé :</th>
					<td><?php echo makesize($row['uploaded']); ?></td>
				</tr>
                        	<tr>
				<th>Téléchargé :</th>
					<td><?php echo makesize($row['downloaded']); ?></td>
				</tr>

                        	<?php
                        	//Peer Ratio
                        	if (intval($row["downloaded"])>0) {
                                	$ratio=number_format($row["uploaded"]/$row["downloaded"],2);
                        	}
                        	else {
                                	$ratio='&#8734;';
                        	}
                        	?>

                        	<tr>
				<th>Ratio de partage :</th>
					<td><?php echo $ratio; ?></td>
				</tr>
                	</table>

			<br>

			<!-- Historique téléchargements -->
			<h4 id="historique">Ses Téléchargements :</h4>
			<table class="table table-striped small">
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

				<thead class="thead-dark">
					<tr>
  						<th style="width: 420px;" class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
					</tr>
				</thead>
				<tbody>
					<?php
                			while($rowtorr = $stmtorr1->fetch()) {
                			?>
					<tr>
                         			<td>
                                			<a href="/<?php echo $rowtorr['postSlug']; ?>"><?php echo $rowtorr['postTitle'];?></a>
                        			</td>
                        			<?php
                        			sscanf($rowtorr['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        			echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        			?>
                        			<td class="center font-tiny"><?php echo makesize($rowtorr['postTaille']); ?></td>
                        			<td class="center"><?php echo $rowtorr['seeders']; ?></td>
                        			<td class="center"><?php echo $rowtorr['leechers']; ?></td>
                        			<td class="center"><?php echo $rowtorr['completed']; ?></td>
					</tr>
                			<?php } ?>
				</tbody>

			</table>
			<!-- //historique téléchargements -->

			<?php
			// Pagination
        		echo '<div class="text-center">';
                	//echo $pages->page_links('?membre='.$row['username'].'&');
                	echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        		echo '</div>';
			?>

			<br>

			<!-- Historique uploads -->
			<h4 id="historique">Ses Uploads :</h4>
			<table class="table table-striped small">
				<?php
        			$pages = new Paginator('5','u');
        			$stmt = $db->prepare('SELECT postID FROM blog_posts_seo WHERE postAuthor = :postAuthor');
        			//$stmt = $db->prepare('SELECT fid FROM xbt_files_users WHERE uid = :uid');
        			$stmt->execute(array(
                			':postAuthor' => $row['username']
         			));
        			$pages->set_total($stmt->rowCount());

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

				<thead class="thead-dark">
					<tr class="text-white">
						<th class="border border-white" style="width: 420px;"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border worder-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                			</tr>
				</thead>
				<tbody>
				<?php
                		while($rowtorr2 = $stmtorr2->fetch()) {
                		?>
					<tr>
                        			<td><a href="/<?php echo $rowtorr2['postSlug']; ?>"><?php echo $rowtorr2['postTitle'];?></a></td>
                        			<?php
                        			sscanf($rowtorr2['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        			echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        			?>
                        			<td class="center font-tiny"><?php echo makesize($rowtorr2['postTaille']); ?></td>
                        			<td class="center"><?php echo $rowtorr2['seeders']; ?></td>
                        			<td class="center"><?php echo $rowtorr2['leechers']; ?></td>
                        			<td class="center"><?php echo $rowtorr2['completed']; ?></td>
                			</tr>
                		<?php } ?>
				</tbody>
			</table>
			<!-- //historique téléchargements -->

			<?php
			//Pagination
        		echo '<div class="text-center">';
        			echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        		echo '</div>';
			?>

			<br>

		<?php
       		 }// fin if($_SESSION)

        	else {
        	?>

			<div class="text-center">
				<div class="alert alert-info">
					<h4>Profil membre de : <?php echo $row['username']; ?></h4>
				</div>	
				<p class="small">
		     			[ <span class="fas fa-user"></span>&nbsp;<a href="/edit-profil.php?membre=<?php echo $row['username']; ?>">&nbsp;Editer votre profil</a>
                     			&nbsp;|&nbsp;
		     			<span class="fas fa-envelope"></span>&nbsp;<a href="/messagerie.php?membre=<?php echo $row['username']; ?>">&nbsp;Messagerie interne</a> ]
				</p>
				
			</div>

			<br>

			<table class="table table-striped small">
				<tr>
					<th>ID de membre : </th>
						<td><?php echo $row['memberID']; ?>
						<?php
                                		if(empty($row['avatar'])) {
                                		?>
                                        		<td rowspan="7" class="text-center" style="vertical-align:middle;"><img style="max-width:150px;" class="rounded-circle" src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo $row['username']; ?>" /></td>
                                		<?php }
                                		else {
                                		?>
                                        		<td rowspan="7" class="text-center" style="vertical-align:middle;"><img style="max-width:150px;" class="rounded-circle" src="/images/avatars/<?php echo $row['avatar']; ?>" alt="Avatar de <?php echo $row['username']; ?>" /></td>
                                		<?php } ?>
				</tr>
				<tr><th>E-mail : </th><td><?php echo $row['email']; ?></td></tr>
                        	<tr><th>Pid : </th><td><?php echo $row['pid']; ?></td></tr>
                        	<tr><th>Date d'inscription : </th><td>
					<?php
                                	sscanf($row['memberDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                                	echo 'Le '.$jour.'-'.$mois.'-'.$annee.' à '.$heure.':'.$minute.':'.$seconde;
                        		?>		
				</tr>
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
        		<h4 id="historique">Mes Téléchargements :</h4>
        		<?php
        		$pages = new Paginator('5','d');
        		$stmt = $db->prepare('SELECT fid FROM xbt_files_users WHERE uid = :uid');
        		$stmt->execute(array(
                		':uid' => $row['memberID']
         		));
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

			<table class="table table-striped table-bordered table-hover small">
				<thead class="thead-dark">
					<tr>
 						<th class="border border-white" style="width: 420px;"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                			</tr>
				</thead>
				<tbody>
				<?php
                		while($rowtorr = $stmtorr1->fetch()) {
               			?>
					<tr>
                        			<td><a href="/<?php echo $rowtorr['postSlug']; ?>"><?php echo $rowtorr['postTitle'];?></a></td>
						<?php
                        			sscanf($rowtorr['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
						?>
						<td class="text-center"><?php echo $jour.'-'.$mois.'-'.$annee; ?></td>
                        			<td class="text-center"><?php echo makesize($rowtorr['postTaille']); ?></td>
                        			<td class="text-center"><?php echo $rowtorr['seeders']; ?></td>
                        			<td class="text-center"><?php echo $rowtorr['leechers']; ?></td>
                        			<td class="text-center"><?php echo $rowtorr['completed']; ?></td>
                			</tr>
                		<?php } ?>
				</tbody>
			</table>
			<!-- //historique téléchargements -->

			<?php
			//Pagination	
        		echo '<div class="text-center">';
                		echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        		echo '</div>';
			?>

			<!-- Historique uploads -->
        		<br><h4 id="historique">Mes Uploads :</h4>
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

			<table class="table table-striped small">
                		<thead class="thead-dark">
					<tr>
  						<th class="border border-white" style="width: 420px;"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=desc">&#x2191;</a>Nom<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTitle&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=desc">&#x2191;</a>Ajouté<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postDate&ordre=asc">&#x2193;</a></th>
                        			<th class="border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=desc">&#x2191;</a>Taille<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=postTaille&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=desc">&#x2191;</a>S<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=seeders&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=desc">&#x2191;</a>L<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=leechers&ordre=asc">&#x2193;</a></th>
                        			<th class="text-center border border-white"><a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=desc">&#x2191;</a>T<a class="text-white" href="profil.php?membre=<?php echo $row['username']; ?>&tri=xf.completed&ordre=asc">&#x2193;</a></th>
                			</tr>
				</thead>
				<tbody>

               			<?php
                		while($rowtorr2 = $stmtorr2->fetch()) {
                		?>
					<tr>
                        			<td><a href="/<?php echo $rowtorr2['postSlug']; ?>"><?php echo $rowtorr2['postTitle'];?></a></td>
                        			<?php
                        			sscanf($rowtorr2['postDate'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
                        			echo '<td class="center font-tiny">'.$jour.'-'.$mois.'-'.$annee.'</td>';
                        			?>
                        			<td class="center font-tiny"><?php echo makesize($rowtorr2['postTaille']); ?></td>
                        			<td class="center"><?php echo $rowtorr2['seeders']; ?></td>
                        			<td class="center"><?php echo $rowtorr2['leechers']; ?></td>
                        			<td class="center"><?php echo $rowtorr2['completed']; ?></td>
                			</tr>
				<?php } ?>
				</tbody>
			</table>
			<!-- //historique téléchargements -->

			<?php
        		echo '<div class="text-center">';
        			//echo $pages->page_links('?membre='.$row['username'].'&');
        			echo $pages->page_links('profil.php?membre='.$row['username'].'&tri='.$tri.'&ordre='.$ordre.'&');
        		echo '</div>';
			?>

			<br />

		<?php
        	}// fin else
        	?>

			</div> <!-- //col-sm-9 -->
			
			<!-- sidebar -->
                        <?php include_once 'includes/sidebar.php'; ?>

		</div> <!-- //row -->

		<!-- footer -->
        	<?php include_once 'includes/footer.php'; ?>

	</div> <!-- //container coprs -->

</div> <!-- //container global -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

<?php
} // /else c'est parti
?>

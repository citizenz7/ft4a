      <!-- ######################################## sidebar.php ######################################## -->
      <div class="one_third">

      <!-- ######## RECHERCHE ########-->
      <h2 class="nospace font-medium push20">Recherche</h2>
      	<div id="search" class="fl_left">
      		<form class="clear" method="post" action="../recherche.php">
        		<fieldset>
          			<legend>Recherche :</legend>
          			<input type="search" name="requete" style="width:280px;" placeholder="Rechercher un torrent&hellip;">
          			<button class="fa fa-search medium" type="submit" title="Recherche"></button>
        		</fieldset>
      		</form>
	</div>
	  
      <!-- ######## MENU ########-->
      <div class="clear push40"></div>

         <h2 class="nospace font-medium push20">Menu</h2>

	<?php
        if($user->is_logged_in() && $_SESSION['userid'] == 1) {
		$query=$db->query('SELECT avatar FROM blog_members WHERE memberID = 1');
		$data = $query->fetch();
		$avatar = html($data['avatar']);
		
		if(empty($data['avatar'])) {
		?>
			<img src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo html($_SESSION['username']); ?>" class="imgr boxholder" style="width-max:100px;">
		<?php 
		}
		else {
		?>	
			<img src="/images/avatars/<?php echo $avatar; ?>" alt="<?php echo html($_SESSION['username']); ?>" class="imgr boxholder">
		<?php
		}
		?>

                <span class="bold">Bienvenue <?php echo html($_SESSION['username']); ?> !</span>
		<br />
	
		<?php
		$stmtmess = $db->query('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_members.username as expediteur, blog_messages.messages_id as id_message FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID AND blog_messages.messages_lu = "0"');
		$nbmessages = $stmtmess->rowCount();

		$stmtnbmess = $db->query('SELECT blog_messages.messages_id, blog_members.memberID FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID');
		$nbstmtnbmess = $stmtnbmess->rowCount();
		?>

		<ul class="list none">	
			<li>
            		<?php
				echo '<i class="fas fa-envelope"></i> <a href="/messagerie.php?membre='.html($_SESSION['username']).'">Messagerie : ';
				if($nbmessages >= 1 ) {
					echo '<i class="fas fa-mail-bulk"></i> ';
				}
				echo '<span>['.$nbmessages.'&nbsp;-&nbsp;'.$nbstmtnbmess.']</span>';
				echo '</a>';
			?>
			</li>
            		<li><i class="fas fa-plus"></i> <a href="/upload.php">Ajouter un torrent</a></li>
            		<li><i class="fas fa-user"></i> <a href="profil.php?membre=<?php echo html($_SESSION['username']); ?>">Profil</a></li>
            		<li><i class="fas fa-users-cog"></i> <a href="/admin">Admin</a></li>
			<li><i class="fas fa-sign-out-alt"></i> <a href="/logout.php">Déconnexion</a></li>
        	</ul>
                <?php }

                elseif($user->is_logged_in()) {

			$session_username = html($_SESSION['username']);
			$query=$db->prepare('SELECT avatar FROM blog_members WHERE username = :session_username');
			$query->bindValue(':session_username',$session_username,PDO::PARAM_STR);
			$query->execute();
			$data = $query->fetch();

			if(empty($data['avatar'])) {
				$avatar = 'avatar-profil.png';
			}
			else {
				$avatar = html($data['avatar']);
			}

                ?>
			<ul class="list none indent">
				<span class="bold">Bienvenue <?php echo $session_username; ?> !</span>
				<img src="/images/avatars/<?php echo $avatar; ?>" alt="<?php echo $session_username; ?>" style="width:80px; height:80px;" class="imgr boxholder">
				<br />

				<?php
				$stmtmess = $db->query('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_members.username as expediteur, blog_messages.messages_id as id_message FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID AND blog_messages.messages_lu = "0"');
				$nbmessages = $stmtmess->rowCount();
			
				$stmtnbmess = $db->query('SELECT blog_messages.messages_id, blog_members.memberID FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID');	
				$nbstmtnbmess = $stmtnbmess->rowCount();
				?>

				<li>
				<?php
				echo '<i class="fas fa-envelope"></i> <a href="/messagerie.php?membre='.$session_username.'">
				Messagerie : ';
				if($nbmessages >= 1 ) {
					echo ' <i class="fas fa-mail-bulk"></i> ';
				}
				echo '[<span class="bold">'.$nbmessages.'</span>&nbsp;-&nbsp;'.$nbstmtnbmess.']';
				echo '</a>';
				?>
				</li>

				<li><i class="fas fa-plus"></i> <a href="/upload.php">Ajouter un torrent</a></li>
				<li><i class="fas fa-user"></i> <a href="/profil.php?membre=<?php echo $session_username; ?>"> Profil</a></li>
				<li><i class="fas fa-sign-out-alt"></i> <a href="/logout.php">Déconnexion</a></li>
			</ul>

                <?php }

                elseif(!$user->is_logged_in()) {
                ?>

                <ul class="list underline">
                        <li><i class="fas fa-sign-in-alt"></i> <a href="/login.php">Connexion</a></li>
                        <li><i class="fas fa-user-plus"></i> <a href="/signup.php"><span class="bold"> Créer un compte </span></a></li>
                </ul>
                <?php } ?>


      <!-- ######## COMMENTAIRES ########-->
      <div class="clear push40"></div>

        <h2 class="nospace font-medium push20">3 derniers commentaires</h2>
	<ul class="font-content list underline indent push50 justify">
	<?php
	// 3 derniers commentaires
	$stmt = $db->query('SELECT blog_posts_seo.postID,blog_posts_seo.postTitle,blog_posts_seo.postSlug,blog_posts_comments.cid,blog_posts_comments.cid_torrent,blog_posts_comments.cadded,blog_posts_comments.ctext,blog_posts_comments.cuser FROM blog_posts_seo,blog_posts_comments WHERE blog_posts_seo.postID = blog_posts_comments.cid_torrent ORDER BY cadded DESC LIMIT 3');
	while($row = $stmt->fetch()){
		sscanf($row['cadded'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
		$max = 80;
		$chaine = $row['ctext'];
		if (strlen($chaine) >= $max) {
			$chaine = substr($chaine, 0, $max);
			$espace = strrpos($chaine, " ");
			$chaine = substr($chaine, 0, $espace).' ...';
		}
		echo '<li>Le '.$jour.'-'.$mois.'-'.$annee.', '.$row['cuser'].' a dit dans <a href="/'.$row['postSlug'].'#commentaires">'.$row['postTitle'].'</a> : <i>"'.html($chaine).'"</i></li>';
	}
	?>
	</ul>
	<div class="clear"></div>
		
	<!-- ### LIENS WEB ###-->
        <h2 class="nospace font-medium push20">Liens web</h2>
        <ul class="list underline push50 indent">
          <li><a href="https://www.citizenz.info">Blog de citizenZ</a></li>
          <li><a href="http://azote.org">Azote.org</a></li>
        </ul>
        <div class="clear">
          <h2 class="nospace font-medium push20">Statistiques du site</h2>
	     <h4 class="font-small">Membres et visiteurs</h4>
	        <ul style="margin-top:-18px;" class="list underline indent">

		<?php
		// NOMBRE DE MEMBRES INSCRITS
		// On ne compte pas le compte visiteur qui porte l'ID 32 et pas les non-validés
		$stmt3 = $db->query('SELECT COUNT(memberID) AS membres FROM blog_members WHERE memberID != 32 AND active = "yes"');
		$row3 = $stmt3->fetch();
		echo '<li>&rsaquo; <span>Membres inscrits :</span> '.html($row3['membres']).'</li>';

		// NOMBRE DE MEMBRES NON VALIDES
		$stmt4 = $db->query('SELECT COUNT(memberID) AS membres FROM blog_members WHERE memberID !=32 AND active != "yes" AND active != "no"');
		$row4 = $stmt4->fetch();
		echo '<li>&rsaquo; <span>A valider :</span> '.html($row4['membres']).'</li>';

		// NOMBRE DE PERSONNES CONNECTEES SUR LE SITE
		$stmt = $db->prepare('SELECT COUNT(*) AS nbre_entrees FROM connectes WHERE ip = :ip ');
		$stmt->execute(array(
			':ip' => $_SERVER['REMOTE_ADDR']
		));
		$donnees = $stmt->fetch();

		// ETPAE 1
		// S'il y a une $_SESSION, c'est un membre connecté
		if(isset($_SESSION['username'])) {
			$stmt2 = $db->prepare('UPDATE connectes SET timestamp = :timestamp, pseudo = :pseudo  WHERE ip = :ip') ;
			$stmt2->execute(array(
				':timestamp' => time(),
				':pseudo' => html($_SESSION['username']),
				':ip' => $_SERVER['REMOTE_ADDR']
			));
		}

		else { // Ou bien il n'y a aucune $_SESSION, ce n'est pas un membre connecté, c'est un "Visiteur"
			$pseudo = 'Visiteur';
			if ($donnees['nbre_entrees'] == 0) { // L'IP ne se trouve pas dans la table, on va l'ajouter.
				$stmt1 = $db->prepare('INSERT INTO connectes VALUES (:ip, :pseudo, :timestamp)');
				$stmt1->execute(array(
					':ip' => $_SERVER['REMOTE_ADDR'],
					':pseudo' => $pseudo,
					':timestamp' => time()
				));
			}

			else { // L'IP se trouve déjà dans la table, on met juste à jour le timestamp.
				$stmt2 = $db->prepare('UPDATE connectes SET timestamp = :timestamp WHERE ip = :ip');
				$stmt2->execute(array(
					':timestamp' => time(),
					':ip' => $_SERVER['REMOTE_ADDR']
				));
			}

		}

		// ÉTAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 5 minutes.
		// On stocke dans une variable le timestamp qu'il était il y a 5 min :
		$timestamp_5min = time() - (60 * 5); // (60 * 5 = nombre de secondes écoulées en 5 minutes)
		$stmt3 = $db->query('DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);

		// ÉTAPE 3 : on compte le nombre d'IP stockées dans la table. C'est le nombre total de personnes connectées.
		$stmt4 = $db->query('SELECT COUNT(*) AS nbre_entrees FROM connectes');
		$donnees = $stmt4->fetch();

		// On affiche le nombre total de connectés
		if ($donnees['nbre_entrees'] < 2) {
			echo '<li>&rsaquo; <span style="font-weight: bold;">Personne connectée :</span> '.$donnees['nbre_entrees'].'</li>';
		}
		else {
			echo '<li>&rsaquo; <span>Personnes connectées :</span> '.$donnees['nbre_entrees'].'</li>';
		}

		// ETAPE 4 : on affiche si c'est un Visiteur ou un Membre (avec son nom de membre)
		// On cherche le nombre de Visiteurs
		$stmt5 = $db->query("SELECT pseudo FROM connectes WHERE pseudo = 'Visiteur'");
		$num = $stmt5->rowCount();

		if($num>0) {
			$i=0;
			while($dn2 = $stmt5->fetch()) {
				$i++;
			}
		}

		echo '<div class="indent">';
			if($num<2) {
				echo '<li><span class="fa fa-user"></span>&nbsp;'.$num.' visiteur</li>';
			}
			else {
				echo '<li><span class="fa fa-user"></span>&nbsp;'.$num.' visiteurs</li>';
			}

			// On cherche le nombre de membres connectés avec leur pseudo
			$stmt6 = $db->query("SELECT pseudo FROM connectes WHERE pseudo != 'Visiteur'");
			$num1 = $stmt6->rowCount();

			if($num1 >= 2) {
				echo '<li><span class="fa fa-user-circle"></span>&nbsp;'.$num1.' membres : ';
			}
			elseif($num1 == 0) {
				echo '<li><span class="fa fa-user-circle"></span>&nbsp;'.$num1.' membre';
			}
			elseif($num1 < 2) {
				echo '<li><span class="fa fa-user-circle"></span>&nbsp;'.$num1.' membre : ';
			}

			$links = array();
			foreach ($stmt6 as $s) {
				$links[] = '<a href="/profil.php?membre='.html($s['pseudo']).'" style="text-decoration: none;">'.html($s['pseudo']).'</a>';
			}
			echo implode(", ", $links);
				echo '</li>';

		echo '</div>';

	/**** compteur de visites ***/
	// ETAPE 1 : on vérifie si l'IP se trouve déjà dans la table
	// Pour faire ça, on n'a qu'à compter le nombre d'entrées dont le champ "ip" est l'adresse ip du visiteur
	$stmt5 = $db->prepare('SELECT COUNT(*) AS nbre_entrees FROM compteur WHERE ip = :adresseip');
	$stmt5->execute(array(
		':adresseip' => $_SERVER['REMOTE_ADDR']
	));
	$donnees2 = $stmt5->fetch();

	if ($donnees2['nbre_entrees'] == 0) { // L'ip ne se trouve pas dans la table, on va l'ajouter
		$stmt6 = $db->prepare('INSERT INTO compteur VALUES (:adresseip, :time)');
		$stmt6->execute(array(
			':adresseip' => $_SERVER['REMOTE_ADDR'],
			':time' => time()
		));
	}
	else { // L'ip se trouve déjà dans la table, on met juste à jour le timestamp
		$stmt7 = $db->prepare('UPDATE compteur SET timestamp = :timestamp WHERE ip = :adresseip');
		$stmt7->execute(array(
			':timestamp' => time(),
			':adresseip' => $_SERVER['REMOTE_ADDR']
		));
	}

	$jour = date('d');
	$mois = date('m');
	$annee = date('Y');
	$aujourd_hui = mktime(0, 0, 0, $mois, $jour, $annee);

	$stmt8 = $db->prepare('SELECT COUNT(*) AS nbre_entrees FROM compteur WHERE timestamp > :timestamp');
	$stmt8->execute(array(
		':timestamp' => $aujourd_hui
	));
	$donnees3 = $stmt8->fetch();
	echo '<li>&rsaquo; Visites aujourd\'hui : '.$donnees3['nbre_entrees'].'</li>';

	$stmt9 = $db->query('SELECT COUNT(*) AS nbre_entrees FROM compteur');
	$donnees4 = $stmt9->fetch();
	echo '<li>&rsaquo; Visites totales : ' . $donnees4['nbre_entrees'].'</li>';

	/**** Fin compteur de visites ****/

	?>

   	</ul>

	<div class="divider5"></div>

	<h2 class="font-small">Tracker bittorent</h2>

	<?php
	$stmt = $db->query('SELECT info_hash, sum(completed) completed, sum(leechers) leechers, sum(seeders) seeders, sum(leechers or seeders) torrents FROM xbt_files');
	$result = $stmt->fetch();
	$result['peers'] = $result['leechers'] + $result['seeders'];

	echo '<table style="margin-top:-18px;">';
		echo '<tr><th>Torrents téléchargés :</th><td class="center">'. $result['completed']. '</td></tr>';
		echo '<tr><th>Clients :</th><td class="center">'. $result['peers']. '</td></tr>';

		if ($result['peers']) {
			   printf('<tr><th><span class="fa fa-download"></span> Leechs :</th><td class="center">%d <span class="font-tiny">(%d %%)</span>', $result['leechers'], $result['leechers'] * 100 / $result['peers'], '</td></tr>');
			   printf('<tr><th><span class="fa fa-upload"></span> Seeds :</th><td class="center">%d <span class="font-tiny">(%d %%)</span>', $result['seeders'], $result['seeders'] * 100 / $result['peers'], '</td></tr>');
		}

		echo '<tr><th>Torrents actifs :</th><td class="center">'. $result['torrents']. '</td></tr>';

		$stmt = $db->query('SELECT postID FROM blog_posts_seo');
		$nbrtorrents =$stmt->rowCount();

		printf('<tr><th>Torrents total :</th><td class="center">%d', $nbrtorrents ,'</td></tr>');

		$stmt = $db->query('SELECT sum(downloaded) as down, sum(uploaded) as up FROM xbt_users');
		$row = $stmt->fetch();

		$dled=makesize($row['down']);
		$upld=makesize($row['up']);
		$traffic=makesize($row['down'] + $row['up']);

		printf('<tr><th>Download total :</th><td class="center">'. $dled. '</td></tr>');
		printf('<tr><th>Upload total :</th><td class="center">'. $upld. '</td></tr>');
		printf('<tr><th>Trafic total :</th><td class="center">'. $traffic. '</td></tr>');

	echo '</table>';
	?>

        </div> <!-- /class clear -->

      </div> <!-- /one_third -->

    <!-- ### content.php ### -->
    </div> <!-- /id homepage -->

    <div class="clear"></div>

  </div> <!-- /id container -->

</div> <!-- /class wrapper row3 -->

			<div class="col-sm-3">

				<?php
				//Si c'est l'admin (memberID = 1) qui se ocnnecte
        			if($user->is_logged_in() && $_SESSION['userid'] == 1) {
					$query=$db->query('SELECT avatar FROM blog_members WHERE memberID = 1');
					$data = $query->fetch();
					$avatar = html($data['avatar']);
					?>

					<ul class="list-group small">

					<?php	
					if(empty($data['avatar'])) {
					?>
						<li class="list-group-item text-center"><img src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo html($_SESSION['username']); ?>" class="rounded-circle" style="max-width:90px;"><br>
					<?php 
					}
					else {
					?>	
						<li class="list-group-item text-center"><img src="/images/avatars/<?php echo $avatar; ?>" alt="<?php echo html($_SESSION['username']); ?>" class="rounded-circle" style="max-width:90px;"><br>
					<?php
					}
					?>
					
        				Bienvenue <?php echo html($_SESSION['username']); ?> !</li>
	
					<?php
					$stmtmess = $db->query('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_members.username as expediteur, blog_messages.messages_id as id_message FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID AND blog_messages.messages_lu = "0"');
					$nbmessages = $stmtmess->rowCount();

					$stmtnbmess = $db->query('SELECT blog_messages.messages_id, blog_members.memberID FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID');
					$nbstmtnbmess = $stmtnbmess->rowCount();
					?>

						<li class="list-group-item"><i class="fas fa-mail-bulk"></i> <a href="/messagerie.php?membre=<?php echo html($_SESSION['username']); ?>">Messagerie</a> 
						<?php
						if ($nbmessages >= 1) {
							echo '<span class="badge badge-pill badge-danger">'.$nbmessages.'</span>';
						}
						else {
							echo '<span class="badge badge-pill badge-success">'.$nbmessages.'</span>';
						}
						?>
					</li>
					<li class="list-group-item"><i class="fas fa-upload"></i> <a href="upload.php">Ajouter un torrent</a></li>
					<li class="list-group-item"><i class="fas fa-user"></i> <a href="profil.php?membre=<?php echo html($_SESSION['username']); ?>">Profil</a></li>
					<li class="list-group-item"><i class="fas fa-user-cog"></i> <a href="/admin">Admin</a></li>
					<li class="list-group-item"><i class="fas fa-sign-out-alt"></i> <a href="logout.php">Déconnexion</a></li>
					</ul>
					<br>
				<?php }

				//Si c'est un membre qui se connecte
				elseif($user->is_logged_in()) {
					$session_username = html($_SESSION['username']);
					$query=$db->prepare('SELECT avatar FROM blog_members WHERE username = :session_username');
					$query->bindValue(':session_username',$session_username,PDO::PARAM_STR);
					$query->execute();
					$data = $query->fetch();
					$avatar = html($data['avatar']);
					?>

					<ul class="list-group small">

					<?php
					if(empty($data['avatar'])) {
                                        ?>
						<li class="list-group-item text-center"><img src="/images/avatars/avatar-profil.png" alt="Pas d'avatar pour <?php echo html($_SESSION['username']); ?>" class="rounded-circle" style="max-width:80px;"><br>
                                        <?php
                                        }
                                        else {
                                        ?>
                                                <li class="list-group-item text-center"><img src="/images/avatars/<?php echo $avatar; ?>" alt="<?php echo html($_SESSION['username']); ?>" class="rounded-circle" style="max-width:80px;"><br>
                                        <?php
                                        }
                                        ?>
					Bienvenue <?php echo html($_SESSION['username']); ?> !
					<br />
					<?php
                                        $stmtmess = $db->query('SELECT blog_messages.messages_titre, blog_messages.messages_date, blog_members.username as expediteur, blog_messages.messages_id as id_message FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID AND blog_messages.messages_lu = "0"');
                                        $nbmessages = $stmtmess->rowCount();

                                        $stmtnbmess = $db->query('SELECT blog_messages.messages_id, blog_members.memberID FROM blog_messages, blog_members WHERE blog_messages.messages_id_destinataire = "'.$_SESSION['userid'].'" AND blog_messages.messages_id_expediteur = blog_members.memberID');
                                        $nbstmtnbmess = $stmtnbmess->rowCount();
                                        ?>

						<li class="list-group-item"><i class="fas fa-mail-bulk"></i> <a href="/messagerie.php?membre=<?php echo html($_SESSION['username']); ?>">Messagerie</a>
                                               	<?php
                                               	if ($nbmessages >= 1) {
                                                       	echo '<span class="badge badge-pill badge-danger">'.$nbmessages.'</span>';
                                               	}
                                               	else {
                                                       	echo '<span class="badge badge-pill badge-success">'.$nbmessages.'</span>';
                                               	}
                                               	?>
                                       	</li>
                                       	<li class="list-group-item"><i class="fas fa-upload"></i> <a href="upload.php">Ajouter un torrent</a></li>
                                       	<li class="list-group-item"><i class="fas fa-user"></i> <a href="profil.php?membre=<?php echo html($_SESSION['username']); ?>">Profil</a></li>
                                       	<li class="list-group-item"><i class="fas fa-sign-out-alt"></i> <a href="logout.php">Déconnexion</a></li>
                                	</ul>
                                	<br>
                                <?php }
				
				elseif(!$user->is_logged_in()) {
				//Si c'est un visiteur (pas connecté)...
                		?>

				<h6>Menu</h6>
                                <ul class="list-group small">
                                        <li class="list-group-item"><i class="fas fa-sign-in-alt"></i> <a href="login.php">Connexion</a></li>
                                        <li class="list-group-item"><i class="fas fa-user-plus"></i> <a href="signup.php">Créer un compte</a></li>
                                </ul>
                                <br>
				<?php } ?>

				<h6>3 derniers commentaires</h6>
				<ul class="list-group small">
					<?php
					// 3 derniers commentaires
					$stmt = $db->query('SELECT blog_posts_seo.postID,blog_posts_seo.postTitle,blog_posts_seo.postSlug,blog_posts_comments.cid,blog_posts_comments.cid_torrent,blog_posts_comments.cadded,blog_posts_comments.ctext,blog_posts_comments.cuser FROM blog_posts_seo,blog_posts_comments WHERE blog_posts_seo.postID = blog_posts_comments.cid_torrent ORDER BY cadded DESC LIMIT 3');
					while($row = $stmt->fetch()){
						$max = 60;
						$chaine = $row['ctext'];
						if (strlen($chaine) >= $max) {
							$chaine = substr($chaine, 0, $max);
							$espace = strrpos($chaine, " ");
							$chaine = substr($chaine, 0, $espace).' ...';
						}
						echo '<li class="list-group-item"><i class="far fa-comment"></i> '.$row['cuser'].' a dit dans <a href="/'.$row['postSlug'].'#commentaires">'.html($chaine).'</a></li>';
					}
					?>
				</ul>
				<br>

				<h6>Liens web</h6>
				<ul class="list-group small">
					<li class="list-group-item">
						<i class="fas fa-link"></i> <a href="https://www.olivierprieur.fr" target="_blank">CV d'Olivier Prieur (aka citizenz)</a><br>
						<i class="fas fa-link"></i> <a href="https://www.citizenz.info" target="_blank">Blog de citizenZ</a><br>
						<i class="fas fa-link"></i> <a href="https://www.clevery.xyz" target="_blank">Plateforme clevery.xyz</a>
					</li>
				</ul>
				<br>

				<h6>Stats du site</h6>
				<ul class="list-group">
					<li class="list-group-item">
						<?php
						// NOMBRE DE MEMBRES INSCRITS
						$stmt3 = $db->query('SELECT COUNT(memberID) AS membres FROM blog_members WHERE active = "yes"');
						$row3 = $stmt3->fetch();
						echo '<span class="small">Membres inscrits :</span> <span class="badge badge-success float-right">'.html($row3['membres']).'</span><br>';

						// NOMBRE DE MEMBRES NON VALIDES
						$stmt4 = $db->query('SELECT COUNT(memberID) AS membres FROM blog_members WHERE memberID !=32 AND active != "yes" AND active != "no"');
						$row4 = $stmt4->fetch();
						echo '<span class="small">A valider :</span> <span class="badge badge-warning float-right">'.html($row4['membres']).'</span><br>';

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
				
						// ÉTAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 10 minutes.
						// On stocke dans une variable le timestamp qu'il était il y a 10 min :
						$timestamp_5min = time() - (60 * 10); // (60 * 10 = nombre de secondes écoulées en 10 minutes)
						$stmt3 = $db->query('DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);

						// ÉTAPE 3 : on compte le nombre d'IP stockées dans la table. C'est le nombre total de personnes connectées.
						$stmt4 = $db->query('SELECT COUNT(*) AS nbre_entrees FROM connectes');
						$donnees = $stmt4->fetch();

						// On affiche le nombre total de connectés
						if ($donnees['nbre_entrees'] < 2) {
							echo '<span class="small">Personne connectée :</span> <span class="badge badge-secondary float-right">'.$donnees['nbre_entrees'].'</span><br>';
						}
						else {
							echo '<span class="small">Personnes connectées :</span> <span class="badge badge-secondary float-right">'.$donnees['nbre_entrees'].'</span><br>';
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

						if($num<2) {
							echo '<span class="ml-3" style="font-size:11px;"><i class="fas fa-user"></i> Visiteur :</span> <span class="badge badge-secondary float-right" style="font-size:10px;">'.$num.'</span><br>';
						}
						else {
							echo '<span class="ml-3" style="font-size:11px;"><i class="fas fa-user"></i> Visiteurs :</span> <span class="badge badge-secondary float-right" style="font-size:10px;">'.$num.'</span><br>';
						}

						// On cherche le nombre de membres connectés avec leur pseudo
						$stmt6 = $db->query("SELECT pseudo FROM connectes WHERE pseudo != 'Visiteur'");
						$num1 = $stmt6->rowCount();

						if($num1 >= 2) {
							echo '<span class="ml-3" style="font-size:11px;"><i class="fas fa-user-tie"></i> Membres :</span> <span class="badge badge-secondary float-right" style="font-size:10px;">'.$num1.'</span><br>';
						}
						elseif($num1 <= 1) {
							echo '<span class="ml-3" style="font-size:11px;"><i class="fas fa-user-tie"></i> Membre :</span> <span class="badge badge-secondary float-right" style="font-size:10px;">'.$num1.'</span><br>';
						}
						//elseif($num1 < 2) {
						//	echo '<span class="ml-3" style="font-size:11px;"><i class="fas fa-user-tie"></i> Membre :</span> <span class="badge badge-secondary float-right" style="font-size:10px;">'.$num1.'</span><br>';
						//}

						$links = array();
						foreach ($stmt6 as $s) {
							$links[] = '<a href="/profil.php?membre='.html($s['pseudo']).'" style="text-decoration:none; font-size:11px;">'.html($s['pseudo']).'</a>';
						}
						$memberslink = implode(" <span class='small'>&brvbar;</span> ", $links);

						//On affiche le cadre d'affichage des membres connectés que si il y a au moins 1 membre connecté ;)
						if($num1 >=1) {
							echo '<div class="text-center border mb-2">'.$memberslink.'</div>';
						}

						/**** compteur de visites ***/
						// ETAPE 1 : on vérifie si l'IP se trouve déjà dans la table
						// On va compter le nombre d'entrées dont le champ "ip" est l'adresse ip du visiteur
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
						echo '<span class="small">Visites aujourd\'hui :</span> <span class="badge badge-info float-right">'.$donnees3['nbre_entrees'].'</span><br>';

						$stmt9 = $db->query('SELECT COUNT(*) AS nbre_entrees FROM compteur');
						$donnees4 = $stmt9->fetch();
						echo '<span class="small">Visites totales :</span> <span class="badge badge-info float-right">'.$donnees4['nbre_entrees'].'</span><br>';
						/**** Fin compteur de visites ****/

						?>
					</li>
				</ul>
				<br>

				<h6>Stats du tracker</h6>
				<ul class="list-group">
					<li class="list-group-item">
						<?php
						$stmt = $db->query('SELECT info_hash, sum(completed) completed, sum(leechers) leechers, sum(seeders) seeders, sum(leechers or seeders) torrents FROM xbt_files');
						$result = $stmt->fetch();
						$result['peers'] = $result['leechers'] + $result['seeders'];
						
						echo '<span class="small">Torrents téléchargés</span> <span class="badge badge-primary badge-pill float-right">'.$result['completed'].'</span><br>';
						echo '<span class="small">Clients</span> <span class="badge badge-primary badge-pill float-right">'.$result['peers'].'</span><br>';
						
						if ($result['peers']) {
							printf('<span class="small">Leech :</span> <span class="badge badge-danger badge-pill float-right">%d <span style="font-size:10px;">(%d %%)</span></span><br>', $result['leechers'], $result['leechers'] * 100 / $result['peers']);
							printf('<span class="small">Seed :</span> <span class="badge badge-success badge-pill float-right">%d <span style="font-size:10px;">(%d %%)</span></span><br>', $result['seeders'], $result['seeders'] * 100 / $result['peers']);
						}

						echo '<span class="small">Torrents actifs</span> <span class="badge badge-primary badge-pill float-right">'.$result['torrents'].'</span><br>';

						$stmt = $db->query('SELECT postID FROM blog_posts_seo');
						$nbrtorrents =$stmt->rowCount();

						printf('<span class="small">Torrents total :</span> <span class="badge badge-primary badge-pill float-right">%d</span><br>', $nbrtorrents);

						$stmt = $db->query('SELECT sum(downloaded) as down, sum(uploaded) as up FROM xbt_users');
						$row = $stmt->fetch();

						$dled=makesize($row['down']);
						$upld=makesize($row['up']);
						$traffic=makesize($row['down'] + $row['up']);

						printf('<span class="small">Download total :</span> <span class="badge badge-warning badge-pill float-right">'. $dled. '</span><br>');
						printf('<span class="small">Upload total :</span> <span class="badge badge-success badge-pill float-right">'. $upld. '</span><br>');
						printf('<span class="small">Trafic total :</span> <span class="badge badge-info badge-pill float-right">'. $traffic. '</span><br>');

						?>
					</li>
				</ul>
			</div>

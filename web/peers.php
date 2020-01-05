<?php
require_once 'includes/config.php';

// Pas d'accès direct à cette page + définition de la variable $hash
if(isset($_GET['hash'])) {

	if($_GET['hash'] == '') {
                header('Location: ./');
                exit();
        }

	$hash = isset($_GET['hash']) ? html($_GET['hash']) : NULL;


$pagetitle = 'Clients torrent';

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
        $stmt = $db->prepare('SELECT postTitle FROM blog_posts_seo WHERE postHash = :hash');
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $row = $stmt->fetch();
        ?>

        <h2>Torrent : <?php echo $row['postTitle']; ?></h2>

                <table>
		   <thead>
                      <tr>
                      	<th>Pseudo</th>
                  	<th>Statut</th>
                  	<th>Client</th>
                  	<th>Port</th>
                  	<th>Téléchargé</th>
                  	<th>Uploadé</th>
                 	<th>Ratio</th>
                  	<th>Mis à jour</th>
                 	</tr>
		   </thead>

                <?php
                $stmt = $db->prepare('
                        SELECT xal.id,xal.ipa,xal.port,xal.peer_id,xal.downloaded down,xal.uploaded up,xal.uid,xfu.mtime time,b.username, IF(xal.left0=0,"seeder","leecher") as status
                        FROM xbt_announce_log xal
                        LEFT JOIN blog_members b ON b.memberID = xal.uid
                        LEFT JOIN xbt_files xf ON xf.info_hash = xal.info_hash
                        LEFT JOIN blog_posts_seo bps ON bps.postID = xf.fid
                        LEFT JOIN xbt_files_users xfu ON xfu.fid = xf.fid
                        WHERE bps.postHash = :postHash AND xfu.active = 1 AND xal.mtime < (UNIX_TIMESTAMP() - 30)
                        GROUP BY xal.ipa
                        ORDER BY status DESC
                ');
                $stmt->bindValue(':postHash', $hash, PDO::PARAM_INT);
                $stmt->execute();
				while($row = $stmt->fetch()) {

                        // on trouve le client bittorrent
                        $peer = substr($row['peer_id'], 1, 2);

                        if($peer == 'AZ') {
                                $client = 'Azureus';
                        }
                        if($peer == 'AX') {
                                $client = 'AnalogX';
                        }
                        elseif($peer == 'AG') {
                                $client = 'Ares';
                        }
                        if($peer == 'BB') {
                                $client = 'BitBuddy';
                        }
                        elseif($peer == 'BC') {
                                $client = 'BitComet';
                        }
                        elseif($peer == 'BP') {
                                $client = 'Bittorrent Pro';
                        }
                        elseif($peer == 'BT') {
                                $client = 'BBtor';
                        }
                        elseif($peer == 'DE') {
                                $client = 'Deluge Torrent';
                        }
                        elseif($peer == 'FX') {
                                $client = 'Freebox BitTorrent';
                        }
                        elseif($peer == 'HL') {
                                $client = 'Halite';
                        }
                        elseif($peer == 'HM') {
                                $client = 'hMule';
                        }
                        elseif($peer == 'IL') {
                                $client = 'iLivid';
                        }
                        elseif($peer == 'JT') {
                                $client = 'JavaTorrent';
                        }
                        elseif($peer == 'KT') {
                                $client = 'KTorrent';
                        }
                        elseif($peer == 'KG') {
                                $client = 'KGet';
                        }
                        elseif($peer == 'LT') {
                                $client = 'libTorrent';
                        }
                        elseif($peer == 'lt') {
                                $client = 'rTorrent';
                        }
                        elseif($peer == 'LP') {
                                $client = 'Lphant';
                        }
						elseif($peer == 'LW') {
                                $client = 'LimeWire';
                        }
                        elseif($peer == 'MO') {
                                $client = 'MonoTorrent';
                        }
                        elseif($peer == 'MT') {
                                $client = 'MoonlightTorrent';
                        }
                        elseif($peer == 'NB') {
                                $client = 'Net::Bittorent';
                        }
                        elseif($peer == 'NX') {
                                $client = 'Net Transport';
                        }
                        elseif($peer == 'OS') {
                                $client = 'OneSwarm';
                        }
                        elseif($peer == 'OT') {
                                $client = 'Omega Torrent';
                        }
                        elseif($peer == 'PB') {
                                $client = 'Protocol::BitTorrent';
                        }
                        elseif($peer == 'PT') {
                                $client = 'PHPTracker';
                        }
                        elseif($peer == 'qB') {
                                $client = 'qBittorrent';
                        }
                        elseif($peer == 'SP') {
                                $client = 'BitSpirit';
                        }
                        elseif($peer == 'st') {
                                $client = 'Sharktorrent';
                        }
                        elseif($peer == 'SZ') {
                                $client = 'Shareaza';
                        }
                        elseif($peer == 'TB') {
                                $client = 'Torch';
                        }
                        elseif($peer == 'TIX') {
                                $client = 'Tixati';
                        }
                        elseif($peer == 'TL') {
                                $client = 'Tribler';
                        }
                        elseif($peer == 'TR') {
                                $client = 'Transmission';
                        }
                        elseif($peer == 'TS') {
                                $client = 'Torrentstorm';
                        }
                        elseif($peer == 'UM') {
                                $client = '&#181;Torrent for MAC';
                        }
						elseif($peer == 'UT') {
                                $client = '&#181;Torrent';
                        }
                        elseif($peer == 'WD') {
                                $client = 'WebTorrent Desktop';
                        }
                        elseif($peer == 'WT') {
                                $client = 'BitLet';
                        }
                        elseif($peer == 'WW') {
                                $client = 'WebTorrent';
                        }
                        elseif($peer == 'WY') {
                                $client = 'FireTorrent';
                        }
                        elseif($peer == 'XT') {
                                $client = 'XanTorrent';
                        }
                        elseif($peer == 'XX') {
                                $client = 'Xtorrent';
                        }
                        elseif($peer == 'ZT') {
                                $client = 'ZipTorrent';
                        }

                        else {
                                $client = 'Client inconnu';
                        }

                        echo '<tbody><tr class="center">';
                          	echo '<td>'.html($row['username']).'</td>';

                          	if ($row['status'] == 'leecher') {
                                	echo '<td><span class="red">leecher</span></td>';
                          	}
                        	elseif ($row['status'] == 'seeder') {
                                	echo '<td><span class="green">seeder</span></td>';
                          	}

                          	echo '<td>'.$client.'</td>';
                          	echo '<td>'.$row['port'].'</td>';
                          	echo '<td>'.makesize($row['down']).'</td>';
                          	echo '<td>'.makesize($row['up']).'</td>';

                          	//Peer Ratio
                          	if (intval($row["down"])>0) {
                                	$ratio=number_format($row["up"]/$row["down"],2);
                          	}
                          	else {
                                	$ratio='&#8734;';
                          	}
                          	echo '<td>'.$ratio.'</td>';
                          	echo '<td>'.get_elapsed_time($row['time']).'</td>';
                        echo '</tr></tbody>';
                }
                ?>

                </table>

        </div>
		
	<div class="divider2"></div>
	
      </div>

<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';

} //Fin if $_GET['hash']

?>

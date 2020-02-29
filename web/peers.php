<?php
include_once 'includes/config.php';

// Pas d'accès direct à cette page + définition de la variable $hash
if(isset($_GET['hash'])) {
	if($_GET['hash'] == '') {
                header('Location: ./');
                exit();
        }
	$hash = isset($_GET['hash']) ? html($_GET['hash']) : NULL;

	$pagetitle = 'Clients torrent';
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
        			$stmt = $db->prepare('SELECT postTitle FROM blog_posts_seo WHERE postHash = :hash');
        			$stmt->bindValue(':hash', $hash);
        			$stmt->execute();
        			$row = $stmt->fetch();
        			?>

        			<h2>Torrent : <?php echo $row['postTitle']; ?></h2>

                		<table class="table table-striped table-bordered table-hover small table-responsive-sm">
		   			<thead class="thead-dark">
                      				<tr>
                      					<th class="border border-white text-center">Pseudo</th>
                  					<th class="border border-white text-center">Statut</th>
                  					<th class="border border-white text-center">Client</th>
                  					<th class="border border-white text-center">Port</th>
                  					<th class="border border-white text-center">Téléchargé</th>
                  					<th class="border border-white text-center">Uploadé</th>
                 					<th class="border border-white text-center">Ratio</th>
                  					<th class="border border-white text-center">Mis à jour</th>
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

					echo '<tbody>';

					while($row = $stmt->fetch()) {
						// on trouve le client bittorrent
                        			$peer = substr($row['peer_id'], 1, 2);

                        			if($peer == 'AZ') {
                                			$client = 'Azureus';
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
                        			elseif($peer == 'HM') {
                                			$client = 'hMule';
                        			}
                        			elseif($peer == 'JT') {
                                			$client = 'JavaTorrent';
                        			}
                        			elseif($peer == 'KT') {
                                			$client = 'KTorrent';
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

                        			else {
                                			$client = 'Client inconnu';
                        			}

							echo '<tr class="text-center">';
                          					echo '<td>'.html($row['username']).'</td>';
                          					if ($row['status'] == 'leecher') {
                                					echo '<td><span class="text-danger">leecher</span></td>';
                          					}
                        					elseif ($row['status'] == 'seeder') {
                                					echo '<td><span class="text-success">seeder</span></td>';
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
                        				echo '</tr>';
                				}//while
                				?>
					</tbody>
		                </table>

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
}
?>

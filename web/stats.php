<?php
include_once 'includes/config.php';

$pagetitle = 'Stats torrents';

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
	
	<h2>Les 5 derniers torrents</h2>
	<table>
	   <thead>
              <th>Nom du torrent</th>
	      <th>Seeders</th>
	      <th>Leechers</th>
	   </thead>

	   <tbody>

	<?php
	$stmt = $db->query('SELECT blog_posts_seo.postID,blog_posts_seo.postHash,blog_posts_seo.postTitle,blog_posts_seo.postSlug,xbt_files.seeders,xbt_files.leechers FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postID = xbt_files.fid ORDER BY postID DESC LIMIT 5');
	while($row = $stmt->fetch()){
		echo '<tr>';
        	   echo '
		      <td style="width:85%;"> <a href="/'.html($row['postSlug']).'"><span>'.html($row['postTitle']).'</span></a></td>
		      <td class="center""><a href="../peers.php?hash='.html($row['postHash']).'"><span class="green">'.html($row['seeders']).'</span></a></td> 
		      <td class="center"><a href="../peers.php?hash='.html($row['postHash']).'"><span class="red">'.html($row['leechers']).'</span></a></td>
		';
		echo '</tr>';
        }
	?>

	</tbody>
        </table>

	<br><br>

	<h2>Les 5 torrents les plus populaires</h2>
	<table>
	   <thead>
	      <th>Nom du torrent</th>
	      <th>Nb de vues</th>
	   </thead>

	   <tbody>

	<?php
	$stmt = $db->query('SELECT postSlug,postTitle,postAuthor,postDate,postViews FROM blog_posts_seo ORDER BY postViews DESC LIMIT 5');
	while($row = $stmt->fetch()) {
        	echo '<tr>';
		echo '
			<td style="width:88%;"> <a href="/'.html($row['postSlug']).'">'.html($row['postTitle']).'</a></td>
			<td class="center"><span class="green">'.html($row['postViews']).'</span></td>
			';
		echo '</tr>';
	}
	?>

	</tbody>
	</table>

	<br><br>

	<?php
        $stmt = $db->query('SELECT blog_posts_seo.postID,blog_posts_seo.postHash,blog_posts_seo.postTitle,blog_posts_seo.postSlug,xbt_files.fid,xbt_files.seeders,xbt_files.leechers FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postID = xbt_files.fid AND xbt_files.seeders = 0 AND xbt_files.leechers > 0 ORDER BY postID DESC LIMIT 5');
	$count = $stmt->rowCount();

	if (!empty($count)) {
	?>

	<h2>Torrents qui ont besoin de seeds</h2>
	<table>
	   <thead>
	      <th>Nom du torrent</th>
              <th>Seeders</th>
	      <th>Leechers</th>
	   </thead>
	
	   <tbody>

	   <?php
	   while($row = $stmt->fetch()) {
		echo '<tr>';
                echo '
			<td style="width: 85%;"> <a style="text-decoration: none; color:black; font-size:14px;" href="/'.html($row['postSlug']).'">'.html($row['postTitle']).'</td>
			<td style="text-align:center;"><a  href="/peers.php?hash='.html($row['postHash']).'"><span style="color: green;">'.html($row['seeders']).'</span></a></td>
                       	<td style="text-align:center;"><a  href="/peers.php?hash='.html($row['postHash']).'"><span style="color: red;">'.html($row['leechers']).'</span></a></td>
		';
		echo '</tr>';
	   }
           ?>
	   </tbody>
	</table>
	<br><br>
	<?php } ?>


	<h2>Les 5 torrents les plus actifs</h2>
	<table>
	   <thead>
	      <th>Nom du torrent</th>
	      <th>Seeders</th>
	      <th>Leechers</th>
	   </thead>

	   <tbody>

	   <?php
           $stmt = $db->query('SELECT blog_posts_seo.postID,blog_posts_seo.postHash,blog_posts_seo.postTitle,blog_posts_seo.postSlug,xbt_files.seeders,xbt_files.leechers FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postID = xbt_files.fid ORDER BY xbt_files.seeders DESC LIMIT 5');
           while($row = $stmt->fetch()){
           	echo '<tr>';
                   echo '
                   <td style="width:85%;"> <a href="/'.html($row['postSlug']).'"><span>'.html($row['postTitle']).'</span></a></td>
                   <td style="text-align:center;"><a href="../peers.php?hash='.html($row['postHash']).'"><span class="green">'.html($row['seeders']).'</span></a></td>
                   <td style="text-align:center;"><a href="../peers.php?hash='.html($row['postHash']).'"><span class="red">'.html($row['leechers']).'</span></a></td>
                   ';
                echo '</tr>';
           }
           ?>

	</tbody>
        </table>


	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>


<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

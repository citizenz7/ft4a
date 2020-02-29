<?php
header('Content-type: text/xml'); 
echo '<?xml version="1.0" encoding="UTF-8" ?>';

require_once 'includes/config.php';

//Titre de la page
$pagetitle = 'Flux RSS';

?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">

<?php
$nomsite = SITENAMELONG;
?>

      <channel>
      	    	<title><?php echo SITEURL; ?></title>
		<language>fr</language>
      	    	<link><?php echo SITEURL; ?></link>
		<lastBuildDate><?php print date("D, d M Y H:i:s O");?></lastBuildDate>
      	    	<description><?php echo SITESLOGAN; ?></description>
		<copyright><?php print "(copyleft)". date("Y",time())." " .$nomsite; ?></copyright>
		<atom:link href="<?php echo SITEURL; ?>/rss.php" rel="self" type="application/rss+xml" />

      	    	<?php
		$stmt = $db->query("SELECT * FROM blog_posts_seo,xbt_files WHERE blog_posts_seo.postID = xbt_files.fid ORDER BY postDate DESC LIMIT 10");
      	    	while($data = $stmt->fetch()) {
			$id=html($data['postID']);
			$filename = preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $data['postTitle']);
			$seeders = strip_tags($data['seeders']);
			$leechers = strip_tags($data['leechers']);
			$desc = nl2br(bbcode($data['postDesc']));
			$torrent = html($data['postTorrent']);
			$f= rawurlencode(html($data['postTitle']));
			$taille = strip_tags($data['postTaille']);

			// on affiche dans le navigateur
      	    		echo "<item>\n";
      	    		echo "<title><![CDATA[$filename [seeders ($seeders)/leechers ($leechers)]]]></title>\n";
      	    		echo "<link>".SITEURL."/".$data['postSlug']."</link>\n";
			echo "<guid>".SITEURL."/".$data['postSlug']."</guid>\n";
      	    		echo "<description><![CDATA[".$desc."]]></description>\n";
			echo "<enclosure url=\"".SITEURL."/torrents/".$torrent."\" length=\"".$taille."\" type=\"application/x-bittorrent\" />";
      	    		echo "<pubDate>".$data["postDate"]." GMT</pubDate>\n";
      	    		echo "</item>\n";
      	    	}	    	
      	    	?>
      </channel>
</rss>

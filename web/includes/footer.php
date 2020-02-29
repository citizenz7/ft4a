	<div class="container p-3 mt-4 bg-dark text-white rounded">
		<div class="row">
			<div class="col-sm-4">
				<p class="small font-italic">
					<i class="far fa-copyright"></i> ft4a.fr 2020 | <i class="fas fa-info-circle"></i> <span class="small">Version du site : <?php echo SITEVERSION; ?> du <?php echo SITEDATE; ?></span>
					<br><i class="far fa-clock"></i> 
					<?php
					$fin = getmicrotime();
					echo '<span class="small">Page générée en '.round($fin-$debut, 3) .' secondes.</span>';
					?>
				</p>
			</div>
			<div class="col-sm-2">
				<!-- Archives -->
				<select onchange="document.location.href = this.value" class="custom-select custom-select-sm" style="font-size: 12px;">
					<option selected>Archives</option>
					<?php
					$stmt = $db->query("SELECT Month(postDate) as Month, Year(postDate) as Year FROM blog_posts_seo GROUP BY Month(postDate), Year(postDate) ORDER BY postDate DESC");
					while($row = $stmt->fetch()){
						$monthName = date_fr("F", mktime(0, 0, 0, html($row['Month']), 10));
						$year = date_fr(html($row['Year']));
						$slug = 'a-'.html($row['Month']).'-'.html($row['Year']);
						echo '<option value="/'.$slug.'">'.$monthName.'&nbsp;'.$year.'</option>';
					}
					?>
				</select>
			</div>
			<div class="col-sm-2">
				<!-- Catégories -->
				<select onchange="document.location.href = this.value" class="custom-select custom-select-sm" style="font-size: 12px;">
					<option selected>Catégories</option>
					<?php
                			$stmt = $db->query('SELECT catTitle, catSlug FROM blog_cats ORDER BY catTitle ASC');
                			while($row = $stmt->fetch()){
                				echo '<option value="c-'.html($row['catSlug']).'">'.html($row['catTitle']).'</option>';
                			}
                			?>
				</select>
			</div>
			<div class="col-sm-2">
				<!-- Licences -->
				<select onchange="document.location.href = this.value" class="custom-select custom-select-sm" style="font-size: 12px;">
					<option selected>Licences</option>
					<?php
                			$stmt = $db->query('SELECT licenceTitle, licenceSlug FROM blog_licences ORDER BY licenceTitle ASC');
                			while($row = $stmt->fetch()){
                				echo '<option value="l-'.html($row['licenceSlug']).'">'.html($row['licenceTitle']).'</option>';
                			}
                			?>
				</select>
			</div>
			<div class="col-sm-2">
				<h4>
					<a target="blank_" href="https://github.com/citizenz7"><i class="fab fa-github small text-white"></i></a>&nbsp;
					<a target="blank_" href="https://discord.gg/neVZtE3"><i class="fab fa-discord small text-white"></i></a>&nbsp;
					<a target="blank_" href="https://mastodon.top/@citizenz7"><i class="fab fa-mastodon small text-white"></i></a>&nbsp;
					<a target="blank_" href="https://twitter.com/citizenz58"><i class="fab fa-twitter-square small text-white"></i></a>&nbsp;
					<a target="blank_" href="/rss.php"><i class="fas fa-rss-square small text-white"></i></a>
				</h4>
			</div>
		</div>
		<div class="container-fluid text-center">
			<h2><a data-toggle="tooltip" title="Retour en haut" href="<?php echo $_SERVER['REQUEST_URI']; ?>#top" class="text-white"><i class="fas fa-chevron-circle-up"></i></a></h2>
		</div>
	</div> <!-- //footer -->

<?php
include_once 'includes/config.php';
$pagetitle ='A propos';
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
			
			<!-- A propos -->
			<div class="col-sm-9 mb-4 text-justify small">
					<div class="card mb-3">
						<div class="card-body">
							<div class="float-right"><img src="images/logo.png" style="max-height:150px;" alt="ft4a"></div>
							<h4>Mentions légales</h4>
							ft4a.fr<br>
							Olivier Prieur (citizenz7)<br>
							57, rue de Marzy 58000 NEVERS<br>
							Mail : contact AT ft4a.fr<br><br>
							Ce site est hébergé par : <a href="https://www.hetzner.com/">Hetzner</a>
						</div>
					</div>

					<p>
					<h4>A propos et Présentation :</h4>
					ft4a.fr rassemble tous projets et médias libres et les propose au téléchargement par l'intermédiaire du protocole Bittorrent.<br>
					Il est complémentaire des projets officiels qui possèdent déjà leurs services Bittorrent (distributions Gnu/Linux, projets divers, ...) et s'adresse tout particulièrement aux projets plus modestes qui recherchent un moyen simple de partager librement leurs travaux.<br>
					</p>

					<h4>Conditions d'Utilisation :</h4>
					<p>
					ft4a.fr propose des médias sous licence libre ou licence de libre diffusion EXCLUSIVEMENT. Tout autre matériel sous une quelconque licence restrictive, commerciale ou propriétaire n'est pas admis sur ft4a.fr.<br>
					Tout média "cracké" ou "piraté" (warez, etc.) est strictement interdit sur ft4a.fr et sera irrémédiablement et immédiatement effacé.<br>
					Le compte de l'utilisateur responsable de l'upload de torrents interdits sera supprimé et son adresse IP transmise aux ayant-droits.<br>
					En tant qu'utilisateur ou membre inscrit, la "personne" accepte les conditions générales d'utilisation.<br>
					</p>
					
					<h4>Download / Upload (Proposer des fichiers) :</h4>
					<p>
					Pour uploader (proposer) des torrents ET pour downloader (recevoir) des torrents le visiteur doit devenir membre (inscription) en créant un compte.<br>
					ft4a.fr se réserve le droit de supprimer ou de modifier tout fichier envoyé et mis en partage sur son serveur et ne pourra être tenu responsable des écrits, prises de positions, convictions ou partis-pris exposés ou suggérés dans les fichiers proposés au téléchargement.<br>
					ft4a.fr n'est ainsi pas responsable des fichiers proposés par ses membres.<br>
					ft4a.fr s'engage néanmoins à faire tout ce qui est en son pouvoir pour lutter contre la diffusion de fichiers illégaux et/ou immoraux. Dans les cas les plus graves d'atteinte à la personne humaine notamment, ft4a.fr jouera pleinement son rôle citoyen et responsable en avertissant les autorités compétentes.<br>
					En tant qu'utilisateur de ft4a.fr, vous vous engagez à respecter la loi en général, et la loi sur les droits d'auteur en particulier.<br>
					Vous pourrez, à tout moment, avertir le webmaster de ft4a.fr de la présence de fichiers suspects ou illégaux sur le site en faisant un simple signalement par l'intermédiaire de la page de contact.<br>
					Ainsi, ft4a.fr n'incite pas à la délation péjorative mais souhaite de manière communautaire participer à la promotion du "Libre" et se protéger au niveau de la loi.<br>
					</p>

					<h4>Informatique et libertés</h4>
					<p>
					Informations personnelles collectées<br>
					En France, les données personnelles sont notamment protégées par la loi n 78-87 du 6 janvier 1978, la loi n 2004-801 du 6 août 2004, l'article L. 226-13 du Code pénal et la Directive Européenne du 24 octobre 1995.<br>
					En tout état de cause, ft4a.fr ne collecte des informations personnelles relatives à l'utilisateur (nom, adresse électronique, coordonnées ....) que pour le besoin des services proposés par le site web de ft4a.fr, notamment pour l'inscription à des événements par le biais de formulaires en ligne. L'utilisateur fournit ces informations en toute connaissance de cause, notamment lorsqu'il procède par lui-même à leur saisie. Il est alors précisé à l'utilisateur le caractère obligatoire ou non des informations qu'il serait amené à fournir.<br>
					Aucune information personnelle de l'utilisateur du site de ft4a.fr n'est collectée à l'insu de l'utilisateur, publiée à l'insu de l'utilisateur, échangée, transférée, cédée ou vendue sur un support quelconque à des tiers.<br>
					</p>

					<h4>Rectification des informations nominatives collectées</h4>
					<p>
					Conformément aux dispositions de l'article 34 de la loi n 48-87 du 6 janvier 1978, l'utilisateur dispose d'un droit de modification des données nominatives collectées le concernant.<br>
					Pour ce faire, l'utilisateur envoie à ft4a.fr un courrier électronique en utilisant le formulaire de contact en indiquant son nom ou sa raison sociale, ses coordonnées physiques et/ou électroniques, ainsi que le cas échéant la référence dont il disposerait en tant qu'utilisateur du site de ft4a.fr. La modification interviendra dans des délais raisonnables à compter de la réception de la demande de l'utilisateur.<br>
					</p>

					<h4>Limitation de responsabilité</h4>
					<p>
					ft4a.fr peut comporter des informations mises à disposition par des sociétés externes ou des liens hypertextes vers d'autres sites qui n'ont pas été développés par ft4a.fr. Le contenu mis à disposition sur le site est fourni à titre informatif. L'existence d'un lien de ce site vers un autre site ne constitue pas une validation de ce site ou de son contenu. Il appartient à l'internaute d'utiliser ces informations avec discernement et esprit critique. La responsabilité de ft4a.fr ne saurait être engagée du fait des informations, opinions et recommandations formulées par des tiers.<br>
					</p>

			</div>
			
			<!-- sidebar -->
			<?php include_once 'includes/sidebar.php'; ?>

		</div> <!-- //row -->
		

	<!-- footer -->
	<?php include_once 'includes/footer.php'; ?>

</div> <!-- //container -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html> 

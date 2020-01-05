<?php
include_once 'includes/config.php';

$pagetitle = 'A propos';

include_once 'includes/header.php';
include_once 'includes/header-logo.php';
include_once 'includes/header-nav.php';
?>

<!-- ########################################## content.php ############################################# -->
<div class="wrapper row3">
  <div id="container">
    <!-- ### -->
    <div id="homepage" class="clear">
      <div class="two_third first">

	<div id="blog-posts" class="first justify">

	<h3>Mentions légales</h3>
	<?php echo SITENAMELONG; ?><br />
	<?php echo SITEOWNORNAME; ?><br />
	<?php echo SITEOWNORADDRESS; ?><br />
	Mail : <?php echo SITEMAIL; ?><br /><br />

Serveur : <a href="https://hetzner.com">Hetzner</a><br><br>

<h3>A propos et Présentation :</h3>
<p><?php echo SITENAMELONG; ?> rassemble tous projets et médias libres et les propose au téléchargement par l'intermédiaire du protocole Bittorrent.<br />
Il est complémentaire des projets officiels qui possèdent déjà leurs services Bittorrent (distributions Gnu/Linux, projets divers, ...) et s'adresse tout particulièrement aux projets plus modestes qui recherchent un moyen simple de partager librement leurs travaux.<br /></p>

<h3>Un peu d'aide ?</h3>
<p>Si vous vous sentez l'âme d'un codeur php, et si vous deviez décider de consacrer un peu de temps à <?php echo SITENAME; ?>, cela serait avec joie. Le code a certainement besoin d'être afiné, "nettoyé", sécurisé. N'hésitez pas à m'envoyer un petit mot par l'intermédiaire du <a href="contact.php">formulaire de contact</a>.</p>

<h3>Conditions d'Utilisation :</h3>
<p><?php echo SITENAMELONG; ?> propose des médias sous licence libre ou licence de libre diffusion EXCLUSIVEMENT.
Tout autre matériel sous une quelconque licence restrictive, commerciale ou propriétaire n'est pas admis sur <?php echo SITENAMELONG; ?>.<br />
Tout média "cracké" ou "piraté" (warez, etc.) est strictement interdit sur <?php echo SITENAMELONG; ?> et sera irrémédiablement et immédiatement effacé.<br />
Le compte de l'utilisateur responsable de l'upload de torrents interdits sera supprimé et son adresse IP transmise aux ayant-droits.<br />
En tant qu'utilisateur ou membre inscrit, la "personne" accepte les conditions générales d'utilisation.</p>

<h3>Download / Upload (Proposer des fichiers)</h3>
<p>Pour uploader (proposer) des torrents ET pour downloader (recevoir) des torrents le visiteur doit devenir membre (inscription) en créant un compte.<br />
<?php echo SITENAMELONG; ?> se réserve le droit de supprimer ou de modifier tout fichier envoyé et mis en partage sur son serveur et ne pourra être tenu responsable des écrits, prises de positions, convictions ou partis-pris exposés ou suggérés dans les fichiers proposés au téléchargement.
<br />
<?php echo SITENAMELONG; ?> n'est ainsi pas responsable des fichiers proposés par ses membres.
<br />
<?php echo SITENAMELONG; ?> s'engage néanmoins à faire tout ce qui est en son pouvoir pour lutter contre la diffusion de fichiers illégaux et/ou immoraux. Dans les cas les plus graves d'atteinte à la personne humaine notamment, <?php echo SITENAMELONG; ?> jouera pleinement son rôle citoyen et responsable en avertissant les autorités compétentes.
<br />
En tant qu'utilisateur de <?php echo SITENAMELONG; ?>, vous vous engagez à respecter la loi en général, et la loi sur les droits d'auteur en particulier.
<br />
Vous pourrez, à tout moment, avertir le webmaster de <?php echo SITENAMELONG; ?> de la présence de fichiers suspects ou illégaux sur le site en faisant un simple signalement par l'intermédiaire de la page de <a href="/contact.php">contact</a>.
<br />Ainsi, <?php echo SITENAMELONG; ?> n'incite pas à la délation péjorative mais souhaite de manière communautaire participer à la promotion du "Libre" et se protéger au niveau de la loi.</p>

<h3>Informatique et libertés</h3>
<p>Informations personnelles collectées<br />
En France, les données personnelles sont notamment protégées par la loi n 78-87 du 6 janvier 1978, la loi n 2004-801 du 6 août 2004, l'article L. 226-13 du Code pénal et la Directive Européenne du 24 octobre 1995.<br />
En tout état de cause, <?php echo SITENAMELONG; ?> ne collecte des informations personnelles relatives à l'utilisateur (nom, adresse électronique, coordonnées ....) que pour le besoin des services proposés par le site web de <?php echo SITENAMELONG; ?>, notamment pour l'inscription à des événements par le biais de formulaires en ligne. L'utilisateur fournit ces informations en toute connaissance de cause, notamment lorsqu'il procède par lui-même à leur saisie. Il est alors précisé à l'utilisateur le caractère obligatoire ou non des informations qu'il serait amené à fournir.<br />
Aucune information personnelle de l'utilisateur du site de <?php echo SITENAMELONG; ?> n'est collectée à l'insu de l'utilisateur, publiée à l'insu de l'utilisateur, échangée, transférée, cédée ou vendue sur un support quelconque à des tiers.</p>

<h3>Rectification des informations nominatives collectées</h3>
<p>Conformément aux dispositions de l'article 34 de la loi n 48-87 du 6 janvier 1978, l'utilisateur dispose d'un droit de modification des données nominatives collectées le concernant.<br />
Pour ce faire, l'utilisateur envoie à <?php echo SITENAMELONG; ?> un courrier électronique en utilisant le formulaire de contact en indiquant son nom ou sa raison sociale, ses coordonnées physiques et/ou électroniques, ainsi que le cas échéant la référence dont il disposerait en tant qu'utilisateur du site de <?php echo SITENAMELONG; ?>. La modification interviendra dans des délais raisonnables à compter de la réception de la demande de l'utilisateur.</p>

<h3>Limitation de responsabilité</h3>
<p><?php echo SITENAMELONG; ?> peut comporter des informations mises à disposition par des sociétés externes ou des liens hypertextes vers d'autres sites qui n'ont pas été développés par <?php echo SITENAMELONG; ?>. Le contenu mis à disposition sur le site est fourni à titre informatif. L'existence d'un lien de ce site vers un autre site ne constitue pas une validation de ce site ou de son contenu. Il appartient à l'internaute d'utiliser ces informations avec discernement et esprit critique. La responsabilité de <?php echo SITENAMELONG; ?> ne saurait être engagée du fait des informations, opinions et recommandations formulées par des tiers.</p> 

	<!-- ### -->
        </div>
		
	<div class="divider2"></div>
	
      </div>

<?php
include_once 'includes/sidebar.php';
include_once 'includes/footer.php';
?>

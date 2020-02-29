<?php
function slug($text){ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
  // trim
  $text = trim($text, '-');
  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  // lowercase
  $text = strtolower($text);
  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);
  if (empty($text))
  {
    return 'n-a';
  }
  return $text;
}


function makesize($bytes) {
  if (abs($bytes) < 1000 * 1024)
    return number_format($bytes / 1024, 2) . " Ko";
  if (abs($bytes) < 1000 * 1048576)
    return number_format($bytes / 1048576, 2) . " Mo";
  if (abs($bytes) < 1000 * 1073741824)
    return number_format($bytes / 1073741824, 2) . " Go";
    return number_format($bytes / 1099511627776, 2) . " To";
}


function get_elapsed_time($ts)
{
  $mins = floor((time() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks > 0)
    return "$weeks semaine" . ($weeks > 1 ? "s" : "");
  if ($days > 0)
    return "$days jour" . ($days > 1 ? "s" : "");
  if ($hours > 0)
    return "$hours heure" . ($hours > 1 ? "s" : "");
  if ($mins > 0)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}


function buildTreeArray($files)
{
    $ret = array();
    foreach ($files as $k => $v)
    {
        $filename=$v['filename'];
        $parts = preg_split('/\//', $filename, -1, PREG_SPLIT_NO_EMPTY);
        $leaf = array_pop($parts);

        // build parent structure
        $parent = &$ret;
        foreach ($parts as $part)
        {
                $parent = &$parent[$part];
        }

        if (empty($parent[$leaf]))
        {
                $v['filename']=$leaf;
                $parent[$leaf] = $v;
        }
    }

    return $ret;
}


function outputTree($files, $indent=1)
{
    //echo "<table style=\"font-size: 7pt; width: 100%;\"";
    foreach($files as $k=>$v)
    {
        $entry=isset($v['filename']) ? $v['filename'] : $k;
        $size=$v['size'];

        if($indent==0)
        {
            // root
            $is_folder=true;
        }
        elseif(is_array($v) && (!array_key_exists('filename',$v) && !array_key_exists('size',$v)))
        {
            // normal node
            $is_folder=true;
        }
        else
        {
            // leaf node, i.e. a file
        $is_folder=false;
        }

        if($is_folder)
        {
            // we could output a folder icon here
        }
        else
        {
            // we could output an appropriate icon
            // based on file extension here
            $ext=pathinfo($entry,PATHINFO_EXTENSION);
        }

       // echo "<tr><td style=\"border: 1px solid #D2D2D2;\">";
        echo $entry; // output folder name or filename

        if(!$is_folder)
        {
            // if it’s not a folder, show file size
            echo " (".makesize($size).")";
        }

        //echo "</td></tr>";
 
        if(is_array($v) && $is_folder)
        {
            outputTree($v, ($indent+1));
        }
    }

    //echo "</table>";
}


function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}


function date_fr($format, $timestamp=false) {
	if (!$timestamp) $date_en = date($format);
	else $date_en = date($format,$timestamp);

	$texte_en = array(
		"Monday", "Tuesday", "Wednesday", "Thursday",
		"Friday", "Saturday", "Sunday", "January",
		"February", "March", "April", "May",
		"June", "July", "August", "September",
		"October", "November", "December"
	);
	$texte_fr = array(
		"lundi", "mardi", "mercredi", "jeudi",
		"vendredi", "samedi", "dimanche", "janvier",
		"f&eacute;vrier", "mars", "avril", "mai",
		"juin", "juillet", "ao&ucirc;t", "septembre",
		"octobre", "novembre", "d&eacute;cembre"
	);
	$date_fr = str_replace($texte_en, $texte_fr, $date_en);

	$texte_en = array(
		"Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun",
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul",
		"Aug", "Sep", "Oct", "Nov", "Dec"
	);
	$texte_fr = array(
		"Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim",
		"Jan", "F&eacute;v", "Mar", "Avr", "Mai", "Jui",
		"Jui", "Ao&ucirc;", "Sep", "Oct", "Nov", "D&eacute;c"
	);
	$date_fr = str_replace($texte_en, $texte_fr, $date_fr);

	return $date_fr;
}


// ---------------------------------------------------------------------
//  Générer un mot de passe aléatoire
// ---------------------------------------------------------------------
function fct_passwd( $chrs = "")
{
   if( $chrs == "" ) $chrs = 10;
   $chaine = "";

   $list = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghkmnpqrstuvwxyz!=$";
   mt_srand((double)microtime()*1000000);
   $newstring="";

   while( strlen( $newstring )< $chrs ) {
   $newstring .= $list[mt_rand(0, strlen($list)-1)];
   }
   return $newstring;
 }


function get_extension($nom) {
    $nom = explode(".", $nom);
    $nb = count($nom);
    return strtolower($nom[$nb-1]);
}


//define('REPLACE_FLAGS', ENT_COMPAT | ENT_XHTML);

function html($string) {
    //return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    return htmlspecialchars($string, ENT_COMPAT, 'UTF-8', true);
}

function bbcode($input){
    $input = strip_tags($input);
    $input = html($input);
    
    $search = array(
                '/\[b\](.*?)\[\/b\]/is',
                '/\[i\](.*?)\[\/i\]/is',
                '/\[u\](.*?)\[\/u\]/is',
		'/\[img=(.*?)x(.*?)\](.*?)\[\/img\]/is',
                '/\[url=(.*?)\](.*?)\[\/url\]/is',
		'/\[color=(.*?)\](.*?)\[\/color\]/is',
		'/\[size=(.*?)\](.*?)\[\/size\]/is',
		'/\[code\](.*?)\[\/code\]/is',
		'/\[quote\](.*?)\[\/quote\]/is',
		'/\[center\](.*?)\[\/center\]/is',
		'/\[right\](.*?)\[\/right\]/is',
		'/\[justify\](.*?)\[\/justify\]/is'
    );
    
    $replace = array(
                '<span style="font-weight:bold;">$1</span>',
                '<span="font-style:italic;">$1</span>',
                '<span style="text-decoration:underline;">$1</span>',
		'<img style="width:$1px; height:$2px;" src="$3" alt="" />',
                '<a href="$1">$1</a>',
		'<span style="color:$1;">$2</span>',
		'<span style="font-size:$1px;">$2</span>',
		'<code>$1</code>',
		'<blockquote>$1</blockquote>',
		'<p style="text-align: center;">$1</p>',
		'<p style="text-align: right;">$1</p>',
		'<p style="text-align: justify;">$1</p>'
    );
    
    return preg_replace($search,$replace,$input);
}

// editeur BBCode dans un champ de type textarea
function BBCodeGetEditor($aName, $aTxt, $aButtonLst=''){

	// 1- liste des balises reconnues
	$tag = array();
	$tag['fontsize'] = array('id'=>'bbcode1', 'title'=>'Taille', 'type'=>'select', 'option'=>[
	['title'=>'Dimension', 'value'=>''],
	['title'=>'10px', 'value'=>'10'],
	['title'=>'11px', 'value'=>'11'],
	['title'=>'12px', 'value'=>'12'],
	['title'=>'14px', 'value'=>'14'],
	['title'=>'16px', 'value'=>'16'],
	['title'=>'18px', 'value'=>'18'],
	['title'=>'20px', 'value'=>'20'],
	['title'=>'24px', 'value'=>'24']
	], 'tag1'=>'[size=$1]', 'tag2'=>'[/size]');
	$tag['color'] = array('id'=>'bbcode2', 'title'=>'Couleur', 'type'=>'select', 'option'=>[
	['title'=>'Couleur', 'value'=>''],
	['title'=>'Rouge', 'value'=>'#ff0000'],
	['title'=>'Vert', 'value'=>'#00ff00'],
	['title'=>'Bleu', 'value'=>'#0000ff']
	], 'tag1'=>'[color=$1]', 'tag2'=>'[/color]');
	$tag['bold'] = array('id'=>'bbcode3', 'title'=>'Gras', 'type'=>'button', 'tag1'=>'[b]', 'tag2'=>'[/b]');
	$tag['italic'] = array('id'=>'bbcode4', 'title'=>'Italique', 'type'=>'button', 'tag1'=>'[i]', 'tag2'=>'[/i]');
	$tag['underline'] = array('id'=>'bbcode5', 'title'=>'Souligné', 'type'=>'button', 'tag1'=>'[u]', 'tag2'=>'[/u]');
	$tag['stroke'] = array('id'=>'bbcode6', 'title'=>'Barré', 'type'=>'button', 'tag1'=>'[s]', 'tag2'=>'[/s]');
	$tag['sup'] = array('id'=>'bbcode7', 'title'=>'Exposant', 'type'=>'button', 'tag1'=>'[sup]', 'tag2'=>'[/sup]');
	$tag['sub'] = array('id'=>'bbcode8', 'title'=>'Indice', 'type'=>'button', 'tag1'=>'[sub]', 'tag2'=>'[/sub]');
	$tag['left'] = array('id'=>'bbcode9', 'title'=>'Aligné à gauche', 'type'=>'button', 'tag1'=>'[left]', 'tag2'=>'[/left]');
	$tag['right'] = array('id'=>'bbcode10', 'title'=>'Aligné à droite', 'type'=>'button', 'tag1'=>'[right]', 'tag2'=>'[/right]');
	$tag['center'] = array('id'=>'bbcode11', 'title'=>'Centré', 'type'=>'button', 'tag1'=>'[center]', 'tag2'=>'[/center]');
	$tag['justify'] = array('id'=>'bbcode12', 'title'=>'Justifié', 'type'=>'button', 'tag1'=>'[justify]', 'tag2'=>'[/justify]');
	$tag['img'] = array('id'=>'bbcode13', 'title'=>'Image', 'type'=>'button', 'tag1'=>'[img]', 'tag2'=>'[/img]');
	/*$tag['video'] = array('id'=>'bbcode18', 'title'=>'Vidéo', 'type'=>'button', 'tag1'=>'[video]', 'tag2'=>'[/video]');*/
	$tag['url'] = array('id'=>'bbcode14', 'title'=>'Url', 'type'=>'button', 'tag1'=>'[url]', 'tag2'=>'[/url]');
	/*$tag['email'] = array('id'=>'bbcode16', 'title'=>'Email', 'type'=>'button', 'tag1'=>'[email]', 'tag2'=>'[/email]');*/
	$tag['code'] = array('id'=>'bbcode15', 'title'=>'Code', 'type'=>'button', 'tag1'=>'[code]', 'tag2'=>'[/code]');
	$tag['quote'] = array('id'=>'bbcode17', 'title'=>'Citation', 'type'=>'button', 'tag1'=>'[quote]', 'tag2'=>'[/quote]');

	// 2- initialise les balises à utiliser parmi la liste aButtonLst
	$tagSel = array();
	if(empty($aButtonLst)){
		$tagSel = $tag;

	}else{
		foreach($aButtonLst as $v){
			if(!empty($tag[$v])) $tagSel[] = $tag[$v];
		}
	}

	// 3- affiche les boutons choisis
	$h = '<section class="last">';
	foreach($tagSel as $v){
		switch($v['type']){
		case 'button' :
			// bouton
			$h .= '<div id="bbcode" class="button"><input id="'.$v['id'].'" type="button" value="'.$v['title'].'" onclick="EditorTagInsert(\''.$aName.'\', \''.$v['tag1'].'\', \''.$v['tag2'].'\', 0);" /></div>';
			break;
		case 'select' :
			// menu déroulant
			$h .= '<select id="'.$v['id'].'" onchange="EditorTagInsert(\''.$aName.'\', \''.$v['tag1'].'\', \''.$v['tag2'].'\', this.value);">';
			foreach($v['option'] as $v){
				$h .= '<option value="'.$v['value'].'">'.$v['title'].'</option>';
			}
			$h .= '</select>';
			break;
		}
	}
	$h .= '</section><textarea id="'.$aName.'" name="'.$aName.'" rows="10" cols="60">'.$aTxt.'</textarea>';

	// 4- code javascript
	// ce code permet d'insérer des balises en tenant compte de la sélection.
	$h .= '
<script type="text/javascript">
//<![CDATA[
var tagLst = [];
function EditorTagInsert(aId, aTag1, aTag2, aOpt){
	if(aOpt === "") return 0;
	if(aOpt != 0) aTag1 = aTag1.replace("$1", aOpt);
	var e = document.getElementById(aId);
	if(typeof(e) == "undefined" || e == null) return 0;
	var s1 = e.selectionStart;
	var s2 = e.selectionEnd;
	var txt = e.value;
	e.value = (txt.substring(0, s1) + aTag1 + txt.substring(s1, s2) + aTag2 + txt.substring(s2, txt.length));
	e.focus();
}
//]]>
</script>';
	return $h;
}

// conversion d'un message en html
function BBCode2Html($aTxt){
	// 1- remplace les retour à la ligne par des balises <br />
	$aTxt = nl2br($aTxt);

	// 2- liste des balises BBCode
	$tag = array(
		'/\[b\](.*?)\[\/b\]/is',
		'/\[i\](.*?)\[\/i\]/is',
		'/\[u\](.*?)\[\/u\]/is',
		'/\[s\](.*?)\[\/s\]/is',
		'/\[sup\](.*?)\[\/sup\]/is',
		'/\[sub\](.*?)\[\/sub\]/is',
		'/\[size\=(.*?)\](.*?)\[\/size\]/is',
		'/\[color\=(.*?)\](.*?)\[\/color\]/is',
		'/\[code\](.*?)\[\/code\]/is',
		'/\[quote\](.*?)\[\/quote\]/is',
		'/\[quote\=(.*?)\](.*?)\[\/quote\]/is',
		'/\[left](.*?)\[\/left\]/is',
		'/\[right](.*?)\[\/right\]/is',
		'/\[center](.*?)\[\/center\]/is',
		'/\[justify](.*?)\[\/justify\]/is',
		'/\[list\](.*?)\[\/list\]/is',
		'/\[list=1\](.*?)\[\/list\]/is',
		'/\[\*\](.*?)(\n|\r\n?)/is',
		'/\[img\](.*?)\[\/img\]/is',
		'/\[url\](.*?)\[\/url\]/is',
		'/\[url\=(.*?)\](.*?)\[\/url\]/is',
		'/\[email\](.*?)\[\/email\]/is',
		'/\[email\=(.*?)\](.*?)\[\/email\]/is'
	);

	// 3- correspondance HTML
	$h = array(
		'<strong>$1</strong>',
		'<em>$1</em>',
		'<u>$1</u>',
		'<span style="text-decoration:line-through;">$1</span>', 
		'<sup>$1</sup>',
		'<sub>$1</sub>',
		'<span style="font-size:$1px;">$2</span>',  
		'<span style="color:$1;">$2</span>',   
		'<code><pre>$1</pre></code>', 
		'<blockquote>$1</blockquote>',
		'<blockquote><cite>$1 : </cite>$2</blockquote>',  
		'<div style="text-align:left;">$1</div>',
		'<div style="text-align:right;">$1</div>',
		'<div style="text-align:center;">$1</div>',
		'<div style="text-align:justify;">$1</div>',
		'<ul>$1</ul>',
		'<ol>$1</ol>',
		'<li>$1</li>',
		'<img src="$1" />',
		'<a href="$1">$1</a>',
		'<a href="$1">$2</a>',
		'<a href="mailto:$1">$1</a>',
		'<a href="mailto:$1">$2</a>'
	);

	// 4- remplace les balises BBCode par des balises HTMLdans le texte
	$n = 1;
	while($n > 0){
		$aTxt = preg_replace($tag, $h, $aTxt, -1, $n);
	}

	// 5- balise vidéo
	//if(function_exists(VidProviderUrl2Player)) $aTxt = preg_replace_callback('/\[video\](.*?)\[\/video\]/is', 'VidProviderUrl2Player', $aTxt);

	// 6- fais le ménage dans les balises restantes
	return preg_replace(array('/\[(.*?)\]/is', '/\[\/(.*?)\]/is'), '', $aTxt);
}

function write_log($message, $db) {

	/*
	// Check database connection
	if(($db instanceof PDO) == false) { 
		return array(status => false, message => 'ERREUR : connexion MySQL non valide');
	}
	*/

	// Check message
	if($message == '') { 
		return array('status' => false, 'message' => 'Message vide'); 
	}

	// Get IP address
	if( ($remote_addr = $_SERVER['REMOTE_ADDR']) == '') { 
		$remote_addr = "Adresse IP inconnue"; 
	}
 
	// Get requested script
	if( ($request_uri = $_SERVER['REQUEST_URI']) == '') { 
		$request_uri = "Adresse inconnue"; 
	} 

	// Mysql
	$sql = "INSERT INTO blog_logs (remote_addr, request_uri, message) VALUES('$remote_addr', '$request_uri','$message')";

	// Execute query and save data
	$result = $db->query($sql);
	if($result) { 
		return array('status' => true, 'message' => 'ok'); 
	} 
	else { 
		return array('status' => false, 'message' => 'ERREUR : écriture impossible dans la base de données.'); 
	}
}


function getRSSContent() {
	//Thanks to https://davidwalsh.name/php-cache-function for cache idea
        $file = "./feed-cache.txt";
        $current_time = time();
        $expire_time = 5 * 60;
        $file_time = filemtime($file);
        if(file_exists($file) && ($current_time - $expire_time < $file_time)) {
        	return file_get_contents($file);
        }
        else {
        	$content = getFreshContent();
                file_put_contents($file, $content);
                return $content;
        }
}
function getFreshContent() {
	$html = "";
        $newsSource = array(
          	array(
                	"title" => "citizenz.info",
               		"url" => "https://www.citizenz.info/feed/rss"
                )
	);
function getFeed($url){
	$rss = simplexml_load_file($url);
        $count = 0;
        $html = '<ul class="list underline justify">';
        foreach($rss->channel->item as$item) {
        	$count++;
                if($count > 4){
                	break;
                }
                $html .= '<li><span class="fa fa-rss font-tiny"></span> <a href="'.htmlspecialchars($item->link).'">'.htmlspecialchars($item->title).'</a></li>';
        }
        $html .= '</ul>';
        return $html;
}
	foreach($newsSource as $source) {
        	$html = getFeed($source["url"]);
	}
	return $html;
}

?>
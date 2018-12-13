<?php

/*
+----------------------------------------------------------------------+
| APC                                                                  |
+----------------------------------------------------------------------+
| Copyright (c) 2006-2011 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.01 of the PHP license,      |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_01.txt                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors du apcu.php d'origine :                                      |
|          Ralf Becker <beckerr@php.net>                               |
|          Rasmus Lerdorf <rasmus@php.net>                             |
|          Ilia Alshanetsky <ilia@prohost.org>                         |
| Auteur des adaptations et du plugin SPIP :                           |
|          JLuc http://contrib.spip.net/JLuc                           |
+----------------------------------------------------------------------+

All other licensing and usage conditions are those of the PHP Group.

*/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip ('inc/autoriser');
if (!autoriser('webmestre'))
	die("Autorisation non accordée : devenez webmestre d'abord.");
include_spip('inc/filtres');
include_spip('inc/cachelab');

$VERSION = '$Id$';

////////// READ OPTIONAL CONFIGURATION FILE ////////////
if (file_exists("apc.conf.php"))
	include("apc.conf.php");
////////////////////////////////////////////////////////

////////// BEGIN OF DEFAULT CONFIG AREA ///////////////////////////////////////////////////////////

defaults('MAXLEN_HTMLCOURT', 1000);	// Couper les html

// (beckerr) I'm using a clear text password here, because I've no good idea how to let
//           users generate a md5 or crypt password in a easy way to fill it in above

// defaults('DATE_FORMAT', "d.m.Y H:i:s");	// German
defaults('DATE_FORMAT', 'Y/m/d H:i:s'); // US

defaults('GRAPH_SIZE', 400); // Image size

//defaults('PROXY', 'tcp://127.0.0.1:8080');

// _CACHE_NAMESPACE est défini par memoization et préfixe chaque nom de cache SPIP
// On ne souhaite pas que cette partie du nom s'affiche sur chaque ligne
define('XRAY_NEPASAFFICHER_DEBUTNOMCACHE', _CACHE_NAMESPACE.'cache:');

////////// END OF DEFAULT CONFIG AREA /////////////////////////////////////////////////////////////

include_spip ('inc/xray_apc');

// copie un peu modifiée de la fonction définie dans public/cacher.php
if (!function_exists('gunzip_page')) {
	function gunzip_page(&$page) {
		if (isset ($page['gz']) and $page['gz']) {
			$page['texte'] = gzuncompress($page['texte']);
			$page['gz'] = false; // ne pas gzuncompress deux fois une meme page
		}
	}
}
else
	die("La fonction gunzip_page ne devrait pas être déjà définie"); // à défaut de disposer de la lib nobug

// Strings utils

function ajuste_longueur_html($str) {
	$court = (!isset($_GET['ZOOM']) or ($_GET['ZOOM'] != 'TEXTELONG'));
	$str = trim(preg_replace("/^\s*$/m", '', $str)); // enlève lignes vides... mais il en reste qqunes
	if ($court and (mb_strlen($str) > MAXLEN_HTMLCOURT))
		$str = mb_substr($str, 0, MAXLEN_HTMLCOURT) . '...';
	elseif (!$str)
		$str = '(vide)';
	return $str;
}

function is_serialized($str) {
	return ($str == serialize(false) || @unserialize($str) !== false);
}

function get_serial_class($serial) {
	$types = array(
		's' => 'string',
		'a' => 'array',
		'b' => 'bool',
		'i' => 'int',
		'd' => 'float',
		'N;' => 'NULL'
	);
	
	$parts = explode(':', $serial, 4);
	return isset($types[$parts[0]]) ? $types[$parts[0]] : trim($parts[2], '"');
}

function get_apc_data($info, &$data_success) {
	if (apcu_exists($info)
		and ($data = apcu_fetch($info, $data_success)) 
		and $data_success 
		and is_array($data) and (count($data) == 1) 
		and is_serialized($data[0])
		) {
		$page = unserialize($data[0]);
		if (is_array($page))
			gunzip_page($page);
		return $page;
	};

	$data_success = false;
	return null;
}

function joli_contexte($contexte) {
	global $MY_SELF;
	$return = '';
	if (!$contexte)
		return '';
	if (!is_array($contexte))
		return $contexte;
	foreach ($contexte as $var => $val) {
		$print = print_r($val, 1);
		if (!is_array($val) and (!$val or (strpos("\n", $val) === false))) {
			$ligne = "[$var] => $val";
			if (strlen($val) < 100) {
				$url = parametre_url (parametre_url($MY_SELF,'WHERE', 'CONTEXTE'), 
									  'SEARCH', "\\[$var\\] => $val$");
				$title = "Voir tous les caches ayant cette même valeur de contexte";
				$return .= "<a href='$url' title='$title'><xmp>[$var] => $val</xmp></a>";
				if (substr($var,0,3)== 'id_')
					$return .= bouton_objet(substr($var,3), $val, $contexte);
			}
			else
				$return .= "<xmp>$ligne</xmp>";
			$return .= "<br>";
		}
		else
			$return .= "<xmp>[$var] => (".gettype($val).") $print</xmp>";
	};
	return $return;
}

function joli_cache($extra) {
	if (is_array($extra)
		and isset($extra['texte']))
		$extra['texte'] = ajuste_longueur_html($extra['texte']);
		// sinon c'est pas un squelette spip, par exemple une textwheel
		// ou juste un talon ou juste une des métadonnées du cache

	$print=print_r($extra,1);

	if (!is_array($extra))
		return "<xmp>".ajuste_longueur_html($print)."</xmp>";

	// On enlève 'Array( ' au début et ')' à la fin
	$print = trim(substr($print, 5), " (\n\r\t");
	$print = substr ($print, 0, -1);

	// rien à améliorer s'il n'y a ni la source ni le squelette
	if (!isset($extra['source']) and !isset($extra['squelette']))
		return "<xmp>$print</xmp>";

	// [squelette] => html_5731a2e40776724746309c16569cac40
	// et [source] => plugins/paeco/squelettes/inclure/element/tag-rubrique.html
	$print = preg_replace_callback("/\[(squelette|source)\]\s*=>\s*(html_[a-f0-9]{32}+|[\w_\.\/\-]+\.html)$/im",
		function($match)
		{
			if (!defined('_SPIP_ECRIRE_SCRIPT'))
				spip_initialisation_suite();	// pour define(_DIR_CACHE)

			switch ($match[1]) {
			case 'squelette' : // cache squelette intermédiaire, en php
				$source = trim(_DIR_CACHE, '/').'/skel/'.$match[2].'.php';
				$title = "Squelette compilé : cache intermédiaire en php";
				break;
			case 'source' :
				$source = '../'.$match[2];
				$title = "Source du squelette SPIP, avec boucles, balises etc";
				break;
			}
			return "[{$match[1]}] => </xmp><a title='{$title}' 
						href='".generer_url_ecrire('xray', "SOURCE=$source")."' 
						target='blank'><xmp>{$match[2]}</xmp> <small>&#128279;</small></a><xmp>";
		}, 
		$print);
	$print = preg_replace('/^    /m', '', $print);
	return "<xmp>$print</xmp>";;
}

function bouton_session($id_session, $url_session) {
	if (function_exists('cachelab_cibler')) {
		$title = cachelab_cibler('get_html', array('chemin'=>'xray_marqueur_visible_'.$id_session))."\n";
	}
	else
		$title = 'Installez CacheLab pour bénéficier d’informations sur cette session.';
	$title = preg_replace("/\n+/", "\n", $title);
	$title .= "\nVoir tous les caches sessionnés de cet internaute";
	return "<a href=\"$url_session\" title=\"$title\">[session]</a>";
}

function bouton_objet($objet, $id_objet, $contexte) {
	$objet_visible = $objet;
	if ($objet == 'secteur')
		$objet = 'rubrique';
	elseif (($objet == 'objet')	and isset ($contexte['objet']))
	{
		$objet_visible = $objet = $contexte['objet'];
	};
global $MY_SELF;
	return "<a href='/ecrire/?exec=$objet&id_$objet=$id_objet' target='blank' 
				style='float: right'
				title=\"" . attribut_html(generer_info_entite($id_objet, $objet, 'titre', 'etoile')) . "\">
				[voir $objet_visible]
			</a>
			";
}

if (!function_exists('plugin_est_actif')) {
	function plugin_est_actif($prefixe) {
		$f = chercher_filtre('info_plugin');
		return $f($prefixe, 'est_actif');
	}
}

function antislash ($str) {
	return str_replace('/', '\/', $str);
}

define ('XRAY_PATTERN_SESSION', '/_([a-f0-9]{8}|)$/i');
define ('XRAY_PATTERN_SESSION_AUTH', '/_([a-f0-9]{8})$/i');
define ('XRAY_PATTERN_SESSION_ANON', '/_$/i');
define ('XRAY_PATTERN_NON_SESSION', '/[^_](?<!_[a-f0-9]{8})$/i');
define ('XRAY_PATTERN_TALON', '/^(.*)(?<!_[a-f0-9]{8})(?<!_)(_([a-f0-9]{8})?|)$/i');

if (!function_exists('cache_est_sessionne')) {
	function cache_est_sessionne ($nomcache) {
		if (preg_match (XRAY_PATTERN_SESSION_AUTH, $nomcache))
			return 'session_auth';
		elseif (preg_match (XRAY_PATTERN_SESSION_ANON, $nomcache))
			return 'session_anon';
		else 
			return false;
	}

	function cache_est_talon ($nomcache,&$data='') {
		if (preg_match(XRAY_PATTERN_SESSION, $nomcache))
			return false;
		if (!is_array($data)) // textwheels par exemple
			return false;
		return !isset($data['contexte']);
	}
}

function cache_get_squelette($cle) {
	$squelette = substr(str_replace(XRAY_NEPASAFFICHER_DEBUTNOMCACHE, '', $cle), 33);
	$squelette = preg_replace(XRAY_PATTERN_SESSION, '', $squelette);
	return $squelette;
}

////////////////////////////////////////////////////////////////////////

// "define if not defined"
function defaults($d, $v) {
	if (!defined($d))
		define($d, $v); // or just @define(...)
}

// rewrite $PHP_SELF to block XSS attacks
//
$PHP_SELF = isset($_SERVER['PHP_SELF']) ? htmlentities(strip_tags($_SERVER['PHP_SELF'], ''), ENT_QUOTES, 'UTF-8') : '';

$time     = time();
$host     = php_uname('n');
if ($host) {
	$host = '(' . $host . ')';
}
if (isset($_SERVER['SERVER_ADDR'])) {
	$host .= ' (' . $_SERVER['SERVER_ADDR'] . ')';
}

// operation constants
define('OB_HOST_STATS', 1);
define('OB_USER_CACHE', 2);
define('OB_VERSION_CHECK', 3);
define('OB_CACHELAB', 4);

// check validity of input variables
$vardom = array(
	'exec' => '/^[a-zA-Z_\-0-9]+$/', // pour #URL_ECRIRE{xray}
	'OB' => '/^\d+$/', // operational mode switch
	'CC' => '/^[01]$/', // clear cache requested
	'PP' => '/^[01]$/', // Purger Précache de compilation des squelettes en plus de vider le cache APC user
	'DU' => '/^.*$/', // Delete User Key
	'SH' => '/^[a-z0-9]*$/', // shared object description
	
	'IMG' => '/^[123]$/', // image to generate
	'SOURCE' => '/^[a-z0-9\-_\/\.]+$/', // file source to display
//	'LO' => '/^1$/', // login requested
	'TYPELISTE' => '/^(caches|squelettes)$/',
	'COUNT' => '/^\d+$/', // number of line displayed in list
	'S_KEY' => '/^[AHSMCDTZ]$/', // first sort key
	'SORT' => '/^[DA]$/', // second sort key
	'AGGR' => '/^\d+$/', // aggregation by dir level
	'SEARCH' => '~.*~',
	'TYPECACHE' => '/^(|ALL|NON_SESSIONS|SESSIONS|SESSIONS_AUTH|SESSIONS_NONAUTH|SESSIONS_TALON|FORMULAIRES)$/', //
	'ZOOM' => '/^(|TEXTECOURT|TEXTELONG)$/', //
	'WHERE' => '/^(|ALL|HTML|META|CONTEXTE)$/', // recherche dans le contenu
	'EXTRA' => '/^(|CONTEXTE|CONTEXTES_SPECIAUX|HTML_COURT|INFO_AUTEUR|INFO_OBJET_SPECIAL|INVALIDEURS|INVALIDEURS_SPECIAUX|INCLUSIONS'
		.(plugin_est_actif('macrosession') ? '|MACROSESSIONS|MACROAUTORISER' : '')
		.')$/'		// Affichage pour chaque élément de la liste
);

global $MYREQUEST; // fix apcu
	$MYREQUEST = array();

// handle POST and GET requests
if (empty($_REQUEST)) {
	if (!empty($_GET) && !empty($_POST)) {
		$_REQUEST = array_merge($_GET, $_POST);
	} else if (!empty($_GET)) {
		$_REQUEST = $_GET;
	} else if (!empty($_POST)) {
		$_REQUEST = $_POST;
	} else {
		$_REQUEST = array();
	}
}

// check parameter syntax
foreach ($vardom as $var => $dom) {
	if (!isset($_REQUEST[$var]))
		$MYREQUEST[$var] = NULL;
	else if (!is_array($_REQUEST[$var]) && preg_match($dom . 'D', $_REQUEST[$var]))
		$MYREQUEST[$var] = $_REQUEST[$var];
	else {
		echo "<xmp>ERREUR avec parametre d'url « $var » qui vaut « {$_REQUEST[$var]} »</xmp>";
		$MYREQUEST[$var] = $_REQUEST[$var] = NULL;
	}
}

// check parameter semantics
if (empty($MYREQUEST['S_KEY']))
	$MYREQUEST['S_KEY'] = "H";
if (empty($MYREQUEST['SORT']))
	$MYREQUEST['SORT'] = "D";
if (empty($MYREQUEST['OB']))
	$MYREQUEST['OB'] = OB_HOST_STATS;
if (!isset($MYREQUEST['COUNT']))
	$MYREQUEST['COUNT'] = 20;
if (!isset($MYREQUEST['EXTRA']))
	$MYREQUEST['EXTRA'] = '';
if (!isset($MYREQUEST['ZOOM']))
	$MYREQUEST['ZOOM'] = 'TEXTECOURT';
if (!isset($MYREQUEST['TYPELISTE']))
	$MYREQUEST['TYPELISTE'] = 'caches';

global $MY_SELF; // fix apcu
global $MY_SELF_WO_SORT; // fix apcu
$MY_SELF_WO_SORT = "$PHP_SELF" . "?COUNT=" . $MYREQUEST['COUNT'] . "&SEARCH=" . $MYREQUEST['SEARCH'] . "&TYPECACHE=" . $MYREQUEST['TYPECACHE'] . "&ZOOM=" . $MYREQUEST['ZOOM'] . "&EXTRA=" . $MYREQUEST['EXTRA'] . "&WHERE=" . $MYREQUEST['WHERE'] . "&exec=" . $MYREQUEST['exec'] . "&OB=" . $MYREQUEST['OB']. "&TYPELISTE=" . $MYREQUEST['TYPELISTE'];
$MY_SELF = $MY_SELF_WO_SORT . "&S_KEY=" . $MYREQUEST['S_KEY'] . "&SORT=" . $MYREQUEST['SORT'];

$self_pour_lien = 
	"http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" 
	. $_SERVER['SERVER_NAME']
	// parametre_url fait un urlencode bienvenu pour les regexp qui peuvent contenir des ?
	. parametre_url($_SERVER['REQUEST_URI'], 'SEARCH', @$_REQUEST['SEARCH']);

global $IMG_BASE;
$IMG_BASE = "$PHP_SELF" . "?exec=" . $MYREQUEST['exec'];

// clear APC cache
if (isset($MYREQUEST['CC']) && $MYREQUEST['CC']) {
	apcu_clear_cache();
}

// clear APC & SPIP cache
if (isset($MYREQUEST['PP']) && $MYREQUEST['PP']) {
	include_spip('inc/invalideur');
	purger_repertoire(_DIR_SKELS);
	apcu_clear_cache();
	ecrire_meta('cache_mark', time());
}

if (!empty($MYREQUEST['DU'])) {
	apcu_delete($MYREQUEST['DU']);
}

if (!function_exists('apcu_cache_info')) {
	echo "No cache info available.  APC does not appear to be running.";
	exit;
}

$cache = apcu_cache_info();

$mem = apcu_sma_info();

// don't cache this page
//
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

function duration($ts)
{
	global $time;
	$years = (int) ((($time - $ts) / (7 * 86400)) / 52.177457);
	$rem   = (int) (($time - $ts) - ($years * 52.177457 * 7 * 86400));
	$weeks = (int) (($rem) / (7 * 86400));
	$days  = (int) (($rem) / 86400) - $weeks * 7;
	$hours = (int) (($rem) / 3600) - $days * 24 - $weeks * 7 * 24;
	$mins  = (int) (($rem) / 60) - $hours * 60 - $days * 24 * 60 - $weeks * 7 * 24 * 60;
	$str   = '';
	if ($years == 1)
		$str .= "$years year, ";
	if ($years > 1)
		$str .= "$years years, ";
	if ($weeks == 1)
		$str .= "$weeks week, ";
	if ($weeks > 1)
		$str .= "$weeks weeks, ";
	if ($days == 1)
		$str .= "$days day,";
	if ($days > 1)
		$str .= "$days days,";
	if ($hours == 1)
		$str .= " $hours hour and";
	if ($hours > 1)
		$str .= " $hours hours and";
	if ($mins == 1)
		$str .= " 1 minute";
	else
		$str .= " $mins minutes";
	return $str;
}

// create graphics
//
function graphics_avail()
{
	return extension_loaded('gd');
}
if (isset($MYREQUEST['IMG'])) {
	if (!graphics_avail()) {
		exit(0);
	}
	
	function fill_arc($im, $centerX, $centerY, $diameter, $start, $end, $color1, $color2, $text = '', $placeindex = 0)
	{
		$r = $diameter / 2;
		$w = deg2rad((360 + $start + ($end - $start) / 2) % 360);
		
		
		if (function_exists("imagefilledarc")) {
			// exists only if GD 2.0.1 is avaliable
			imagefilledarc($im, $centerX + 1, $centerY + 1, $diameter, $diameter, $start, $end, $color1, IMG_ARC_PIE);
			imagefilledarc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color2, IMG_ARC_PIE);
			imagefilledarc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color1, IMG_ARC_NOFILL | IMG_ARC_EDGED);
		} else {
			imagearc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($start)) * $r, $centerY + sin(deg2rad($start)) * $r, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($start + 1)) * $r, $centerY + sin(deg2rad($start)) * $r, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($end - 1)) * $r, $centerY + sin(deg2rad($end)) * $r, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($end)) * $r, $centerY + sin(deg2rad($end)) * $r, $color2);
			imagefill($im, $centerX + $r * cos($w) / 2, $centerY + $r * sin($w) / 2, $color2);
		}
		if ($text) {
			if ($placeindex > 0) {
				imageline($im, $centerX + $r * cos($w) / 2, $centerY + $r * sin($w) / 2, $diameter, $placeindex * 12, $color1);
				imagestring($im, 4, $diameter, $placeindex * 12, $text, $color1);
				
			} else {
				imagestring($im, 4, $centerX + $r * cos($w) / 2, $centerY + $r * sin($w) / 2, $text, $color1);
			}
		}
	}
	
	function text_arc($im, $centerX, $centerY, $diameter, $start, $end, $color1, $text, $placeindex = 0)
	{
		$r = $diameter / 2;
		$w = deg2rad((360 + $start + ($end - $start) / 2) % 360);
		
		if ($placeindex > 0) {
			imageline($im, $centerX + $r * cos($w) / 2, $centerY + $r * sin($w) / 2, $diameter, $placeindex * 12, $color1);
			imagestring($im, 4, $diameter, $placeindex * 12, $text, $color1);
			
		} else {
			imagestring($im, 4, $centerX + $r * cos($w) / 2, $centerY + $r * sin($w) / 2, $text, $color1);
		}
	}
	
	function fill_box($im, $x, $y, $w, $h, $color1, $color2, $text = '', $placeindex = '')
	{
		global $col_black;
		$x1 = $x + $w - 1;
		$y1 = $y + $h - 1;
		
		imagerectangle($im, $x, $y1, $x1 + 1, $y + 1, $col_black);
		if ($y1 > $y)
			imagefilledrectangle($im, $x, $y, $x1, $y1, $color2);
		else
			imagefilledrectangle($im, $x, $y1, $x1, $y, $color2);
		imagerectangle($im, $x, $y1, $x1, $y, $color1);
		if ($text) {
			if ($placeindex > 0) {
				
				if ($placeindex < 16) {
					$px = 5;
					$py = $placeindex * 12 + 6;
					imagefilledrectangle($im, $px + 90, $py + 3, $px + 90 - 4, $py - 3, $color2);
					imageline($im, $x, $y + $h / 2, $px + 90, $py, $color2);
					imagestring($im, 2, $px, $py - 6, $text, $color1);
					
				} else {
					if ($placeindex < 31) {
						$px = $x + 40 * 2;
						$py = ($placeindex - 15) * 12 + 6;
					} else {
						$px = $x + 40 * 2 + 100 * intval(($placeindex - 15) / 15);
						$py = ($placeindex % 15) * 12 + 6;
					}
					imagefilledrectangle($im, $px, $py + 3, $px - 4, $py - 3, $color2);
					imageline($im, $x + $w, $y + $h / 2, $px, $py, $color2);
					imagestring($im, 2, $px + 2, $py - 6, $text, $color1);
				}
			} else {
				imagestring($im, 4, $x + 5, $y1 - 16, $text, $color1);
			}
		}
	}
	
	
	$size = GRAPH_SIZE / 3; // image size
	if ($MYREQUEST['IMG'] == 3)
		$image = imagecreate(3 * $size + 200, 2 * $size + 150);
	else
		$image = imagecreate($size + 50, $size + 10);
	
	$col_white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$col_red   = imagecolorallocate($image, 0xD0, 0x60, 0x30);
	$col_green = imagecolorallocate($image, 0x60, 0xF0, 0x60);
	$col_black = imagecolorallocate($image, 0, 0, 0);
	imagecolortransparent($image, $col_white);
	
	switch ($MYREQUEST['IMG']) {
		
		case 1:
			$s    = $mem['num_seg'] * $mem['seg_size'];
			$a    = $mem['avail_mem'];
			$x    = $y = $size / 2;
			$fuzz = 0.000001;
			
			// This block of code creates the pie chart.  It is a lot more complex than you
			// would expect because we try to visualize any memory fragmentation as well.
			$angle_from       = 0;
			$string_placement = array();
			for ($i = 0; $i < $mem['num_seg']; $i++) {
				$ptr  = 0;
				$free = $mem['block_lists'][$i];
				uasort($free, 'block_sort');
				foreach ($free as $block) {
					if ($block['offset'] != $ptr) { // Used block
						$angle_to = $angle_from + ($block['offset'] - $ptr) / $s;
						if (($angle_to + $fuzz) > 1)
							$angle_to = 1;
						if (($angle_to * 360) - ($angle_from * 360) >= 1) {
							fill_arc($image, $x, $y, $size, $angle_from * 360, $angle_to * 360, $col_black, $col_red);
							if (($angle_to - $angle_from) > 0.05) {
								array_push($string_placement, array(
									$angle_from,
									$angle_to
								));
							}
						}
						$angle_from = $angle_to;
					}
					$angle_to = $angle_from + ($block['size']) / $s;
					if (($angle_to + $fuzz) > 1)
						$angle_to = 1;
					if (($angle_to * 360) - ($angle_from * 360) >= 1) {
						fill_arc($image, $x, $y, $size, $angle_from * 360, $angle_to * 360, $col_black, $col_green);
						if (($angle_to - $angle_from) > 0.05) {
							array_push($string_placement, array(
								$angle_from,
								$angle_to
							));
						}
					}
					$angle_from = $angle_to;
					$ptr        = $block['offset'] + $block['size'];
				}
				if ($ptr < $mem['seg_size']) { // memory at the end
					$angle_to = $angle_from + ($mem['seg_size'] - $ptr) / $s;
					if (($angle_to + $fuzz) > 1)
						$angle_to = 1;
					fill_arc($image, $x, $y, $size, $angle_from * 360, $angle_to * 360, $col_black, $col_red);
					if (($angle_to - $angle_from) > 0.05) {
						array_push($string_placement, array(
							$angle_from,
							$angle_to
						));
					}
				}
			}
			foreach ($string_placement as $angle) {
				text_arc($image, $x, $y, $size, $angle[0] * 360, $angle[1] * 360, $col_black, bsize($s * ($angle[1] - $angle[0])));
			}
			break;
		
		case 2:
			$s = $cache['num_hits'] + $cache['num_misses'];
			$a = $cache['num_hits'];
			
			fill_box($image, 30, $size, 50, $s ? (-$a * ($size - 21) / $s) : 0, $col_black, $col_green, sprintf("%.1f%%", $s ? $cache['num_hits'] * 100 / $s : 0));
			fill_box($image, 130, $size, 50, $s ? -max(4, ($s - $a) * ($size - 21) / $s) : 0, $col_black, $col_red, sprintf("%.1f%%", $s ? $cache['num_misses'] * 100 / $s : 0));
			break;
		
		case 3:
			$s = $mem['num_seg'] * $mem['seg_size'];
			$a = $mem['avail_mem'];
			$x = 130;
			$y = 1;
			$j = 1;
			
			// This block of code creates the bar chart.  It is a lot more complex than you
			// would expect because we try to visualize any memory fragmentation as well.
			for ($i = 0; $i < $mem['num_seg']; $i++) {
				$ptr  = 0;
				$free = $mem['block_lists'][$i];
				uasort($free, 'block_sort');
				foreach ($free as $block) {
					if ($block['offset'] != $ptr) { // Used block
						$h = (GRAPH_SIZE - 5) * ($block['offset'] - $ptr) / $s;
						if ($h > 0) {
							$j++;
							if ($j < 75)
								fill_box($image, $x, $y, 50, $h, $col_black, $col_red, bsize($block['offset'] - $ptr), $j);
							else
								fill_box($image, $x, $y, 50, $h, $col_black, $col_red);
						}
						$y += $h;
					}
					$h = (GRAPH_SIZE - 5) * ($block['size']) / $s;
					if ($h > 0) {
						$j++;
						if ($j < 75)
							fill_box($image, $x, $y, 50, $h, $col_black, $col_green, bsize($block['size']), $j);
						else
							fill_box($image, $x, $y, 50, $h, $col_black, $col_green);
					}
					$y += $h;
					$ptr = $block['offset'] + $block['size'];
				}
				if ($ptr < $mem['seg_size']) { // memory at the end
					$h = (GRAPH_SIZE - 5) * ($mem['seg_size'] - $ptr) / $s;
					if ($h > 0) {
						fill_box($image, $x, $y, 50, $h, $col_black, $col_red, bsize($mem['seg_size'] - $ptr), $j++);
					}
				}
			}
			break;
		
		case 4:
			$s = $cache['num_hits'] + $cache['num_misses'];
			$a = $cache['num_hits'];
			
			fill_box($image, 30, $size, 50, $s ? -$a * ($size - 21) / $s : 0, $col_black, $col_green, sprintf("%.1f%%", $s ? $cache['num_hits'] * 100 / $s : 0));
			fill_box($image, 130, $size, 50, $s ? -max(4, ($s - $a) * ($size - 21) / $s) : 0, $col_black, $col_red, sprintf("%.1f%%", $s ? $cache['num_misses'] * 100 / $s : 0));
			break;
	}
	
	header("Content-type: image/png");
	imagepng($image);
	exit;
}

if (isset($MYREQUEST['SOURCE']) and $MYREQUEST['SOURCE']) {
	echo '<xmp>'.file_get_contents ($MYREQUEST['SOURCE']).'</xmp>';
	exit;
}

// pretty printer for byte values
//
function bsize($s)
{
	foreach (array(
		'',
		'K',
		'M',
		'G'
	) as $i => $k) {
		if ($s < 1024)
			break;
		$s /= 1024;
	}
	return sprintf("%5.1f %sBytes", $s, $k);
}

// sortable table header in "scripts for this host" view
function sortheader($key, $name, $extra = '')
{
	global $MYREQUEST;
	
	// fix apcu l'affichage des headers ne doit pas changer $MYREQUEST
	$sort = $MYREQUEST['SORT'];
	if (!$sort)
		$sort = 'D';
	if ($MYREQUEST['S_KEY'] == $key)
		$sort = (($sort == 'A') ? 'D' : 'A');

//	global $MY_SELF_WO_SORT; // fix apcu : il faut global ici aussi
//	$url = "$MY_SELF_WO_SORT$extra&S_KEY=$key&SORT=$SORT";
global $MY_SELF;
	$url = parametre_url(parametre_url($MY_SELF.$extra,'S_KEY',$key),'SORT', $sort);
	return "<a class=sortable href='$url'>$name</a>";
}

// create menu entry
function menu_entry($ob, $title)
{
	global $MYREQUEST;
	global $MY_SELF; // fix apcu
	if ($MYREQUEST['OB'] != $ob) {
		return "<li><a href='" . parametre_url($MY_SELF, 'OB', $ob) . "'>$title</a></li>";
	} else if (empty($MYREQUEST['SH'])) {
		return "<li><span class=active>$title</span></li>";
	} else {
		return "<li><a class=\"child_active\" href='$MY_SELF'>$title</a></li>";
	}
}

function block_sort($array1, $array2)
{
	if ($array1['offset'] > $array2['offset']) {
		return 1;
	} else {
		return -1;
	}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head><title>XRay - APCu Infos sur les caches SPIP</title>
<style><!--
body { background:white; font-size:100.01%; margin:0; padding:0; }
body,p,td,th,input,submit { font-size:0.8em;font-family:arial,helvetica,sans-serif; }
* html body   {font-size:0.8em}
* html p      {font-size:0.8em}
* html td     {font-size:0.8em}
* html th     {font-size:0.8em}
* html input  {font-size:0.8em}
* html submit {font-size:0.8em}
td { vertical-align:top }
a { color:black; font-weight:none; text-decoration:none; }
a:hover { text-decoration:underline; }
div.content { padding:1em 1em 1em 1em; width:97%; z-index:100; }

h1.apc { background:rgb(153,153,204); margin:0; padding:0.5em 1em 0.5em 1em; }
* html h1.apc { margin-bottom:-7px; }
h1.apc a:hover { text-decoration:none; color:rgb(90,90,90); }
h1.apc div.logo {display: inline}
h1.apc div.logo span.logo {
	background:rgb(119,123,180);
	color:black;
	border-right: solid black 1px;
	border-bottom: solid black 1px;
	font-style:italic;
	font-size:1em;
	padding-left:1.2em;
	padding-right:1.2em;
	text-align:right;
	}
h1.apc div.logo span.name { color:white; font-size:0.7em; padding:0 0.8em 0 2em; }
h1.apc div.nameinfo { color:white; display:inline; font-size:0.6em; margin-left: 1em; }
h1.apc div.nameinfo a { color:white; margin-left: 3em; }
h1.apc div.nameinfo img {margin-bottom: -10px; margin-right : 1em;}
h1.apc div.copy { color:black; font-size:0.4em; position:absolute; right:1em; }
hr.apc { display: none; }

ol,menu { margin:1em 0 0 0; padding:0.2em; margin-left:1em;}
ol.menu li { display:inline; margin-right:0.7em; list-style:none; font-size:85%}
ol.menu a {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	margin-left: 5px;
	}
ol.menu a.child_active {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	border-left: solid black 5px;
	margin-left: 0px;
	}
ol.menu span.active {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:black;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	border-left: solid black 5px;
	}
ol.menu span.inactive {
	background:rgb(193,193,244);
	border:solid rgb(182,182,233) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	margin-left: 5px;
	}
ol.menu a:hover {
	background:rgb(193,193,244);
	text-decoration:none;
	}


div.info {
	background:rgb(204,204,204);
	border:solid rgb(204,204,204) 1px;
	margin-bottom:1em;
	}
div.info h2 {
	background:rgb(204,204,204);
	color:black;
	font-size:1em;
	margin:0;
	padding:0.1em 1em 0.1em 1em;
	}
div.info table {
	border:solid rgb(204,204,204) 1px;
	border-spacing:0;
	width:100%;
	}
div.info table th {
	background:rgb(204,204,204);
	color:black;
	margin:0;
	padding:0.1em 1em 0.1em 1em;
	}
div.info table th a.sortable { color:black; }
div.info table tr.tr-0 { background:rgb(238,238,238); }
div.info table tr.tr-1 { background:rgb(221,221,221); }
div.info table td { padding:0.3em 1em 0.3em 1em; }
div.info table td.td-0 { border-right:solid rgb(102,102,153) 1px; white-space:nowrap; }
div.info table td.td-n { border-right:solid rgb(102,102,153) 1px; }
div.info table td h3 {
	color:black;
	font-size:1.1em;
	margin-left:-0.3em;
	}

div.graph { margin-bottom:1em }
div.graph h2 { background:rgb(204,204,204);; color:black; font-size:1em; margin:0; padding:0.1em 1em 0.1em 1em; }
div.graph table { border:solid rgb(204,204,204) 1px; color:black; font-weight:normal; width:100%; }
div.graph table td.td-0 { background:rgb(238,238,238); }
div.graph table td.td-1 { background:rgb(221,221,221); }
div.graph table td { padding:0.2em 1em 0.4em 1em; }

div.div1,div.div2 { margin-bottom:1em; width:35em; }
div.div3 { position:absolute; left:40em; top:1em; width:580px; }
//div.div3 { position:absolute; left:37em; top:1em; right:1em; }

div.sorting { margin:1.5em 0em 1.5em 2em }
.center { text-align:center }
.aright { float: right; }
.right { text-align:right }
.ok { color:rgb(0,200,0); font-weight:bold}
.failed { color:rgb(200,0,0); font-weight:bold}

span.box {
	border: black solid 1px;
	border-right:solid black 2px;
	border-bottom:solid black 2px;
	padding:0 0.5em 0 0.5em;
	margin-right:1em;
}
span.green { background:#60F060; padding:0 0.5em 0 0.5em}
span.red { background:#D06030; padding:0 0.5em 0 0.5em }

div.authneeded {
	background:rgb(238,238,238);
	border:solid rgb(204,204,204) 1px;
	color:rgb(200,0,0);
	font-size:1.2em;
	font-weight:bold;
	padding:2em;
	text-align:center;
	}

input {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:1em;
	padding:0.1em 0.5em 0.1em 0.5em;
	}

/* xray styles */
xmp { display: inline }
.menuzoom {
	border :  1px solid grey;
	border-radius: 3px;
	padding : 0px 5px 0px 5px;
}

//-->
</style>
</head>
<body>
<div class="head">
	<h1 class="apc">
		<div class="logo"><span class="logo"><a href="http://pecl.php.net/package/APCu">APCu</a></span></div>
		<div class="nameinfo" style="display: inline">
			User Cache
			<a href='https://contrib.spip.net/4946'>
			<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAEwAAABMBDsgnAwAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAARiSURBVFiFxVdNSBtbFP5m8pqpRmw2EhAU/6JtQAStG5VEiC5cCkLBhcQuXIi2i0ppSZ+laGmxghJFwU1cZCe6cFFXKlYENybSQqMENa1SGLUEQ6iZ5CXfW/S9YEwm0YLtgUuY852f7557z5mJIAjCmSiKIgABPyXplyTS6a9rp4YLABLInxDxtyQRRQwMDKCioiItzpteDoeDiqKwpKQkHY74TSavra0lSb569UrNJjMBm83Guro6VVyj0fDt27fUarVp8cnJSX779o2SJKXF/8p0dgCg0+mwtLSEhoYGaLVa1NXVQa/Xw+12w+Px4N27d+jo6MCLFy/S+peXl2NzcxOKoqjmyFiBwsJCqkksFiNJjo2Nqfp//vyZQ0NDqrgoCAIySWtrKwDgx48fF3sZABAOhwEA9+/fV/XPz89HQUGBKp61De/duwcAePjwISorK7G7uwtFUdDU1ASLxQIAKC0tVfX3+Xyq7QcAEAQh4xHcvXuXiqLw69evXFtb4/n5OePxOBcXF/np0yeS5JMnT1T9p6eneXBwoN4p2QgA4PDwsOo9WF9fpyiKqr6PHj1iLBbj7du3VQnELipycnJSjCRJYktLC3t6eri/v0+SHB8fp81mY2FhYYr9xRj19fUkSYvFkp2ATqej3+9nW1sbATA/P58GgyFpbWxskCStVmsKJggCAdDr9bKrq+vnLRdFBgIBvnz5MjsBg8FAknz8+DGLiop4dnamWvp08vz5cwIgSb558yaRZGFhgaurq9cbRIeHh+ju7kZ9fX2SvrOzE8XFxXA6nZBlOaEPBAJwOp1pYy0vL2N0dBSSJKUOJFEU01YAAO12+5V3v7e3lxjHlytQVVVFkjSbzdcbxVNTUzg6OkJZWRm0Wq1qBQ4PD+F2uxGJRNLG2d3dxdHREZqbm/Hhw4erVwAArVZr1t3Pz88n7epyBQBwdnaWKysrmSsQDAaxvb0Nj8eT0K2srKC9vR2SJAEABgcHYTKZ8OzZM/j9fgDA5uZmyo53dnaSdMvLy5iZmUm9B6Io/nORUaahAoCrq6skyZqaGlWbvLy8FJ3JZCJJlpeXJ2OXCVxeBoOB79+/pyzL/P79O6PRKEkyGAwyEAjw4OCAnZ2dGUkDYFNTE0mmDq5sBPr6+rLegY8fP1Kv17O3t5d37txJG+fBgweMRCLMzc29HgGj0UhZllWTx+Nx9vf3U6fTcW9vjycnJ+zv7+etW7cSMfR6Pb1eLxcXF1NzaDSajAT+vxfhcJgTExOJsVtdXU2S7OvrS3pnPH36lMFgkF++fKHD4eDr16/p8Xjo9/tpNBpTY+MKEo/HQRKhUAiyLEOWZRwfHwMAotFowk5RFIyMjMBoNMLlcqG6uhpmsxlbW1tobGyEz+dLiZ31m/BXRJZl2O32K9n+lj8mmUQjiuLfZPajCIfDmJubw+npKYCf34iCIMDlciEUCv0yAUGj0URjsdiNHMVV5I8fgQDgHID03/PFFvktz/8C/6xwoasnBYAAAAAASUVORK5CYII=' >
			XRay pour SPIP
			</a>
		</div>
		</a>
	<hr class="apc">
</div>

<?php
// Les dossiers de squelettes déclarés dans le paquet.xml comme 'public' ne sont pas accessibles dans le privé
// Pour bénéficier des liens ici, il faut les ajouter dans la $GLOBALS['dossier_squelettes']
// echo "<h2>GLOBALS['dossier_squelettes'] : <pre>". print_r($GLOBALS['dossier_squelettes'],1)."</pre></h2>";
// echo "<h2>_chemin</h2> <pre>".print_r(_chemin(),1)."</p>";
?>


<?php
// Display main Menu
echo <<<EOB
	<ol class=menu>
	<li><a href="$MY_SELF&SH={$MYREQUEST['SH']}">Refresh Data</a></li>
EOB;
echo menu_entry(OB_HOST_STATS, 'View Host Stats'), menu_entry(OB_USER_CACHE, 'User Cache Entries'), menu_entry(OB_VERSION_CHECK, 'Version Check');

if (plugin_est_actif('cachelab'))
	echo menu_entry(OB_CACHELAB, 'CacheLab');

	echo <<<EOB
		<li><a class="aright" href="$MY_SELF&CC=1" onClick="javascript:return confirm('Are you sure?');"
			title="Vider le cache APC user">Vider APC</a>
		</li>
		<li><a class="aright" href="$MY_SELF&PP=1" 
				onClick="javascript:return confirm('Êtes-vous certain de vouloir vider le cache APC user et le dossier skel/ des squelettes compilés ?');"
				title="Vider le cache APC user ET effacer les caches de compilation des squelettes ?">
				Purger SPIP</a>
		</li>
	</ol>
EOB;


// CONTENT
echo <<<EOB
	<div class=content>
EOB;

// MAIN SWITCH STATEMENT

switch ($MYREQUEST['OB']) {
	// -----------------------------------------------
	// Host Stats
	// -----------------------------------------------
	case OB_HOST_STATS:
		$mem_size         = $mem['num_seg'] * $mem['seg_size'];
		$mem_avail        = $mem['avail_mem'];
		$mem_used         = $mem_size - $mem_avail;
		$seg_size         = bsize($mem['seg_size']);
		$req_rate_user    = sprintf("%.2f", $cache['num_hits'] ? (($cache['num_hits'] + $cache['num_misses']) / ($time - $cache['start_time'])) : 0);
		$hit_rate_user    = sprintf("%.2f", $cache['num_hits'] ? (($cache['num_hits']) / ($time - $cache['start_time'])) : 0);
		$miss_rate_user   = sprintf("%.2f", $cache['num_misses'] ? (($cache['num_misses']) / ($time - $cache['start_time'])) : 0);
		$insert_rate_user = sprintf("%.2f", $cache['num_inserts'] ? (($cache['num_inserts']) / ($time - $cache['start_time'])) : 0);
		$apcversion       = phpversion('apcu');
		$phpversion       = phpversion();
		$number_vars      = $cache['num_entries'];
		$size_vars        = bsize($cache['mem_size']);
		$i                = 0;
		$_namespace       = _CACHE_NAMESPACE;

		$meta_derniere_modif = lire_meta('derniere_modif');

		echo "<div class='info div1'><h2>Mémoization SPIP - Le ".date(JOLI_DATE_FORMAT,time())."</h2>
			<table cellspacing=0><tbody>
			<tr class=tr-0><td class=td-0>_CACHE_NAMESPACE</td><td>"._CACHE_NAMESPACE."</td></tr>
			<tr class=tr-0><td class=td-0 title='meta SPIP : derniere_modif'>Dernière invalidation</td><td>".date(JOLI_DATE_FORMAT, $meta_derniere_modif)."</td></tr> 
			<tr class=tr-0><td class=td-0 title='meta spip'>Invalidation de '".XRAY_OBJET_SPECIAL."'</td><td>".date(JOLI_DATE_FORMAT, lire_meta('derniere_modif_'.XRAY_OBJET_SPECIAL))."</td></tr> 
			<tr class=tr-0><td class=td-0 title='meta SPIP : cache_mark'>Dernière purge</td><td>".date(JOLI_DATE_FORMAT, $GLOBALS['meta']['cache_mark'])."</td></tr> ";

		$stats = xray_stats($cache);
		echo xray_stats_print($stats, 'generaux', 'Valides '.XRAY_LABEL_STATS_SPECIALES_EXCLUES);
		echo xray_stats_print($stats, 'speciaux', '+ Valides '.XRAY_LABEL_STATS_SPECIALES);
		echo xray_stats_print($stats, 'invalides', '+ Invalidés par SPIP');
		echo xray_stats_print($stats, 'existent', '= Total caches APC OK');
		echo xray_stats_print($stats, 'fantomes', '+ Caches périmés par APC');
		$nb_cache = count($cache['cache_list']);
		echo "<tr class=tr-0>
			<td class=td-0><b>= Nb total caches APC</b></td><td>$nb_cache</td>
			</tr>";

		echo "</table></div>";

		echo <<< EOB
		<div class="info div1"><h2>General Cache Information</h2>
		<table cellspacing=0><tbody>
		<tr class=tr-0><td class=td-0>APCu Version</td><td>$apcversion</td></tr>
		<tr class=tr-1><td class=td-0>PHP Version</td><td>$phpversion</td></tr>
EOB;

		if (!empty($_SERVER['SERVER_NAME']))
			echo "<tr class=tr-0><td class=td-0>APCu Host</td><td>{$_SERVER['SERVER_NAME']} $host</td></tr>\n";
		if (!empty($_SERVER['SERVER_SOFTWARE']))
			echo "<tr class=tr-1><td class=td-0>Server Software</td><td>{$_SERVER['SERVER_SOFTWARE']}</td></tr>\n";
		
		echo <<<EOB
		<tr class=tr-0><td class=td-0>Shared Memory</td><td>{$mem['num_seg']} Segment(s) with $seg_size
    <br/> ({$cache['memory_type']} memory)
    </td></tr>
EOB;
		echo '<tr class=tr-1><td class=td-0>Start Time</td><td>', date(DATE_FORMAT, $cache['start_time']), '</td></tr>';
		echo '<tr class=tr-0><td class=td-0>Uptime</td><td>', duration($cache['start_time']), '</td></tr>';
		echo <<<EOB
		</tbody></table>
		</div>

		<div class="info div1"><h2>Cache Information</h2>
		<table cellspacing=0>
		<tbody>
    		<tr class=tr-0><td class=td-0>Cached Variables</td><td>$number_vars ($size_vars)</td></tr>
			<tr class=tr-1><td class=td-0>Hits</td><td>{$cache['num_hits']}</td></tr>
			<tr class=tr-0><td class=td-0>Misses</td><td>{$cache['num_misses']}</td></tr>
			<tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td>$req_rate_user cache requests/second</td></tr>
			<tr class=tr-0><td class=td-0>Hit Rate</td><td>$hit_rate_user cache requests/second</td></tr>
			<tr class=tr-1><td class=td-0>Miss Rate</td><td>$miss_rate_user cache requests/second</td></tr>
			<tr class=tr-0><td class=td-0>Insert Rate</td><td>$insert_rate_user cache requests/second</td></tr>
			<tr class=tr-1><td class=td-0>Cache full count</td><td>{$cache['expunges']}</td></tr>
		</tbody>
		</table>
		</div>

		<div class="info div2"><h2>Runtime Settings</h2><table cellspacing=0><tbody>
EOB;
		
		$j = 0;
		foreach (ini_get_all('apcu') as $k => $v) {
			echo "<tr class=tr-$j><td class=td-0>", $k, "</td><td>", str_replace(',', ',<br />', $v['local_value']), "</td></tr>\n";
			$j = 1 - $j;
		}
		
		if ($mem['num_seg'] > 1 || $mem['num_seg'] == 1 && count($mem['block_lists'][0]) > 1)
			$mem_note = "Memory Usage<br /><font size=-2>(multiple slices indicate fragments)</font>";
		else
			$mem_note = "Memory Usage";
		
		echo <<< EOB
		</tbody></table>
		</div>

		<div class="graph div3"><h2>Host Status Diagrams</h2>
		<table cellspacing=0><tbody>
EOB;
		$size = 'width=' . (GRAPH_SIZE * 2 / 3) . ' height=' . (GRAPH_SIZE / 2);
		echo <<<EOB
		<tr>
		<td class=td-0>$mem_note</td>
		<td class=td-1>Hits &amp; Misses</td>
		</tr>
EOB;
		
		echo graphics_avail() ? '<tr>' . "<td class=td-0><img alt='' $size src='{$IMG_BASE}&IMG=1&$time'></td>" . "<td class=td-1><img alt='' $size src='{$IMG_BASE}&IMG=2&$time'></td></tr>\n" : "", '<tr>', '<td class=td-0><span class="green box">&nbsp;</span>Free: ', bsize($mem_avail) . sprintf(" (%.1f%%)", $mem_avail * 100 / $mem_size), "</td>\n", '<td class=td-1><span class="green box">&nbsp;</span>Hits: ', $cache['num_hits'] . @sprintf(" (%.1f%%)", $cache['num_hits'] * 100 / ($cache['num_hits'] + $cache['num_misses'])), "</td>\n", '</tr>', '<tr>', '<td class=td-0><span class="red box">&nbsp;</span>Used: ', bsize($mem_used) . sprintf(" (%.1f%%)", $mem_used * 100 / $mem_size), "</td>\n", '<td class=td-1><span class="red box">&nbsp;</span>Misses: ', $cache['num_misses'] . @sprintf(" (%.1f%%)", $cache['num_misses'] * 100 / ($cache['num_hits'] + $cache['num_misses'])), "</td>\n";
		echo <<< EOB
		</tr>
		</tbody></table>

		<br/>
		<h2>Detailed Memory Usage and Fragmentation</h2>
		<table cellspacing=0><tbody>
		<tr>
		<td class=td-0 colspan=2><br/>
EOB;
		
		// Fragementation: (freeseg - 1) / total_seg
		$nseg = $freeseg = $fragsize = $freetotal = 0;
		for ($i = 0; $i < $mem['num_seg']; $i++) {
			$ptr = 0;
			foreach ($mem['block_lists'][$i] as $block) {
				if ($block['offset'] != $ptr) {
					++$nseg;
				}
				$ptr = $block['offset'] + $block['size'];
				/* Only consider blocks <5M for the fragmentation % */
				if ($block['size'] < (5 * 1024 * 1024))
					$fragsize += $block['size'];
				$freetotal += $block['size'];
			}
			$freeseg += count($mem['block_lists'][$i]);
		}
		
		if ($freeseg > 1) {
			$frag = sprintf("%.2f%% (%s out of %s in %d fragments)", ($fragsize / $freetotal) * 100, bsize($fragsize), bsize($freetotal), $freeseg);
		} else {
			$frag = "0%";
		}
		
		if (graphics_avail()) {
			$size = 'width=' . (2 * GRAPH_SIZE + 150) . ' height=' . (GRAPH_SIZE + 10);
			echo <<<EOB
			<img alt="" $size src="{$IMG_BASE}&IMG=3&$time">
EOB;
		}
		echo <<<EOB
		</br>Fragmentation: $frag
		</td>
		</tr>
EOB;
		if (isset($mem['adist'])) {
			foreach ($mem['adist'] as $i => $v) {
				$cur = pow(2, $i);
				$nxt = pow(2, $i + 1) - 1;
				if ($i == 0)
					$range = "1";
				else
					$range = "$cur - $nxt";
				echo "<tr><th align=right>$range</th><td align=right>$v</td></tr>\n";
			}
		}
		echo <<<EOB
		</tbody></table>
		</div>
EOB;
		
		break;

	// -----------------------------------------------
	// User Cache Entries
	// -----------------------------------------------
	case OB_USER_CACHE:
		$cols = 7;
		
		echo '<form>
			<input type="hidden" name="OB" value="'.$MYREQUEST['OB'].'">
			<input type="hidden" name="exec" value="'.$MYREQUEST['exec'].'">
			<input type="hidden" name="S_KEY" value="'.$MYREQUEST['S_KEY'].'">
			<input type="hidden" name="TYPELISTE" value="'.$MYREQUEST['TYPELISTE'].'">
		<b>Affichage extra:</b> 
		<select name=EXTRA  onChange="form.submit()">
			<option value="" ', $MYREQUEST['EXTRA'] == '' ? ' selected' : '', '></option> 
			<option value=CONTEXTE ', $MYREQUEST['EXTRA'] == 'CONTEXTE' ? ' selected' : '', '>Contexte</option>
			<option value=CONTEXTES_SPECIAUX ', $MYREQUEST['EXTRA'] == 'CONTEXTES_SPECIAUX' ? ' selected' : '', '>Contextes spécifiques</option>
			<option value=HTML_COURT ', $MYREQUEST['EXTRA'] == 'HTML_COURT' ? ' selected' : '', '>HTML (...)</option>
			<option value=INFO_AUTEUR ', $MYREQUEST['EXTRA'] == 'INFO_AUTEUR' ? ' selected' : '', '>Infos auteur</option>
			<option value=INFO_OBJET_SPECIAL ', $MYREQUEST['EXTRA'] == 'INFO_OBJET_SPECIAL' ? ' selected' : '', '>Infos '.XRAY_OBJET_SPECIAL.'</option>
			<option value=INVALIDEURS ', $MYREQUEST['EXTRA'] == 'INVALIDEURS' ? ' selected' : '', '>Invalideurs</option>
			<option value=INVALIDEURS_SPECIAUX ', $MYREQUEST['EXTRA'] == 'INVALIDEURS_SPECIAUX' ? ' selected' : '', '>Invalideurs spécifiques</option>
			<option value=INCLUSIONS ', $MYREQUEST['EXTRA'] == 'INCLUSIONS' ? ' selected' : '', '>&lt;INCLURE&gt;</option>
			<option value=MACROSESSIONS ', $MYREQUEST['EXTRA'] == 'MACROSESSIONS' ? ' selected' : '', '>#_SESSION</option>
			<option value=MACROAUTORISER ', $MYREQUEST['EXTRA'] == 'MACROAUTORISER' ? ' selected' : '', '>#_AUTORISER_SI</option>
		</select>';
	
		echo "<span style='margin-left: 2em; '></span>";
		if ($MYREQUEST['TYPELISTE']=='squelettes') {
			echo '<a href="'.parametre_url($MY_SELF, 'TYPELISTE', 'caches').'">Caches</a> 
			| <b>Squelettes</b>';
		}
		else {
			echo '<b>Caches</b> 
			| <a href="'.parametre_url($MY_SELF, 'TYPELISTE', 'squelettes').'">Squelettes</a>';
		};

		echo '<p><b>Types cache:</b> 
		<select name=TYPECACHE  onChange="form.submit()">
			<option value=ALL', $MYREQUEST['TYPECACHE'] == 'ALL' ? ' selected' : '', '>Tous</option>
			<option value=NON_SESSIONS', $MYREQUEST['TYPECACHE'] == 'NON_SESSIONS' ? ' selected' : '', '>Non sessionnés</option>
			<option value=SESSIONS', $MYREQUEST['TYPECACHE'] == 'SESSIONS' ? ' selected' : '', '>Sessionnés</option>
			<option value=SESSIONS_AUTH', $MYREQUEST['TYPECACHE'] == 'SESSIONS_AUTH' ? ' selected' : '', '>Sessionnés identifiés</option>
			<option value=SESSIONS_NONAUTH', $MYREQUEST['TYPECACHE'] == 'SESSIONS_NONAUTH' ? ' selected' : '', '>Sessionnés non identifiés</option>
			<option value=SESSIONS_TALON', $MYREQUEST['TYPECACHE'] == 'SESSIONS_TALON' ? ' selected' : '', '>Talons de session</option>
			<option value=FORMULAIRES', $MYREQUEST['TYPECACHE'] == 'FORMULAIRES' ? ' selected' : '', '>Formulaires</option>
		</select>
		<select name=COUNT onChange="form.submit()">
			<option value=10 ', $MYREQUEST['COUNT'] == '10' ? ' selected' : '', '>Top 10</option>
			<option value=20 ', $MYREQUEST['COUNT'] == '20' ? ' selected' : '', '>Top 20</option>
			<option value=50 ', $MYREQUEST['COUNT'] == '50' ? ' selected' : '', '>Top 50</option>
			<option value=100', $MYREQUEST['COUNT'] == '100' ? ' selected' : '', '>Top 100</option>
			<option value=150', $MYREQUEST['COUNT'] == '150' ? ' selected' : '', '>Top 150</option>
			<option value=250', $MYREQUEST['COUNT'] == '250' ? ' selected' : '', '>Top 250</option>
			<option value=500', $MYREQUEST['COUNT'] == '500' ? ' selected' : '', '>Top 500</option>
			<option value=0  ', $MYREQUEST['COUNT'] == '0' ? ' selected' : '', '>All</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<span title="REGEXP">Chercher:</span> <input name=SEARCH value="', $MYREQUEST['SEARCH'], '" type=text size=25/>
		<b>Dans:</b>
		<select name=WHERE onChange="form.submit()">
			<option value="" ', $MYREQUEST['WHERE'] == '' ? ' selected' : '', '>Noms des caches</option>
			<option value="ALL" ', $MYREQUEST['WHERE'] == 'ALL' ? ' selected' : '', '>Tout le contenu</option>
			<option value="HTML" ', $MYREQUEST['WHERE'] == 'HTML' ? ' selected' : '', '>HTML</option>
			<option value="META" ', $MYREQUEST['WHERE'] == 'META' ? ' selected' : '', '>Métadonnées</option>
			<option value="CONTEXTE" ', $MYREQUEST['WHERE'] == 'CONTEXTE' ? ' selected' : '', '>Contexte</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<input type=submit value="GO!">
		</p></form></div>
		';
		
		if (isset($MYREQUEST['SEARCH'])) {
			// Don't use preg_quote because we want the user to be able to specify a
			// regular expression subpattern.
			// Detection of a potential preg error :
			if (@preg_match('/' . antislash($MYREQUEST['SEARCH']) . '/i', null) === false) {
				echo '<div class="error">Error: search expression is not a valid regexp (it needs escaping parentheses etc)</div>';
				$MYREQUEST['SEARCH'] = preg_quote($MYREQUEST['SEARCH'],'/');
				echo "<div class='warning'>
						Warning : search expression has been preg_quoted :
							<xmp>{$MYREQUEST['SEARCH']}</xmp>
					</div>";
			}
			$MYREQUEST['SEARCH'] = '~'.$MYREQUEST['SEARCH'].'~i';
		}
		echo '<div class="info">
				<table cellspacing=0>
					<tbody><tr>';
		if ($MYREQUEST['TYPELISTE']=='squelettes')
			echo '<th align="left">', sortheader('S', 'Squelettes').'</th>';
		else {
			echo '<th align="left">Caches - ', sortheader('S', 'tri par Squelette').'</th>',
				'<th>', sortheader('H', 'Hits'), '</th>', 
				'<th>', sortheader('Z', 'Size'), '</th>', 
				'<th>', sortheader('A', 'Last accessed'), '</th>', 
				'<th>', sortheader('C', 'Created at'), '</th>',
				'<th>', sortheader('T', 'Timeout'), '</th>',
				'<th>Del</th>
				</tr>';
		};
		
		// FIXME : il vaudrait mieux trier aprés avoir filtré

		// builds list with alpha numeric sortable keys
		//
		$list = array();

		foreach ($cache['cache_list'] as $i => $entry) {
			switch ($MYREQUEST['S_KEY']) {
				case 'A':
					$k = sprintf('%015d-', $entry['access_time']);
					break;
				case 'H':
					$k = sprintf('%015d-', $entry['num_hits']);
					break;
				case 'Z':
					$k = sprintf('%015d-', $entry['mem_size']);
					break;
				case 'C':
					$k = sprintf('%015d-', $entry['creation_time']);
					break;
				case 'T':
					$k = sprintf('%015d-', $entry['ttl']);
					break;
				case 'S': 
					// tri par squelette : on supprime le préfixe et le md5 au début
					// et alors on peut trier
					$k = cache_get_squelette($entry['info']);
					break;
			}
			$list[$k . $entry['info']] = $entry;
		}

		if ($list) {
			// sort list
			//
			switch ($MYREQUEST['SORT']) {
				case "A":
					ksort($list);
					break;
				case "D":
					krsort($list);
					break;
				default:
					echo "...ah ben non pas de tri.";
					break;
			}
			
			$TYPECACHE = (isset($MYREQUEST['TYPECACHE']) ? $MYREQUEST['TYPECACHE'] : 'ALL');
			$also_required='';
			$also_required_bool = true;
			switch ($TYPECACHE) {
				case 'ALL':
					$pattern_typecache = '';
					break;
				case 'NON_SESSIONS':
					$pattern_typecache = XRAY_PATTERN_NON_SESSION;
					$also_required = 'cache_est_talon';
					$also_required_bool = false;
					break;
				case 'SESSIONS':
					$pattern_typecache = XRAY_PATTERN_SESSION;
					break;
				case 'SESSIONS_AUTH':
					$pattern_typecache = XRAY_PATTERN_SESSION_AUTH;
					break;
				case 'SESSIONS_NONAUTH':
					$pattern_typecache = XRAY_PATTERN_SESSION_ANON;
					break;
				case 'SESSIONS_TALON':
					$pattern_typecache = XRAY_PATTERN_NON_SESSION;
					$also_required = 'cache_est_talon';
					break;
				case 'FORMULAIRES':
					$pattern_typecache = '~formulaires/~i';
					break;
			}

			$liste_squelettes = array();

			// output list
			$i = 0;
			foreach ($list as $k => $entry) {
				$data=$searched=null;
				$data_success = false;
				// désormais on cherche toujours data
				$searched = $data = get_apc_data($entry['info'], $data_success);

				if ($MYREQUEST['SEARCH'] and $MYREQUEST['WHERE']) {
					switch ($MYREQUEST['WHERE']) {
					case 'ALL' :
						break;
					case 'HTML' :
						if (is_array($searched)) // !textwheel {
							$searched = $data['texte'];
						break;
					case 'META' :
						if (is_array($searched)) // !textwheel
							unset($searched['texte']);
						break;
					case 'CONTEXTE' :
						if (is_array($searched) 
							and isset($searched['contexte'])) // !textwheel
							$searched = $searched['contexte'];
						break;
					default :
						die("Mauvaise valeur pour where : " . $MYREQUEST['WHERE']);
					}
				};

				if ((!$pattern_typecache or preg_match($pattern_typecache, $entry['info']))
					and (!$MYREQUEST['SEARCH']
						or (!$MYREQUEST['WHERE']
							and preg_match($MYREQUEST['SEARCH'], $entry['info']))
						or ($MYREQUEST['WHERE']
							and preg_match($MYREQUEST['SEARCH'].'m', print_r($searched,1))))
					and (!$also_required 
						or ($also_required($entry['info'], $data)== $also_required_bool))
					) {

					if ($MYREQUEST['TYPELISTE']=='squelettes') {

						$joli = array();
						if (!is_array($data)) {	// textwheel etc
							continue;
						}
						elseif (($MYREQUEST['TYPECACHE'] == 'SESSIONS_TALON')
								or !isset($data['source'])) { // talons
							// ya pas de 'source' dans les talons, c'est la clé qui donne le squelette
							$s = cache_get_squelette($entry['info']);
							$squelette = find_in_path($s.'.html');

							// Les dossiers de squelettes déclarés comme public dans paquet.xml
							// ne sont pas utilisés par find_in_path dans le privé
							if (!$squelette)
								$squelette = $joli = $s." (échec find_in_path : déclarez le chemin (depuis la racine du site) de vos dossiers publics de squelettes dans GLOBALS['dossier_squelette']) ";
							else
								$squelette = $joli['source'] = substr($squelette, 3);	// On enlève ../ 
						}
						else {	// cas normal : vrai cache d'un squelette spip
							$squelette = $joli['source'] = $data['source'];
						}

						if (in_array($squelette, $liste_squelettes)) {	// déjà listé
							continue;
						}

						// squelette pas encore listé
						$i++;
						$liste_squelettes[] = $squelette;
						echo "<tr class='tr-".($i % 2)."'><td colspan='7'>$i) ".joli_cache($joli)."</td></tr>";
						if ($i == $MYREQUEST['COUNT'])
							break;
						continue;
					}
					$i++;
					$sh = md5($entry["info"]);
					
					$displayed_name = htmlentities(strip_tags($entry['info'], ''), ENT_QUOTES, 'UTF-8');
					if (defined('XRAY_NEPASAFFICHER_DEBUTNOMCACHE'))
						$displayed_name = str_replace(XRAY_NEPASAFFICHER_DEBUTNOMCACHE, '', $displayed_name);
					echo '<tr id="key-' . $sh . '" class=tr-', $i % 2, '>', 
							"<td class='td-0' style='position: relative'>
								$i) 
								<a href='$MY_SELF&SH={$sh}#key-{$sh}'>
									$displayed_name
								</a>";

					if ($data and cache_est_talon($entry['info'], $data))
						echo "<span style='margin-left:2em' title='Talon des caches sessionnés avec ce squelette et le même contexte'>[talon]</span>";
					
					$boutons_liens = '';
					if ($p = preg_match(XRAY_PATTERN_SESSION_AUTH, $displayed_name, $match) 
						and $MYREQUEST['SEARCH'] != "/{$match[1]}/i") {
						$url_session = parametre_url($MY_SELF, 'SEARCH', $match[1]);
						$boutons_liens .= bouton_session($match[1], $url_session);
					}
					if (is_array($data)
						and isset($data['invalideurs']['session'])) {
						$p = preg_match(XRAY_PATTERN_TALON, $displayed_name, $match);
						$url_mm_talon = '';
						$bouton_mm_talon='[mm talon]';
						if ($p and $match[1] and ($MYREQUEST['SEARCH']!=$match[1])) {
							$url_mm_talon = parametre_url($MY_SELF, 'TYPECACHE', 'ALL');
							$url_mm_talon = parametre_url($url_mm_talon, 'SEARCH', $match[1]);
						}
						else 
							$bouton_mm_talon='(! Err get talon !)';
						$boutons_liens .= "<a href='$url_mm_talon' title='Caches du même squelette et avec le même contexte'>$bouton_mm_talon</a>";
					}
					echo '<span style="float: right">'.$boutons_liens.'</span>';

					if ($MYREQUEST['EXTRA'] and ($sh != $MYREQUEST["SH"]) // sinon yaura un zoom après et c'est inutile de répéter ici
						and $data_success) {
						$extra = null;
						$jolif='joli_cache';
						if (is_array($data)) {
							switch ($MYREQUEST['EXTRA']) {
							case 'CONTEXTE':
								$jolif='joli_contexte';
								if (isset($data['contexte']))
									$extra = $data['contexte'];
								else
									$extra = '(non défini)';
								break;

							case 'CONTEXTES_SPECIAUX':
								if (isset($data['contexte'])) {
									$jolif='joli_contexte';
									$extra = $data['contexte'];
									foreach (array(
										'lang',
										'date',
										'date_default',
										'date_redac',
										'date_redac_default'
									) as $ki)
										unset($extra[$ki]);
								} else
									$extra = '(non défini)';
								break;

							case 'HTML_COURT' :
								$extra = ajuste_longueur_html($data['texte']);
								break;

							case 'INFO_AUTEUR':
								$jolif='joli_contexte';
								if (isset($data['contexte'])) {
									foreach (array(
										'id_auteur',
										'email',
										'nom',
										'statut',
										'login'
									) as $ki)
										if (isset($data['contexte'][$ki]))
											$extra[$ki] = $extra[$ki] = $data['contexte'][$ki];
								};
								break;

							case 'INFO_OBJET_SPECIAL':
								$jolif='joli_contexte';
								if (isset($data['contexte'])) {
									$ki = 'id_'.XRAY_OBJET_SPECIAL;
									if (isset($data['contexte'][$ki]))
										$extra[$ki] = $extra[$ki] = $data['contexte'][$ki];
								};
								break;
							case 'INVALIDEURS':
								if (isset ($data['invalideurs']))
									$extra = $data['invalideurs'];
								break;
							case 'INVALIDEURS_SPECIAUX': 
								if (isset ($data['invalideurs'])) {
									$extra = $data['invalideurs'];
									foreach (array(
										'cache',
										'session'
									) as $ki)
										unset($extra[$ki]);
								}
								break;
							case 'INCLUSIONS' :
								if (!isset ($data['texte']))
									$extra = '(html non défini)';
								elseif (preg_match_all("/<\?php\s+echo\s+recuperer_fond\s*\(\s*'([a-z0-9_\-\.\/]+)'/", $data['texte'], $matches))
									$extra = $matches[1];
								else
									$extra = '(aucune <INCLUSION>)';
								break;
							case 'MACROSESSIONS' :
								if (!isset ($data['texte']))
									$extra = '(html non défini)';
								elseif (preg_match_all("/\bpipelined_session_get\s*\((['\"a-z0-9\s_\-\.\/,]+)\)/", $data['texte'], $matches))
									$extra = $matches[1];
								else
									$extra = '(aucune balise #_SESSION ou #_SESSION_SI)';
								break;
							case 'MACROAUTORISER' :
								if (!isset ($data['texte']))
									$extra = '(html non défini)';
								elseif (preg_match_all("/if\s+\(autoriser\s*\((.+)\)\s*\)\s*{\s*\?>/", $data['texte'], $matches))
									$extra = $matches[1];
									// $extra = $matches; 
								else
									$extra = '(aucune balise #_AUTORISER_SI)';
								break;
							}
						}
						$extra = $jolif($extra); 

						if ($extra)
							echo "<br>".$extra."<br>";
						else
							echo "<br>(rien)</br>";
					} // fin affichage Extra

					echo '</td>
					<td class="td-n center">', $entry['num_hits'], '</td>
					<td class="td-n right">', $entry['mem_size'], '</td>
					<td class="td-n center">', date(DATE_FORMAT, $entry['access_time']), '</td>
					<td class="td-n center">', date(DATE_FORMAT, $entry['creation_time']), '</td>';
					
					if ($entry['ttl'])
						echo '<td class="td-n center">' . $entry['ttl'] . ' seconds</td>';
					else
						echo '<td class="td-n center">None</td>';

					if ($entry['deletion_time']) {
						echo '<td class="td-last center">', date(DATE_FORMAT, $entry['deletion_time']), '</td>';
					} else if ($MYREQUEST['OB'] == OB_USER_CACHE) {
						
						echo '<td class="td-last center">';
						echo '<a href="', $MY_SELF, '&DU=', urlencode($entry['info']), '" style="color:red">X</a>';
						echo '</td>';
					} else {
						echo '<td class="td-last center"> &nbsp; </td>';
					}
					echo '</tr>';
					if ($sh == $MYREQUEST["SH"]) { // Le ZOOM sur une entrée
						echo '<tr>';
						echo '<td colspan="7">';
						
						if (!isset($_GET['ZOOM']) or ($_GET['ZOOM'] != 'TEXTELONG')) {
							$url      = parametre_url($self_pour_lien, 'ZOOM', 'TEXTELONG') . "#key-$sh";
							$menuzoom = "<a href='$url' class='menuzoom'>Voir tout le texte</a> ";
							if (is_array($data) and isset($data['texte']))
								$data['texte'] = ajuste_longueur_html($data['texte']);
						} else {
							$url      = parametre_url($self_pour_lien, 'ZOOM', 'TEXTECOURT') . "#key-$sh";
							$menuzoom = "<a href='$url' class='menuzoom'>Voir texte abbrégé</a> ";
						}
						$url = parametre_url($self_pour_lien, 'SH', '') . "#key-$sh";
						$menuzoom .= "<a href='$url' class='menuzoom'>Replier</a>";
						
						if ($data_success) {
							echo "<p>$menuzoom</p>";
							echo joli_cache($data);
						} else {
							if (!apcu_exists($entry['info']))
								echo '(! doesnt exist anymore !)';
							else
								echo '(! fetch failed !)';
						}
						echo '</td>';
						echo '</tr>';
					} // fin du zoom SH
					if ($i == $MYREQUEST['COUNT'])
						break;
				} // fin du filtrage
			} // fin du foreach
			
		} else { // En l'absence de tout cache
			echo '<tr class=tr-0><td class="center" colspan=', $cols, '><i>No data</i></td></tr>';
		}
		echo <<< EOB
		</tbody></table>
EOB;
		
		if ($list && $i < count($list)) {
			echo "<a href=\"$MY_SELF", "&COUNT=0\"><i>", count($list) - $i, ' more available...</i></a>';
		}
		
		echo <<< EOB
		</div>
EOB;
		break;
	
	// -----------------------------------------------
	// Version check
	// -----------------------------------------------
	case OB_VERSION_CHECK:
		echo <<<EOB
		<div class="info"><h2>APCu Version Information</h2>
		<table cellspacing=0><tbody>
		<tr>
		<th></th>
		</tr>
EOB;
		if (defined('PROXY')) {
			$ctxt = stream_context_create(array(
				'http' => array(
					'proxy' => PROXY,
					'request_fulluri' => True
				)
			));
			$rss  = @file_get_contents("http://pecl.php.net/feeds/pkg_apcu.rss", False, $ctxt);
		} else {
			$rss = @file_get_contents("http://pecl.php.net/feeds/pkg_apcu.rss");
		}
		if (!$rss) {
			echo '<tr class="td-last center"><td>Unable to fetch version information.</td></tr>';
		} else {
			$apcversion = phpversion('apcu');
			
			preg_match('!<title>APCu ([0-9.]+)</title>!', $rss, $match);
			echo '<tr class="tr-0 center"><td>';
			if (version_compare($apcversion, $match[1], '>=')) {
				echo '<div class="ok">You are running the latest version of APCu (' . $apcversion . ')</div>';
				$i = 3;
			} else {
				echo '<div class="failed">You are running an older version of APCu (' . $apcversion . '),
				newer version ' . $match[1] . ' is available at <a href="http://pecl.php.net/package/APCu/' . $match[1] . '">
				http://pecl.php.net/package/APCu/' . $match[1] . '</a>
				</div>';
				$i = -1;
			}
			echo '</td></tr>';
			echo '<tr class="tr-0"><td><h3>Change Log:</h3><br/>';
			
			preg_match_all('!<(title|description)>([^<]+)</\\1>!', $rss, $match);
			$changelog = $match[2];
			
			for ($j = 2; $j + 1 < count($changelog); $j += 2) {
				$v = $changelog[$j];
				if ($i < 0 && version_compare($apcversion, $ver, '>=')) {
					break;
				} else if (!$i--) {
					break;
				}
				list($unused, $ver) = $v;
				$changes = $changelog[$j + 1];
				echo "<b><a href=\"http://pecl.php.net/package/APCu/$ver\">" . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . "</a></b><br><blockquote>";
				echo nl2br(htmlspecialchars($changes, ENT_QUOTES, 'UTF-8')) . "</blockquote>";
			}
			echo '</td></tr>';
		}
		echo <<< EOB
		</tbody></table>
		</div>
EOB;
		break;

	case OB_CACHELAB :
		include_spip('inc/cachelab');
		include('cachelab_diag.php');
		break;
}
?>

	</div>
<!--
Based on APCGUI By R.Becker\n$VERSION
-->
</body>
</html>

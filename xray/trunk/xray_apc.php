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
| Authors: Ralf Becker <beckerr@php.net>                               |
|          Rasmus Lerdorf <rasmus@php.net>                             |
|          Ilia Alshanetsky <ilia@prohost.org>                         |
|          JLuc http://contrib.spip.net/JLuc                           |
+----------------------------------------------------------------------+

All other licensing and usage conditions are those of the PHP Group.

*/

if (!autoriser('webmestre'))
	die("Autorisation non accordée : devenez webmestre d'abord.");

$VERSION = '$Id$';

////////// READ OPTIONAL CONFIGURATION FILE ////////////
if (file_exists("apc.conf.php"))
	include("apc.conf.php");
////////////////////////////////////////////////////////

////////// BEGIN OF DEFAULT CONFIG AREA ///////////////////////////////////////////////////////////

defaults('USE_AUTHENTICATION', 0); // Use (internal) authentication - best choice if
// no other authentication is available
// If set to 0:
//  There will be no further authentication. You
//  will have to handle this by yourself!
// If set to 1:
//  You need to change ADMIN_PASSWORD to make
//  this work!
defaults('ADMIN_USERNAME', 'admin'); // Admin Username
defaults('ADMIN_PASSWORD', 'password'); // Admin Password - CHANGE THIS TO ENABLE!!!

// (beckerr) I'm using a clear text password here, because I've no good idea how to let
//           users generate a md5 or crypt password in a easy way to fill it in above

// defaults('DATE_FORMAT', "d.m.Y H:i:s");	// German
defaults('DATE_FORMAT', 'Y/m/d H:i:s'); // US

defaults('GRAPH_SIZE', 400); // Image size

//defaults('PROXY', 'tcp://127.0.0.1:8080');

// _CACHE_NAMESPACE est défini par memoization et préfixe chaque nom de cache SPIP
// On ne souhaite pas que cette partie du nom s'affiche sur chaque ligne
define('XRAY_NEPASAFFICHER_DEBUTNOMCACHE', _CACHE_NAMESPACE);

////////// END OF DEFAULT CONFIG AREA /////////////////////////////////////////////////////////////

// Strings utils

function is_serialized($str)
{
	return ($str == serialize(false) || @unserialize($str) !== false);
}
function get_serial_class($serial)
{
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

function get_apc_data($info, &$success) {
	if (apcu_exists($info)
		and ($data = apcu_fetch($info, $success)) 
		and $success 
		and is_array($data) and (count($data) == 1) 
		and is_serialized($data[0]))
		return unserialize($data[0]);
	$success = false;
	return null;
}

function spipsafe_unserialize($str)
{
	if (strpos($str, "SPIPTextWheelRuleset") !== false) {
		if (isset($_GET['ZOOM']) and ($_GET['ZOOM'] == 'TEXTECOURT'))
			return "Début : " . substr(trim($str), 0, 80) . '...';
		else
			return "Brut : $str";
	}
	$unser = unserialize($str);
	if (is_array($unser) and isset($unser['texte']) and isset($_GET['ZOOM']) and ($_GET['ZOOM'] == 'TEXTECOURT')) {
		$unser['texte'] = trim(preg_replace('/\s+/', ' ', $unser['texte']));
		if (mb_strlen($unser['texte']) > 80)
			$unser['texte'] = mb_substr($unser['texte'], 0, 80) . '...';
		elseif (!$unser['texte'])
			$unser['texte'] = '(vide)';
	}
	return print_contexte($unser, 1);
}

function print_contexte($extra, $tostring)
{
	$print = print_r($extra, 1);
	if (stripos($print, 'Array') === 0) {
		// On enlève 'Array( ' au début et ')' à la fin
		$print = trim(substr($print, 5), " (\n\r\t");
		$print = substr ($print, 0, -1);
		$print = preg_replace_callback("/\[id_([a-z\-_]+)\]\s*=>\s*(\d+)$/im", function($match) use ($extra)
			{
				return $match[0] . '</xmp>' . bouton_objet($match[1], $match[2], $extra) . '<xmp>';
			}, $print);
		// [squelette] => html_5731a2e40776724746309c16569cac40
		$print = preg_replace_callback("/\[(squelette|source)\]\s*=>\s*(html_[a-f0-9]+|[a-z0-9_\.\/\-]+\.html)$/im", function($match)
			{
				if (!defined('_SPIP_ECRIRE_SCRIPT'))
					spip_initialisation_suite();
				switch ($match[1]) {
				case 'squelette' : // cache squelette intermédiaire, en php
					$source = trim(_DIR_CACHE, '/').'/skel/'.$match[2].'.php';
					$title = "Squelette compilé : cache intermédiaire en php";
					break;
				case 'source' :
					$source = $match[2];
					$title = "Source du squelette SPIP, avec boucles, balises etc";
					break;
				}
				return "[{$match[1]}] => </xmp><a title='{$title}' 
							href='".generer_url_ecrire('xray', "SOURCE=../$source")."' 
							target='blank'><xmp>{$match[2]}</xmp></a><xmp>";
			}, $print);
	}
	$print=preg_replace('/^    /m', '', $print);
	if ($tostring)
		return $print;
	echo $print;
}

function bouton_objet($objet, $id_objet, $extra)
{
	$objet_visible = $objet;
	if ($objet == 'secteur')
		$objet = 'rubrique';
	elseif (($objet == 'objet')	and isset ($extra['objet']))
	{
		$objet_visible = $objet = $extra['objet'];
	};
	return "<a href='/ecrire/?exec=$objet&id_$objet=$id_objet' target='blank' 
			style='position:absolute; right:1em'
			title=\"" . attribut_html(generer_info_entite($id_objet, $objet, 'titre', 'etoile')) . "\">[voir $objet_visible]</a>";
}

if (!function_exists('plugin_est_actif')) {
	function plugin_est_actif($prefixe) {
		$f = chercher_filtre('info_plugin');
		return $f($prefixe, 'est_actif');
	}
}

////////////////////////////////////////////////////////////////////////

// "define if not defined"
function defaults($d, $v)
{
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

// check validity of input variables
$vardom = array(
	'exec' => '/^[a-zA-Z_\-0-9]+$/', // pour #URL_ECRIRE{xray}
	'OB' => '/^\d+$/', // operational mode switch
	'CC' => '/^[01]$/', // clear cache requested
	'DU' => '/^.*$/', // Delete User Key
	'SH' => '/^[a-z0-9]*$/', // shared object description
	
	'IMG' => '/^[123]$/', // image to generate
	'SOURCE' => '/^[a-z0-9\-_\/\.]+$/', // file source to display
	'LO' => '/^1$/', // login requested
	
	'COUNT' => '/^\d+$/', // number of line displayed in list
	'SCOPE' => '/^[AD]$/', // list view scope
	'S_KEY' => '/^[AHSMCDTZ]$/', // first sort key
	'SORT' => '/^[DA]$/', // second sort key
	'AGGR' => '/^\d+$/', // aggregation by dir level
	'SEARCH' => '~.*~',
	'TYPECACHE' => '/^(|ALL|SESSIONS|SESSIONS_AUTH|SESSIONS_NONAUTH|FORMULAIRES)$/', //
	'ZOOM' => '/^(|TEXTECOURT|TEXTELONG)$/', //
	'WHERE' => '/^(|ALL|HTML|META)$/', // recherche dans le contenu
	'EXTRA' => '/^(|CONTEXTE|CONTEXTES_SPECIAUX|INFO_AUTEUR|INVALIDEURS|INVALIDEURS_SPECIAUX|INCLUSIONS'
		.(plugin_est_actif('macrosession') ? '|MACROSESSIONS' : '')
		.')$/'		// Affichage pour chaque élément de la liste
);

// cache scope
$scope_list = array(
	'A' => 'cache_list',
	'D' => 'deleted_list'
);

global $MYREQUEST; // fix apcu
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

// check parameter sematics
if (empty($MYREQUEST['SCOPE']))
	$MYREQUEST['SCOPE'] = "A";
if (empty($MYREQUEST['S_KEY']))
	$MYREQUEST['S_KEY'] = "H";
if (empty($MYREQUEST['SORT']))
	$MYREQUEST['SORT'] = "D";
if (empty($MYREQUEST['OB']))
	$MYREQUEST['OB'] = OB_HOST_STATS;
if (!isset($MYREQUEST['COUNT']))
	$MYREQUEST['COUNT'] = 20;
if (!isset($scope_list[$MYREQUEST['SCOPE']]))
	$MYREQUEST['SCOPE'] = 'A';

global $MY_SELF; // fix apcu
global $MY_SELF_WO_SORT; // fix apcu
$MY_SELF_WO_SORT = "$PHP_SELF" . "?SCOPE=" . $MYREQUEST['SCOPE'] . "&COUNT=" . $MYREQUEST['COUNT'] . "&SEARCH=" . $MYREQUEST['SEARCH'] . "&TYPECACHE=" . $MYREQUEST['TYPECACHE'] . "&ZOOM=" . $MYREQUEST['ZOOM'] . "&EXTRA=" . $MYREQUEST['EXTRA'] . "&WHERE=" . $MYREQUEST['WHERE'] . "&exec=" . $MYREQUEST['exec'] . "&OB=" . $MYREQUEST['OB'];
$MY_SELF         = $MY_SELF_WO_SORT . "&S_KEY=" . $MYREQUEST['S_KEY'] . "&SORT=" . $MYREQUEST['SORT'];
$self            = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

global $IMG_BASE;
$IMG_BASE = "$PHP_SELF" . "?exec=" . $MYREQUEST['exec'];

// authentication needed?
//
if (!USE_AUTHENTICATION) {
	$AUTHENTICATED = 1;
} else {
	$AUTHENTICATED = 0;
	if (ADMIN_PASSWORD != 'password' && ($MYREQUEST['LO'] == 1 || isset($_SERVER['PHP_AUTH_USER']))) {
		
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME || $_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
			Header("WWW-Authenticate: Basic realm=\"APC Login\"");
			Header("HTTP/1.0 401 Unauthorized");
			
			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big><br/>&nbsp;<br/>&nbsp;
				<big><a href='$PHP_SELF?OB={$MYREQUEST['OB']}'>Continue...</a></big>
				</body></html>
EOB;
			exit;
			
		} else {
			$AUTHENTICATED = 1;
		}
	}
}

// clear cache
if ($AUTHENTICATED && isset($MYREQUEST['CC']) && $MYREQUEST['CC']) {
	apcu_clear_cache();
}

if ($AUTHENTICATED && !empty($MYREQUEST['DU'])) {
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
	echo "<xmp>".file_get_contents ($MYREQUEST['SOURCE'])."</xmp>";
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
	global $MY_SELF_WO_SORT; // fix apcu : il faut global ici aussi
	
	// fix apcu l'affichage des headers ne doit pas changer $MYREQUEST
	$SORT = $MYREQUEST['SORT'];
	if (!$SORT)
		$SORT = 'D';
	if ($MYREQUEST['S_KEY'] == $key)
		$SORT = (($SORT == 'A') ? 'D' : 'A');
	$url = "$MY_SELF_WO_SORT$extra&S_KEY=$key&SORT=$SORT";
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

function put_login_link($s = "Login")
{
	global $MY_SELF, $MYREQUEST, $AUTHENTICATED;
	// needs ADMIN_PASSWORD to be changed!
	//
	if (!USE_AUTHENTICATION) {
		return;
	} else if (ADMIN_PASSWORD == 'password') {
		print <<<EOB
			<a href="#" onClick="javascript:alert('You need to set a password at the top of apc.php before this will work!');return false";>$s</a>
EOB;
	} else if ($AUTHENTICATED) {
		print <<<EOB
			'{$_SERVER['PHP_AUTH_USER']}'&nbsp;logged&nbsp;in!
EOB;
	} else {
		print <<<EOB
			<a href="$MY_SELF&LO=1">$s</a>
EOB;
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
<head><title>APCu INFO <?php
echo $host;
?></title>
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
div.content { padding:1em 1em 1em 1em; position:absolute; width:97%; z-index:100; }


div.head div.login {
	position:absolute;
	right: 1em;
	top: 1.2em;
	color:white;
	width:6em;
	}
div.head div.login a {
	position:absolute;
	right: 0em;
	background:rgb(119,123,180);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	}
div.head div.login a:hover {
	background:rgb(193,193,244);
	}

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
	color:white;
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
.aright { position:absolute;right:1em }
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
	</h1>
	<div class="login">
	<?php
put_login_link();
?>
	</div>
	<hr class="apc">
</div>

<?php
// Display main Menu
echo <<<EOB
	<ol class=menu>
	<li><a href="$MY_SELF&SH={$MYREQUEST['SH']}">Refresh Data</a></li>
EOB;
echo menu_entry(OB_HOST_STATS, 'View Host Stats'), menu_entry(OB_USER_CACHE, 'User Cache Entries'), menu_entry(OB_VERSION_CHECK, 'Version Check');

if ($AUTHENTICATED) {
	echo <<<EOB
		<li><a class="aright" href="$MY_SELF&CC=1" onClick="javascript:return confirm('Are you sure?');">Clear Cache</a></li>
EOB;
}
echo <<<EOB
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
		echo <<< EOB
		<div class="info div1"><h2>Mémoization SPIP</h2>
		<table cellspacing=0><tbody>
		<tr class=tr-0><td class=td-0>_CACHE_NAMESPACE</td><td>$_namespace</td></tr>
		</table></div>
EOB;
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
		if (!$AUTHENTICATED) {
			echo '<div class="error">You need to login to see the user values here!<br/>&nbsp;<br/>';
			put_login_link("Login now!");
			echo '</div>';
			break;
		}
		$fieldname    = 'info';
		$fieldheading = 'User Entry Label';
		$fieldkey     = 'info';
		
		$cols = 6;
		echo <<<EOB
		<div class=sorting><form><p>Scope: 
		<input type=hidden name=OB value={$MYREQUEST['OB']}>
		<input type=hidden name=exec value={$MYREQUEST['exec']}>
		<select name=SCOPE  onChange="form.submit()">
EOB;
		echo '<option value=A', $MYREQUEST['SCOPE'] == 'A' ? ' selected' : '', '>Active</option>
			<option value=D', $MYREQUEST['SCOPE'] == 'D' ? ' selected' : '', '>Deleted</option>
		</select>
		 Sorting:
		<select name=S_KEY  onChange="form.submit()">
			<option value=H', $MYREQUEST['S_KEY'] == 'H' ? ' selected' : '', '>Hits</option>
			<option value=Z', $MYREQUEST['S_KEY'] == 'Z' ? ' selected' : '', '>Size</option>
			<option value=S', $MYREQUEST['S_KEY'] == 'S' ? ' selected' : '', '>$fieldheading</option>
			<option value=A', $MYREQUEST['S_KEY'] == 'A' ? ' selected' : '', '>Last accessed</option>
			<option value=M', $MYREQUEST['S_KEY'] == 'M' ? ' selected' : '', '>Last modified</option>
			<option value=C', $MYREQUEST['S_KEY'] == 'C' ? ' selected' : '', '>Created at</option>
			<option value=D', $MYREQUEST['S_KEY'] == 'D' ? ' selected' : '', '>Deleted at</option>';
		if ($fieldname == 'info')
			echo '<option value=D', $MYREQUEST['S_KEY'] == 'T' ? ' selected' : '', '>Timeout</option>';
		
		echo '</select>
		<select name=SORT  onChange="form.submit()">', 
			'<option value=D', $MYREQUEST['SORT'] == 'D' ? ' selected' : '', '>DESC</option>', 
			'<option value=A', $MYREQUEST['SORT'] == 'A' ? ' selected' : '', '>ASC</option>', 
		'</select>
		&nbsp;&nbsp;<b>HTML:</b>
		<select name=ZOOM  onChange="form.submit()">
			<option value=TEXTECOURT', $MYREQUEST['ZOOM'] == 'TEXTECOURT' ? ' selected' : '', '>Courts</option>
			<option value=TEXTELONG', $MYREQUEST['ZOOM'] == 'TEXTELONG' ? ' selected' : '', '>Entiers</option> 
		</select>
		&nbsp;&nbsp;<b>Affichage extra:</b> 
		<select name=EXTRA  onChange="form.submit()">
			<option value="" ', $MYREQUEST['EXTRA'] == '' ? ' selected' : '', '></option> 
			<option value=CONTEXTE ', $MYREQUEST['EXTRA'] == 'CONTEXTE' ? ' selected' : '', '>Contexte</option>
			<option value=CONTEXTES_SPECIAUX ', $MYREQUEST['EXTRA'] == 'CONTEXTES_SPECIAUX' ? ' selected' : '', '>Contextes spécifiques</option>
			<option value=INFO_AUTEUR ', $MYREQUEST['EXTRA'] == 'INFO_AUTEUR' ? ' selected' : '', '>Infos auteur</option>
			<option value=INVALIDEURS ', $MYREQUEST['EXTRA'] == 'INVALIDEURS' ? ' selected' : '', '>Invalideurs</option>
			<option value=INVALIDEURS_SPECIAUX ', $MYREQUEST['EXTRA'] == 'INVALIDEURS_SPECIAUX' ? ' selected' : '', '>Invalideurs spécifiques</option>
			<option value=INCLUSIONS ', $MYREQUEST['EXTRA'] == 'INCLUSIONS' ? ' selected' : '', '>&lt;INCLURE&gt;</option>
			<option value=MACROSESSIONS ', $MYREQUEST['EXTRA'] == 'MACROSESSIONS' ? ' selected' : '', '>#_SESSION</option>
		</select>
		<p><b>Types cache:</b> 
		<select name=TYPECACHE  onChange="form.submit()">
			<option value=ALL', $MYREQUEST['TYPECACHE'] == 'ALL' ? ' selected' : '', '>Tous</option>
			<option value=SESSIONS', $MYREQUEST['TYPECACHE'] == 'SESSIONS' ? ' selected' : '', '>Sessionnés</option>
			<option value=SESSIONS_AUTH', $MYREQUEST['TYPECACHE'] == 'SESSIONS_AUTH' ? ' selected' : '', '>Sessionnés identifiés</option>
			<option value=SESSIONS_NONAUTH', $MYREQUEST['TYPECACHE'] == 'SESSIONS_NONAUTH' ? ' selected' : '', '>Sessionnés non identifiés</option>
			<option value=FORMULAIRES', $MYREQUEST['TYPECACHE'] == 'FORMULAIRES' ? ' selected' : '', '>Formulaires</option>
		</select>
		<select name=COUNT onChange="form.submit()">
			<option value=10 ', $MYREQUEST['COUNT'] == '10' ? ' selected' : '', '>Top 10</option>
			<option value=20 ', $MYREQUEST['COUNT'] == '20' ? ' selected' : '', '>Top 20</option>
			<option value=50 ', $MYREQUEST['COUNT'] == '50' ? ' selected' : '', '>Top 50</option>
			<option value=100', $MYREQUEST['COUNT'] == '100' ? ' selected' : '', '>Top 100</option>
			<option value=150', $MYREQUEST['COUNT'] == '150' ? ' selected' : '', '>Top 150</option>
			<option value=200', $MYREQUEST['COUNT'] == '200' ? ' selected' : '', '>Top 200</option>
			<option value=500', $MYREQUEST['COUNT'] == '500' ? ' selected' : '', '>Top 500</option>
			<option value=0  ', $MYREQUEST['COUNT'] == '0' ? ' selected' : '', '>All</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		Chercher: <input name=SEARCH value="', $MYREQUEST['SEARCH'], '" type=text size=25/>
		<b>Dans:</b>
		<select name=WHERE onChange="form.submit()">
			<option value="" ', $MYREQUEST['WHERE'] == '' ? ' selected' : '', '>Noms des caches</option>
			<option value="ALL" ', $MYREQUEST['WHERE'] == 'ALL' ? ' selected' : '', '>Tout le contenu</option>
			<option value="HTML" ', $MYREQUEST['WHERE'] == 'HTML' ? ' selected' : '', '>HTML</option>
			<option value="META" ', $MYREQUEST['WHERE'] == 'META' ? ' selected' : '', '>Métadonnées</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<input type=submit value="GO!">
		</p></form></div>';
		
		if (isset($MYREQUEST['SEARCH'])) {
			// Don't use preg_quote because we want the user to be able to specify a
			// regular expression subpattern.
			$MYREQUEST['SEARCH'] = '/' . str_replace('/', '\\/', $MYREQUEST['SEARCH']) . '/i';
			if (preg_match($MYREQUEST['SEARCH'], 'test') === false) {
				echo '<div class="error">Error: enter a valid regular expression as a search query.</div>';
				break;
			}
		}
		echo '<div class="info"><table cellspacing=0><tbody>', '<tr>', '<th>', sortheader('S', $fieldheading), '</th>', '<th>', sortheader('H', 'Hits'), '</th>', '<th>', sortheader('Z', 'Size'), '</th>', '<th>', sortheader('A', 'Last accessed'), '</th>', '<th>', sortheader('M', 'Last modified'), '</th>', '<th>', sortheader('C', 'Created at'), '</th>';
		
		if ($fieldname == 'info') {
			$cols += 2;
			echo '<th>', sortheader('T', 'Timeout'), '</th>';
		}
		echo '<th>', sortheader('D', 'Deleted at'), '</th></tr>';
		
		// builds list with alpha numeric sortable keys
		//
		$list = array();
		
		foreach ($cache[$scope_list[$MYREQUEST['SCOPE']]] as $i => $entry) {
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
				case 'M':
					$k = sprintf('%015d-', $entry['mtime']);
					break;
				case 'C':
					$k = sprintf('%015d-', $entry['creation_time']);
					break;
				case 'T':
					$k = sprintf('%015d-', $entry['ttl']);
					break;
				case 'D':
					$k = sprintf('%015d-', $entry['deletion_time']);
					break;
				case 'S':
					$k = $entry["info"];
					break;
			}
			if (!$AUTHENTICATED) {
				// hide all path entries if not logged in
				$list[$k . $entry[$fieldname]] = preg_replace('/^.*(\\/|\\\\)/', '*hidden*/', $entry);
			} else {
				$list[$k . $entry[$fieldname]] = $entry;
			}
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
			switch ($TYPECACHE) {
				case 'ALL':
					$pattern_typecache = '';
					break;
				case 'SESSIONS':
					$pattern_typecache = '/_([a-f0-9]{8}|)$/i';
					break;
				case 'SESSIONS_AUTH':
					$pattern_typecache = '/_[a-f0-9]{8}$/i';
					break;
				case 'SESSIONS_NONAUTH':
					$pattern_typecache = '/_$/i';
					break;
				case 'FORMULAIRES':
					$pattern_typecache = '~formulaires/~i';
					break;
			}
			
			// output list
			$i = 0;
			foreach ($list as $k => $entry) {
				$data=$searched=null;
				$success = false;
				$tried_get_apc_data = false;
				if ($MYREQUEST['SEARCH'] and $MYREQUEST['WHERE']) {
					$searched = $data = get_apc_data($entry['info'], $success);
					$tried_get_apc_data = true;
					switch ($MYREQUEST['WHERE']) {
					case 'ALL' :
						break;
					case 'HTML' :
						if (is_array($searched)) // !textwheel
							$searched = $data['texte'];
						break;
					case 'META' :
						if (is_array($searched)) // !textwheel
							unset($searched['texte']);
						break;
					default :
						die("Mauvaise valeur pour where : " . $MYREQUEST['WHERE']);
					}
				};

				if ((!$pattern_typecache or preg_match($pattern_typecache, $entry[$fieldname]))
					and (!$MYREQUEST['SEARCH']
						or (!$MYREQUEST['WHERE']
							and preg_match($MYREQUEST['SEARCH'], $entry[$fieldname]))
						or ($MYREQUEST['WHERE']
							and preg_match($MYREQUEST['SEARCH'].'m', print_r($searched,1))))) { 
					$sh = md5($entry["info"]);
					
					$field_value = htmlentities(strip_tags($entry[$fieldname], ''), ENT_QUOTES, 'UTF-8');
					if (defined('XRAY_NEPASAFFICHER_DEBUTNOMCACHE'))
						$field_value = str_replace(XRAY_NEPASAFFICHER_DEBUTNOMCACHE, '...', $field_value);
					echo '<tr id="key-' . $sh . '" class=tr-', $i % 2, '>', "<td class='td-0' style='position: relative'>
			<a href='$MY_SELF&SH={$sh}#key-{$sh}'>$field_value</a>";
					
					if ($p = preg_match('/_([0-9a-f]{8})$/i', $field_value, $match) 
						and $MYREQUEST['SEARCH'] != "/{$match[1]}/i") {
						$url_session = parametre_url($MY_SELF, 'SEARCH', $match[1]);
						echo "<a href='$url_session' style='float: right'>[session]</a>";
					}
					if ($MYREQUEST['EXTRA'] and ($sh != $MYREQUEST["SH"]) // sinon yaura un zoom après et c'est inutile de répéter ici
						and (($tried_get_apc_data and $success)
							or (!$tried_get_apc_data 
								and ($data = get_apc_data($entry['info'], $success))))) {
						$extra = null;
						$liens = '';
						if (is_array($data)) {
							switch ($MYREQUEST['EXTRA']) {
							case 'CONTEXTE':
								if (isset($data['contexte']))
									$extra = $data['contexte'];
								else
									$extra = '(non défini)';
								break;
							case 'CONTEXTES_SPECIAUX':
								if (isset($data['contexte'])) {
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
							case 'INFO_AUTEUR':
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
									$extra = '(aucune inclusion)';
								break;
							case 'MACROSESSIONS' :
								if (!isset ($data['texte']))
									$extra = '(html non défini)';
								elseif (preg_match_all("/pipelined_session_get\((['\"a-z0-9\s_\-\.\/,]+)\)/", $data['texte'], $matches))
									$extra = $matches[1];
								else
									$extra = '(aucune macrosession)';
								break;
							}
						}
						if ($extra = print_contexte($extra, 1))
							echo "<br><xmp>$extra</xmp>";
						if ($liens)	// inutilisé désormais en fait
							echo "<small style='float:right'>$liens</small>";
					}
					echo '</td>
					<td class="td-n center">', $entry['num_hits'], '</td>
					<td class="td-n right">', $entry['mem_size'], '</td>
					<td class="td-n center">', date(DATE_FORMAT, $entry['access_time']), '</td>
					<td class="td-n center">', date(DATE_FORMAT, $entry['mtime']), '</td>
					<td class="td-n center">', date(DATE_FORMAT, $entry['creation_time']), '</td>';
					
					if ($fieldname == 'info') {
						if ($entry['ttl'])
							echo '<td class="td-n center">' . $entry['ttl'] . ' seconds</td>';
						else
							echo '<td class="td-n center">None</td>';
					}
					if ($entry['deletion_time']) {
						
						echo '<td class="td-last center">', date(DATE_FORMAT, $entry['deletion_time']), '</td>';
					} else if ($MYREQUEST['OB'] == OB_USER_CACHE) {
						
						echo '<td class="td-last center">';
						echo '[<a href="', $MY_SELF, '&DU=', urlencode($entry[$fieldkey]), '">Delete Now</a>]';
						echo '</td>';
					} else {
						echo '<td class="td-last center"> &nbsp; </td>';
					}
					echo '</tr>';
					if ($sh == $MYREQUEST["SH"]) { // Le ZOOM sur une entrée
						echo '<tr>';
						echo '<td colspan="7">';
						
						if (isset($_GET['ZOOM']) and ($_GET['ZOOM'] == 'TEXTECOURT')) {
							$url      = parametre_url($self, 'ZOOM', 'TEXTELONG') . "#key-$sh";
							$menuzoom = "<a href='$url' class='menuzoom'>Voir tout le texte</a> ";
						} else {
							$url      = parametre_url($self, 'ZOOM', 'TEXTECOURT') . "#key-$sh";
							$menuzoom = "<a href='$url' class='menuzoom'>Voir texte abbrégé</a> ";
						}
						$url = parametre_url($self, 'SH', '') . "#key-$sh";
						$menuzoom .= "<a href='$url' class='menuzoom'>Replier</a>";
						
						if (apcu_exists($entry['info'])) {
							$d = apcu_fetch($entry['info'], $success);
							if ($success) {
								echo "<p>$menuzoom</p>";
								if (is_array($d) and (count($d) == 1) and is_serialized($d[0]))
									echo "<xmp>" . spipsafe_unserialize($d[0]) . "</xmp>";
								else
									echo "fetch ok<br><xmp>" . print_r($d, 1) . "</xmp>";
							} else
								echo "fetch failed";
						} else
							echo '(doesnt exist)';
						echo '</td>';
						echo '</tr>';
					}
					$i++;
					if ($i == $MYREQUEST['COUNT'])
						break;
				}
			}
			
		} else {
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
		
}

?>

	</div>
<!--
Based on APCGUI By R.Becker\n$VERSION
-->
</body>
</html>

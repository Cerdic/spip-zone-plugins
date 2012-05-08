<?php
/**
 * @name 		Balise
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @copyright 	CreaDesign 2009 {@link http://creadesignweb.free.fr/}
 * @license		(c) 2009 GNU GPL v3 {@link http://opensource.org/licenses/gpl-license.php GNU Public License}
 * @version 	1.0 (10/2009)
 * @package		Javascript_Scroller
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_JS_SCROLLER($p) {
	return calculer_balise_dynamique($p, JS_SCROLLER, array());
}

function balise_JS_SCROLLER_dyn(
	$width='600', $height='20', $type='articles', $max='50', $cut='40', $dir='ltr', $titre='defaut',
	$speed=false, $description_separator=false, $items_separator=false
) {
	include_spip('js_scroller_fonctions');
	if(!strlen($width) || !$width) $width = '600';
	if(!strlen($height) || !$height) $height = ($type == 'documents') ? '100' : '20';
	if(!strlen($type) || !$type) $type = 'articles';
	if(!strlen($max) || !$max) $max = '50';
	if(!strlen($cut) || !$cut) $cut = '40';
	if(!strlen($dir) || !$dir) $dir = 'ltr';
	// Les petits plus ...
	if(!strlen($items_separator) || !$items_separator) 
		$items_separator = $GLOBALS['js_scroller_defauts']['items_separator'];
	if(!strlen($description_separator) || !$description_separator) 
		$description_separator = $GLOBALS['js_scroller_defauts']['description_separator'];
	if(!strlen($speed) || !$speed) 
		$speed = $GLOBALS['js_scroller_defauts']['speed']; // bien tester avant de changer cette valeur
	// Let's go ...
	$counter = 0;
	$scroller_xml = _JS_SCROLLER_XML.'&type='.$type.'&counter='.$max.'&couper='.$cut.'&width='.$width.'&height='.$height;
	// ouverture du XML
	include_spip('inc/xml');
	$r = spip_xml_load($scroller_xml, false);
	if (function_exists('spip_xml_match_nodes')) 
		$c = spip_xml_match_nodes(',^item,', $r, $r2);
	else {
		$r2= array_shift(array_shift(array_shift(array_shift($r))));
		$c = count($r2);
	}
	if($c) {
		$r3 = &$r2['item'];
		$c = count($r3);
		for($i=0; $i<min($c, $max); $i++) {
			if(isset($r3[$i]['lien']) && isset($r3[$i]['titre'])) {
				$_title = $_width = $_height = $_atts = $_content = false;
				// Cas des images
				if(isset($r3[$i]['url_doc']) && $url_doc = find_in_path($r3[$i]['url_doc'][0])) {
					$_title = $r3[$i]['titre'][0];
					$_width = isset($r3[$i]['width'][0]) ? $r3[$i]['width'][0] : $width;
					$_height = isset($r3[$i]['height'][0]) ? $r3[$i]['height'][0] : $height;
					$_content = "<img src='".$url_doc."' alt='".$r3[$i]['titre'][0]."' width='".$_width."' height='".$_height."' style='width:".$_width."px;height:".$_height."px;' />";
					if (isset($r3[$i]['typedoc']) && strlen($r3[$i]['typedoc']))
						$_atts = " type='".$r3[$i]['typedoc'][0]."'";
				}
				// Autres cas ...
				else {
					$_title = _T('js_scroller:title_link');
					$_content = $r3[$i]['titre'][0];
				}
				$java_scroller_txt .= "<a href='".$r3[$i]['lien'][0]."' title='".$_title."'"
					.($_atts ? $_atts : '')." class='js_scroller_lien'>"
					.$_content
					."</a>"
					.(isset($r3[$i]['description'][0]) && strlen($r3[$i]['description'][0]) ?
						$description_separator."<span class='js_scroller_description'>".$r3[$i]['description'][0]."</span>"
						: '')
					.$items_separator;
				$counter++;
			}
		}
	} 
	else spip_log('JS_SCROLLER PLUGIN : lecture du xml "'.$scroller_xml.'" impossible');

	// Le titre de l'affichage
	$java_scroller_titre = _T('js_scroller:titre_bandeau_'.$type, array('counter' => $counter)).$items_separator;
	if(!strlen($titre) || !$titre || $titre=='non')
		$java_scroller_titre = '';
	elseif ($titre!='defaut' && is_string($titre)) {
		if (!substr_count(trim($titre), ' '))
			$java_scroller_titre = _T($titre).$items_separator;
		else $java_scroller_titre = $titre.$items_separator;
	}

	// et l'affichage ...
	$java_scroller_txt = "<span class='js_scroller_titre'>".$java_scroller_titre.'</span> '.$java_scroller_txt;
	$div=
'
<link rel="stylesheet" href="'.find_in_path(_JS_SCROLLER_CSS).'" type="text/css" media="projection, screen, tv" />
<script language="javascript">'.js_scroller_get_js($width,$height,$dir,$speed).'</script>
<ilayer width="&amp;{wwidth};" height="&amp;{wheight};" name="wslider1" bgcolor="&amp;{wbcolor};"><layer name="wslider2" width="&amp;{wwidth};" height="&amp;{wheight};" onmouseover="sspeed=0;" onmouseout="sspeed=restart"></layer></ilayer>
<script language="javascript" type="text/javascript">
$(function(){startw("'.$java_scroller_txt.'");});
</script>
';
	echo $div;
}
?>
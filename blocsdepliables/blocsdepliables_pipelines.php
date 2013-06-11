<?php
/**
 * Plugin Blocs Dépliables
 * (c) 2013 Collectif
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function blocsdepliables_insert_head($flux){
	include_spip("inc/config");
	$slide = lire_config('blocsdepliables/animation','aucun');
	if ($slide=="millisec") $slide = lire_config('blocsdepliables/duree',100);
	$js_cookie = "";
	if (lire_config('blocsdepliables/cookie',0)){
		$js_cookie = find_in_path("javascript/jquery.cookie.js");
		$js_cookie = "if(jQuery('div.cs_blocs').length) jQuery.getScript('$js_cookie', cs_blocs_cookie);";
	}

	$js = "var blocs_replier_tout = ".intval(lire_config('blocsdepliables/unique',0)).";
var blocs_slide = "._q($slide).";
var blocs_title_sep = /".preg_quote(_BLOC_TITLE_SEP)."/g;
var blocs_title_def = "._q(_T('couteau:bloc_deplier')._BLOC_TITLE_SEP._T('couteau:bloc_replier')).";
jQuery(function(){
blocs_init.apply(document);
if(typeof onAjaxLoad=='function') onAjaxLoad(blocs_init);
$js_cookie });";

	$flux .= "<script type='text/javascript' src='".find_in_path("js/blocs.js")."'></script>
<script type='text/javascript'>/*<![CDATA[*/\n$js\n/*]]>*/</script>";
	return $flux;
}

function blocsdepliables_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path("css/blocs.css").'" />';
	return $flux;
}

/*
   Cet outil 'blocs' permet aux redacteurs d'un site spip d'inclure des blocs visibles ou invisibles dans leurs textes
   balises : <bloc></bloc> ou <invisible></invisible>, et <visible></visible>
   le titre est obtenu en sautant deux lignes a l'interieur du bloc
   Attention : seules les balises en minuscules sont reconnues.
*/

// depliage/repliage - fonction de personnalisation des title a placer dans mes_fonctions.php
// function blocs_title($titre='', $corps='', $num='') {
//	return array(_T('couteau:bloc_deplier'), _T('couteau:bloc_replier'));
// }

function blocsdepliables_callback($matches) {
	list($titre, $corps) = preg_split(',(\n\n|\r\n\r\n|\r\r),', trim($matches[3]), 2);
	// pas de corps !
	if(!strlen($corps = trim($corps))) {
		$corps = $titre;
		$titre = preg_replace(',[\n\r]+,s', ' ', couper(propre($titre), 30));
	}
	// pas d'intertitre !
	$titre = preg_replace(',^{{{(.*)}}}$,', '$1', trim($titre));
	if(!strlen($titre)) $titre = '???';
	// un resume facultatif
	if(preg_match(',<resume>(.*)</resume>\s?(.*)$,ms', $corps, $res))
		{ $corps = $res[2]; $res = $res[1]; } else $res = '';
	// types de blocs : bloc|invisible|visible
	if ($matches[1]=='visible' || defined('_CS_PRINT')) {
		$h = $d = '';
		$r = ' blocs_invisible blocs_slide';
	} else {
		$h = ' blocs_replie';
		$d = ' blocs_invisible blocs_slide';
		$r = '';
	}

	// blocs numerotes
	$b = strlen($matches[2])?" cs_bloc$matches[2]' id='deplier_num$matches[2]":'';
	// title
	$title = function_exists('blocs_title')
		?"<div class='blocs_title blocs_invisible'>".join(_BLOC_TITLE_SEP, blocs_title($titre, $corps, $matches[2], $h<>'')).'</div>'
		:''; // valeur par defaut geree en JS
	$hn = blocdepliable_balise_titre();
	return "<div class='cs_blocs$b'><$hn class='blocs_titre$h blocs_click'><a href='javascript:;'>$titre</a></$hn>"
		.(strlen($res)?"<div class='blocs_resume$r'>\n$res\n</div>":"")
		."<div class='blocs_destination$d'>\n\n".blocsdepliables_rempl($corps)."\n\n</div>$title</div>";
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function blocsdepliables_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// balises blocs|visible|invisible : il faut un callback pour analyser l'interieur du texte
	return preg_replace_callback(',<(bloc#?|visible#?|invisible#?|blocintertitre#?)([0-9]*)>(.*?)</\1\2>,ms', 'blocsdepliables_callback', $texte);
}


/**
 * evite les transformations typo dans les balises $balises
 * par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
 *
 * @param $texte
 *   $texte a filtrer
 * @param $filtre
 *   le filtre a appliquer pour transformer $texte
 *   si $filtre = false, alors le texte est retourne protege, sans filtre
 * @param $balises
 *   balises concernees par l'echappement
 *   si $balises = '' alors la protection par defaut est sur les balises de _PROTEGE_BLOCS
 *   si $balises = false alors le texte est utilise tel quel
 * @param null|array $args
 *   arguments supplementaires a passer au filtre
 * @return string
 */
function blocsdepliables_filtre_texte_echappe($texte, $filtre, $balises='', $args=NULL){
	if(!strlen($texte)) return '';

	if ($filtre!==false){
		$fonction = chercher_filtre($filtre,false);
		if (!$fonction) {
			spip_log("blocsdepliables_filtre_texte_echappe() : $filtre() non definie",_LOG_ERREUR);
			return $texte;
		}
		$filtre = $fonction;
	}

	// protection du texte
	if($balises!==false) {
		if(!strlen($balises)) $balises = _PROTEGE_BLOCS;//'html|code|cadre|frame|script';
		else $balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
		if (!function_exists('echappe_html'))
			include_spip('inc/texte_mini');
		$texte = echappe_html($texte, 'FILTRETEXTECHAPPE', true, $balises);
	}
	// retour du texte simplement protege
	if ($filtre===false) return $texte;
	// transformation par $fonction
	if (!$args)
		$texte = $filtre($texte);
	else {
		array_unshift($args,$texte);
		$texte = call_user_func_array($filtre, $args);
	}

	// deprotection des balises
	return echappe_retour($texte, 'FILTRETEXTECHAPPE');
}


// fonction pipeline
function blocsdepliables_pre_typo($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// on remplace apres echappement
	return blocsdepliables_filtre_texte_echappe($texte, 'blocsdepliables_rempl');
}

// 2 fonctions pour le plugin Porte Plume, s'il est present (SPIP>=2.0)
function blocsdepliables_porte_plume_cs_pre_charger($flux) {
	$r = array(array(
		"id" => 'blocs_bloc',
		"name" => _T('couteau:pp_blocs_bloc'),
		"className" => 'blocs_bloc',
		"replaceWith" => "\n<bloc>"._T('couteau:pp_un_titre')."\n\n"._T('couteau:pp_votre_texte')."\n</bloc>\n",
		"display" => true), array(
		"id" => 'blocs_visible',
		"name" => _T('couteau:pp_blocs_visible'),
		"className" => 'blocs_visible',
		"replaceWith" => "\n<visible>"._T('couteau:pp_un_titre')."\n\n"._T('couteau:pp_votre_texte')."\n</visible>\n",
		"display" => true));
	foreach(cs_pp_liste_barres('blocs') as $b)
		$flux[$b] = isset($flux[$b])?array_merge($flux[$b], $r):$r;
	return $flux;
}
function blocsdepliables_porte_plume_lien_classe_vers_icone($flux) {
	$flux['blocs_bloc'] = 'bloc_invisible.png';
	$flux['blocs_visible'] = 'bloc_visible.png';
	return $flux;
}

?>
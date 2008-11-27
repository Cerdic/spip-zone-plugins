<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2007               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite spip 1.9
if(!function_exists(ajax_retour)) { 
	function ajax_retour($corps) {
		$c = $GLOBALS['meta']["charset"];
		header('Content-Type: text/html; charset='. $c);
		$c = '<' . "?xml version='1.0' encoding='" . $c . "'?" . ">\n";
		echo $c, $corps;
		exit;
	}
}
if(defined('_SPIP19100') && !function_exists('spip_xml_load')) { 
	function spip_xml_load($url){
		include_spip('inc/distant');
		$texte = recuperer_page($url);
		include_spip('inc/plugin');
		return parse_plugin_xml($texte);
	}
}

function exec_cs_boite_rss_dist() {
	if (!cout_autoriser()) {
		include_spip('inc/minipres');
		echo defined('_SPIP19100')?minipres( _T('avis_non_acces_page')):minipres();
		exit;
	}
	include_spip('cout_define');
	cout_define('distant');
	$p = '';
	// on cherche le flux rss toutes les _CS_RSS_UPDATE minutes
	$force = _request('force')=='oui';
	if(!$force) {
		$lastmodified = @file_exists(_DIR_RSS_TMP)?@filemtime(_DIR_RSS_TMP):0;
		if (time()-$lastmodified < _CS_RSS_UPDATE) lire_fichier(_DIR_RSS_TMP, $p);
	}
	if(strlen($p)) { ajax_retour($p); return; }
	include_spip('action/editer_site');
	include_spip('inc/xml');
	$r = spip_xml_load(_CS_RSS_SOURCE);
	if (function_exists('spip_xml_match_nodes')) $c = spip_xml_match_nodes(',^item$,', $r, $r2);
	else {
		$r2= array_shift(array_shift(array_shift(array_shift($r))));
		$c = count($r2);
	}
	if($c) {
		$r3 = &$r2['item'];
		$c = count($r3); $p='';
		for($i=0; $i<min($c, _CS_RSS_COUNT); $i++) {
		 $l = $r3[$i]['link'][0];
		 $t = str_replace('&amp;', '&', htmlentities($r3[$i]['title'][0], ENT_NOQUOTES, "UTF-8"));
		 $t = preg_replace(',\s*&#8364;(&brvbar;)?,', '&nbsp;(&hellip;)', $t);
		 $t = preg_replace(',^(.*?):,', "&bull; <a href='$l' class='spip_out' target='_cout'>$1</a>:", $t);
			 $p .= "<li style='padding-top:0.6em;'>$t</li>";
		}
	} else {
		// pour cs_lien()
		include_spip('cout_fonctions');
		$p = '<span style="color: red;">'._T('couteauprive:erreur:probleme', array('pb'=>cs_lien(_CS_RSS_SOURCE,_T('couteauprive:erreur:distant')))).'</span>';
	}
	include_spip('inc/filtres');
	$du = affdate_heure(date('Y-m-d H:i:s',time()));
	$p = '<ul style="list-style-type:none; padding:0; margin:0; ">'.$p
		.'</ul><p class="spip_xx-small" style="border-top:solid gray thin;"><b>'
		._T('couteauprive:rss_edition')."</b><br/>$du</p>"
		.'<p style="text-align:right"><a href="'
		.generer_url_ecrire('admin_couteau_suisse','var_mode=calcul', true).'" onclick="'
		."javascipt:jQuery('div.cs_boite_rss').load('".generer_url_ecrire('cs_boite_rss', 'force=oui', true).'\');return false;">'
		._T('couteauprive:rss_actualiser').'</a> | <a href="'
		._CS_RSS_SOURCE.'">'
		._T('couteauprive:rss_source').'</a></p>';
	if($c) ecrire_fichier(_DIR_RSS_TMP, $p);
	
	ajax_retour($p);
}

?>
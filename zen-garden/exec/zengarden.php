<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_zengarden(){
	if (!autoriser('administrer','theme',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	include_spip('inc/zengarden');
	$themes = zengarden_charge_themes(_DIR_THEMES,_request('tous'));

	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('zengarden:choix_theme'));
	
	echo debut_gauche("choix_theme",true);
	
	echo debut_boite_info(true);
	echo "<img src='"._DIR_PLUGIN_ZENGARDEN."img_pack/themes-128.png' width='128' height='128' />";
	echo propre(_T('zengarden:info_page'));	
	echo fin_boite_info(true);
	#echo recuperer_fond('prive/zengarden_theme_actif',array('themes'=>$themes,'selection'=>$GLOBALS['meta']['zengarden_theme']?$GLOBALS['meta']['zengarden_theme']:''));
	
	echo debut_droite("choix_theme",true);


	$contexte = array('themes'=>$themes,'selection'=>$GLOBALS['meta']['zengarden_theme']?$GLOBALS['meta']['zengarden_theme']:'');
	$contexte = array_merge($contexte,$_GET);
	echo recuperer_fond('prive/zengarden_theme',$contexte,array('ajax'=>true));
	echo fin_gauche(),fin_page();
}

?>
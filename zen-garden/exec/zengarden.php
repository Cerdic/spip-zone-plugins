<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2009 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_zengarden(){
	if (!autoriser('administrer','theme',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('zengarden:choix_theme'));
	
	echo debut_gauche("choix_theme",true);
	
	echo debut_boite_info(true);
	echo "<img src='"._DIR_PLUGIN_ZENGARDEN."img_pack/themes-128.png' width='128' height='128' alt='Zen Garden' />";
	echo propre(_T('zengarden:info_page'));	
	echo fin_boite_info(true);

	if (isset($GLOBALS['meta']['zengarden_switcher']))
		echo bouton_action(_T('zengarden:switcher_desactiver'), generer_action_auteur("zengarden_activer_switcher", "off",self()));
	else
		echo bouton_action(_T('zengarden:switcher_activer'), generer_action_auteur("zengarden_activer_switcher", "on",self()));

	echo debut_droite("choix_theme",true);


	$contexte = array('selection'=>$GLOBALS['meta']['zengarden_theme']?$GLOBALS['meta']['zengarden_theme']:'');
	$contexte = array_merge($contexte,$_GET);
	echo recuperer_fond('prive/zengarden_theme',$contexte,array('ajax'=>true));
	echo fin_gauche(),fin_page();
}

?>
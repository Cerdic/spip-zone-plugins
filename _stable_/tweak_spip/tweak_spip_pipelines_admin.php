<?php
include_spip('tweak_spip');

if (!defined('_DIR_PLUGIN_TWEAK_SPIP')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_TWEAK_SPIP',(_DIR_PLUGINS.end($p)));
}

function tweak_spip_affiche_droite($flux){
	return tweak_pipeline('affiche_droite', $flux);
}
function tweak_spip_affiche_gauche($flux){
	return tweak_pipeline('affiche_gauche', $flux);
}
function tweak_spip_affiche_milieu($flux){
	return tweak_pipeline('affiche_milieu', $flux);
}
function tweak_spip_ajouter_boutons($flux){
	return tweak_pipeline('ajouter_boutons', $flux);
}
function tweak_spip_ajouter_onglets($flux){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) 
		if ($flux['args']=='configuration')
			$flux['data']['tweak_spip']= new Bouton("administration-24.gif", _T('tweak:titre'), generer_url_ecrire("tweak_spip_admin"));
	return tweak_pipeline('ajouter_onglets', $flux);
}
function tweak_spip_body_prive($flux){
	return tweak_pipeline('body_prive', $flux);
}
function tweak_spip_exec_init($flux){
	return tweak_pipeline('exec_init', $flux);
}
function tweak_spip_header_prive($flux){
	$flux .= tweak_insert_css();
	return tweak_pipeline('header_prive', $flux);
}
function tweak_spip_install($action){
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			break;
		case 'install':
			break;
		case 'uninstall':
			effacer_meta('tweaks');
			effacer_meta('tweaks_var');
			ecrire_metas();
			break;
	}
}	


?>
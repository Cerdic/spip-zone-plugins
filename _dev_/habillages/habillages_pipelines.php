<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

function habillages_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  if (_request('exec')=='config_habillages'){
			  $boutons_admin['configuration']->sousmenu['config_habillages']= new Bouton(
			"../"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_icone-22.png",  // icone
			_L('Habillages')	// titre
			);
	  		}
	  		else {
		  $boutons_admin['configuration']->sousmenu['config_habillages']= new Bouton(
			"../"._DIR_PLUGIN_HABILLAGES."/img_pack/habillages_icone-22.png",  // icone
			_L('Habillages')	// titre
			);
		}
		}
		return $boutons_admin;
	}

function habillages_ajouter_onglets($flux) {
	if (_request('exec')=='config_habillages' || _request('exec')=='habillages_squelettes' || _request('exec')=='habillages_styles' || _request('exec')=='habillages_images') {
		$flux['data']['accueil']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_icone-22.png', 'Accueil', generer_url_ecrire("config_habillages"));
	$flux['data']['squelettes']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_squelettes-22.png', 'Squelettes', generer_url_ecrire("habillages_squelettes"));
	$flux['data']['styles']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_styles-22.png', 'Styles', generer_url_ecrire("habillages_styles"));
	$flux['data']['images']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_images-22.png', 'Images', generer_url_ecrire("habillages_images"));
	$flux['data']['icones']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_icones_prive-22.png', 'Icones', generer_url_ecrire("habillages_icones"));
 
}
return $flux;
}

?>
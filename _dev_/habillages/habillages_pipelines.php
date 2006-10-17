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
	if (_request('exec')=='config_habillages' || _request('exec')=='habillages_squelettes' || _request('exec')=='habillages_extras' || _request('exec')=='habillages_images' || _request('exec')=='habillages_icones' || _request('exec')=='habillages_aide' || _request('exec')=='habillages_themes') {
		$flux['data']['accueil']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_icone-22.png', 'Accueil', generer_url_ecrire("config_habillages"));
	$flux['data']['squelettes']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_squelettes-22.png', 'Squelettes', generer_url_ecrire("habillages_squelettes"));
	$flux['data']['themes']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_themes-22.png', 'Th&egrave;mes', generer_url_ecrire("habillages_themes"));
	$flux['data']['extras']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_extras-22.png', 'Extras', generer_url_ecrire("habillages_extras"));
	$flux['data']['logotheque']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_logos-22.png', 'Logoth&egrave;que', generer_url_ecrire("habillages_logos"));
	$flux['data']['icones']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_icones-22.png', 'Ic&#244;nes', generer_url_ecrire("habillages_icones"));
	$flux['data']['aide']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillages_aide-22.png', 'Aide', generer_url_ecrire("habillages_aide")); 
}
return $flux;
}

function habillages_header_prive($flux) {
	if (_request('exec')=='config_habillages' || _request('exec')=='habillages_squelettes' || _request('exec')=='habillages_themes' || _request('exec')=='habillages_images' || _request('exec')=='habillages_icones' || _request('exec')=='habillages_images') {
		$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_HABILLAGES.'img_pack/habillages_habillages.css" type="text/css" >'."\n";
		}
		
	global $exec;
	include_spip('inc/meta');
	lire_metas();
	$theme_link = $GLOBALS['meta']['habillages_icones'];
	if (isset($GLOBALS['meta']['habillages_icones']) AND ($c=$GLOBALS['meta']['habillages_icones'])!="") {
  	$flux .= '<link rel="stylesheet" href="'.$theme_link.'style.css" type="text/css" />'."\n";
	}
	return $flux;
}

function habillages_affichage_final($texte) {
		lire_metas();
		$lire_meta_logo_site = $GLOBALS['meta']['habillages_logo_site'];
		
		$texte = str_replace('alt="', 'alt="'.$lire_meta_logo_site, $texte);
		return $texte;
}
	
?>
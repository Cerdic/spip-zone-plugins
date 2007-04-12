<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

function habillages_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  if (_request('exec')=='habillages_accueil'){
			  $boutons_admin['configuration']->sousmenu['habillages_accueil']= new Bouton(
			"../"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_icone-22.png",  // icone
			_L('Habillages')	// titre
			);
	  		}
	  		else {
		  $boutons_admin['configuration']->sousmenu['habillages_accueil']= new Bouton(
			"../"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_icone-22.png",  // icone
			_L('Habillages')	// titre
			);
		}
		}
		return $boutons_admin;
	}

function habillages_ajouter_onglets($flux) {
	lire_metas();
	$habillages_is_theme = $GLOBALS['meta']['habillages_is_themes'];
	$habillages_is_extras = $GLOBALS['meta']['habillages_is_extras'];
	$squelettes_is_gestionnaire = $GLOBALS['meta']['habillages_squelettes_on'];
	$themes_is_gestionnaire = $GLOBALS['meta']['habillages_themes_on'];
	$exras_is_gestionnaire = $GLOBALS['meta']['habillages_extras_on'];
	$logos_is_gestionnaire = $GLOBALS['meta']['habillages_logos_on'];
	$icones_is_gestionnaire = $GLOBALS['meta']['habillages_icones_on'];
	$config_is_gestionnaire = $GLOBALS['meta']['habillages_config_on'];

	
	if (_request('exec')=='habillages_accueil' || _request('exec')=='habillages_squelettes' || _request('exec')=='habillages_extras' || _request('exec')=='habillages_logos' || _request('exec')=='habillages_icones' || _request('exec')=='habillages_aide' || _request('exec')=='habillages_themes' || _request('exec')=='habillages_config') {
		$flux['data']['accueil']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_accueil-22.png', 'Accueil', generer_url_ecrire("habillages_accueil"));
	
	# Si l'utilisateur/trice a choisi le gestionnaire de squelette, on affiche ce dernier.
	if ($squelettes_is_gestionnaire == "oui" || $squelettes_is_gestionnaire == "") {
		$flux['data']['squelettes']= new Bouton(
		_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_squelettes-22.png', 'Squelettes', generer_url_ecrire("habillages_squelettes"));
	
			if ($habillages_is_theme == "oui") {
				$flux['data']['themes']= new Bouton(
				_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_themes-22.png', 'Th&egrave;mes', generer_url_ecrire("habillages_themes"));
			}
			if ($habillages_is_extras == "oui") {
				$flux['data']['extras']= new Bouton(
				_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_extras-22.png', 'Extras', generer_url_ecrire("habillages_extras"));
			}
	}
	
	if ($logos_is_gestionnaire == "oui") {
		$flux['data']['logotheque']= new Bouton(
		_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_logos-22.png', 'Logoth&egrave;que', generer_url_ecrire("habillages_logos"));
	}
	
	if ($icones_is_gestionnaire == "oui" || $icones_is_gestionnaire == "") {
		$flux['data']['icones']= new Bouton(
		_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_icones-22.png', 'Ic&#244;nes', generer_url_ecrire("habillages_icones"));
	}
	if ($config_is_gestionnaire == "oui") {
		$flux['data']['config']= new Bouton(
		_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_config-22.png', 'Configuration', generer_url_ecrire("habillages_config"));
	}
	$flux['data']['aide']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'img_pack/habillages_aide-22.png', 'Aide', generer_url_ecrire("habillages_aide")); 
}
return $flux;
}

function habillages_header_prive($flux) {
	if (_request('exec')=='habillages_accueil' || _request('exec')=='habillages_squelettes' || _request('exec')=='habillages_themes' || _request('exec')=='habillages_logos' || _request('exec')=='habillages_icones' || _request('exec')=='habillages_extras' || _request('exec')=='habillages_aide' || _request('exec')=='habillages_config') {
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

function habillages_body_prive($texte) {
		$texte = str_replace('border-bottom', 'Oups', $texte);
		return $texte;
}
	
?>
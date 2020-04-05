<?php
/**
 * Plugin Thème privé 
 * Arnaud Bérard - Mist. GraphX
 * Licence GNU/GPL
 * Sources :
 * https://contrib.spip.net/Themes-pour-l-interface-privee
 * 
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function mes_preferences_header_prive($flux) {
        global $visiteur_session, $REQUEST_URI;
	$c = (is_array($visiteur_session)
	AND is_array($visiteur_session['prefs']))
		? $visiteur_session['prefs']['theme']
		: 1;

	$couleurs = charger_fonction('couleurs', 'inc');
	
	$interface = $GLOBALS['visiteur_session']['prefs']['theme'];
	if (strlen($interface) == 0) $interface = "spip_dist";
	
        // ajout des css du plugin :
        $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/mes_preferences.css').'" media="screen" />';
        
        // Source : https://contrib.spip.net/Themes-pour-l-interface-privee
	//generer_url_public('style_prive', parametres_css_prive())
        if ($interface == "spip_dist") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('spip_dist', parametres_css_prive()).'" id="css_spip_dist" />';
	else if ($interface == "spip2") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('spip_2', parametres_css_prive()).'" id="css_spip2" />';
        else if ($interface == "vector_icons") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('vector_icons', parametres_css_prive()).'" id="css_vector_icons" />';
	else if ($interface == "vector_icons_2") $flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('vector_icons_2', parametres_css_prive()).'" id="css_vector_icons_2" />';

	return $flux;
}



?>
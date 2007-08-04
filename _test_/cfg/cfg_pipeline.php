<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajoute le bouton d'amin aux webmestres
function cfg_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('configurer')) {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg'] =
			new Bouton(
			_DIR_PLUGIN_CFG."cfg-22.png",  // icone
			_L('CFG'),	// titre
			generer_url_ecrire('cfg'),
			NULL,
			'cfg'
			);
	}
	return $flux;
}


// ajoute le css de CFG a l'espace prive
function cfg_header_prive($texte) {
	global $auteur_session, $spip_display, $spip_lang;
	if (_request('exec') == 'cfg'){
		if ($GLOBALS["spip_version"] >= 1.93) {
			$couleurs = charger_fonction('couleurs', 'inc');
			$paramcss = 'ltr='. $GLOBALS['spip_lang_left'] . '&'. $couleurs($auteur_session['prefs']['couleur']);
			$css = generer_url_public('jquery.tabs_prive', $paramcss);
		}
		else {
			$css = generer_url_public('jquery.tabs_prive');
		}
		$js = find_in_path('javascript/jquery.tabs.pack.js');
		$texte.= "<link rel='stylesheet' type='text/css' href='" . _DIR_PLUGIN_CFG . "css/cfg.css' />
			<link rel='stylesheet' type='text/css' href='$css' />
			<!--[if lte IE 7]>
			<link rel='stylesheet' href='" . _DIR_PLUGIN_CFG . "css/jquery.tabs-ie.css' type='text/css' media='projection, screen'>
			<![endif]-->
			<script type='text/javascript' src='$js'></script>" . "\n";
	}
	return $texte;
}
?>

<?php
// Affectation de la liste des squelettes disponibles
// --------------------------------------------------

	// Inclusion du fichier de configuration
	include_spip('inc/switcher_config');
	
	// Squelettes par défaut : squelettes courant + dist
	$squelettes_alternatifs = array();
	if (defined('SWITCHER_DOSSIERS_SQUELETTES')) {
		foreach(explode(',',SWITCHER_DOSSIERS_SQUELETTES) as $theme)
			$squelettes_alternatifs[$theme] = $theme;
	// Squelettes supplémentaires : tous les répertoires contenus dans 	$repertoire_squelettes_alternatifs
	} else if (is_dir($repertoire_squelettes_alternatifs)) {
	   if ($dh = opendir($repertoire_squelettes_alternatifs)) {
	       while (($file = readdir($dh)) !== false) {
	       		if ( (is_dir($repertoire_squelettes_alternatifs."/".$file)) AND ($file[0]!=".") ) $squelettes_alternatifs[$file]=$repertoire_squelettes_alternatifs."/".$file;
	       }
	   closedir($dh);
	   }
	}
	else {
		$squelettes_alternatifs = array(
		'defaut' => '',
		'dist' => 'dist');
	}


// Contrib de Fil : voir http://trac.rezo.net/trac/spip-zone/browser/_contribs_/switcher/switcher.php
// --------------------------------------------------------------------------------------------------
	
	// Demande-t-on un cookie de squelette ?
	if (isset($_GET['var_theme'])) {
		include_spip('inc/cookie');
		// S'il est valide on le pose
		if (isset($squelettes_alternatifs[$_GET['var_theme']]))
			spip_setcookie('spip_theme', $_COOKIE['spip_theme'] = $_GET['var_theme'], NULL, '/');
		// S'il est invalide on supprime un eventuel cookie
		else
			spip_setcookie('spip_theme', $_COOKIE['spip_theme'] = '', -24*3600, '/');
	}

	// Porte-t-on un cookie de squelette ?
	if (isset($_COOKIE['spip_theme'])
	AND isset($squelettes_alternatifs[$_COOKIE['spip_theme']]))
		$dossier_squelettes = $squelettes_alternatifs[$_COOKIE['spip_theme']];

?>

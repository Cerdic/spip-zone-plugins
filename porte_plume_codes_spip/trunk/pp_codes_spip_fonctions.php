<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// pour definir des liens vers
if (!defined('CODES_SPIP_BRANCHE')) {
	#define(CODES_SPIP_BRANCHE, '3.0');
	define(CODES_SPIP_BRANCHE, 'dev');
}


function glossaire_core($chemin, $ligne=0) {
	// gestion des aiguillages automatiques
	// vers core_plugins lorsque le chemin indique un fichier qui y pointe.
	static $aiguillages = array(
		'dev' => array(
			'plugins-dist' => '',
			'squelettes-dist' => 'dist',
		),
		'3.0' => array(
			'plugins-dist' => '',
			'squelettes-dist' => 'dist',
		),
		'2.1' => array(
			'extensions' => '',
		),
	);
	if (isset($aiguillages[CODES_SPIP_BRANCHE])) {
		foreach ( $aiguillages[CODES_SPIP_BRANCHE] AS $repertoire => $remplacer ) {
			if (substr($chemin, 0, $len = strlen($repertoire)) == $repertoire) {
				return glossaire_spip_url('core_plugins', $remplacer . substr($chemin, $len), $ligne);
			}
		}
	}

	// sinon c'est la...
	return glossaire_spip_url('core', $chemin, $ligne);
}


function glossaire_core_plugins($chemin, $ligne=0) {
	return glossaire_spip_url('core_plugins', $chemin, $ligne);
}

function glossaire_zone_plugins($chemin, $ligne=0) {
	return glossaire_spip_url('zone_plugins', $chemin, $ligne);
} 


function glossaire_spip_url($type='core', $chemin, $ligne) {
	static $sources = array(
		'dev' => array(
			'core' => 'http://core.spip.org/projects/spip/repository/entry/spip/@file@',
			'core_plugins' => 'http://zone.spip.org/trac/spip-zone/browser/_core_/plugins/@file@',
			'zone_plugins' => 'http://zone.spip.org/trac/spip-zone/browser/_plugins_/@file@',
		),
		'branches' => array(
			'core' => 'http://core.spip.org/projects/spip/repository/entry/branches/spip-@branche@/@file@',
			'core_plugins' => 'http://zone.spip.org/trac/spip-zone/browser/_core_/branches/spip-@branche@/plugins/@file@',
			'zone_plugins' => 'http://zone.spip.org/trac/spip-zone/browser/_plugins_/@file@',
		),
	);

	// retrouver selon la branche
	if (CODES_SPIP_BRANCHE == 'dev') {
		$source = $sources['dev'];
	} else {
		$source = $sources['branches'];
	}

	// retrouver selon le type demande
	$source = $source[$type];

	// inserer les valeurs correctes de branche et de fichier et ligne
	$source = str_replace('@branche@', CODES_SPIP_BRANCHE, $source);

	// on remplace le chemin du fichier et ajoute l'eventuelle ligne.
	return str_replace('@file@', $chemin, $source) . ($ligne ? '#L' . $ligne : '');
}





##
# CODE MORT
# maintenu quelques temps
##


// trac SVN
# @define('_URL_BROWSER_TRAC', 'http://trac.rezo.net/trac/spip/browser/spip/');

// trac GIT
#@define('_URL_BROWSER_TRAC', 'http://core.spip.org/trac/spip/browser/@file@?rev=spip-2.1');

// redmine SVN (trunk)
@define('_URL_BROWSER_TRAC', 'http://core.spip.org/projects/spip/repository/entry/spip/@file@');

// redmine SVN (branche 2.1)
#@define('_URL_BROWSER_TRAC', 'http://core.spip.org/projects/spip/repository/entry/branches/spip-2.1/@file@');

/*
 * Obsolete, a migrer vers les nouveaux glossaires
 * 
 * Un raccourci pour des chemins vers de trac
 * [?ecrire/inc_version.php#trac]
 * [?ecrire/inc_version.php#tracNNN] // NNN = numero de ligne
 * 
 */
if (!function_exists('glossaire_trac')) {
	function glossaire_trac($texte, $id=0) {
		// si @file@ present dans le define, on remplace par le texte
		if (false !== strpos(_URL_BROWSER_TRAC, '@file@')) {
			return str_replace('@file@', $texte, _URL_BROWSER_TRAC) . ($id ? '#L'.$id : '');
		}
		
		// sinon, on met bout a bout comme avant...
		return _URL_BROWSER_TRAC . $texte . ($id ? '#L'.$id : '');
	} 
}
?>

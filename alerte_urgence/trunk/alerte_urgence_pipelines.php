<?php
/*
 * Plugin Alerte Urgence
 * (c) 2010 Cedric
 * Distribue sous licence GPL
 *
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function alerte_urgence_affichage_final($flux) {
	if (
		$GLOBALS['html'] // si c'est bien du HTML
		and (strpos($flux,'<body')) !== false // et qu'on a une balise <body>
	) {
		include_spip('inc/config');
		//$flux = substr_replace($flux, recuperer_fond('inclure/alerte_urgence'), $p, 0);
		$flux = preg_replace(
			'|<body[^>]*>|is',
			'$0'.recuperer_fond(
				'inclure/alerte_urgence',
				array('signature' => lire_config('alerte_urgence/texte'))
			),
			$flux
		);
	}
	return $flux;
}


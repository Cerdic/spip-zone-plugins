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
		and (
			($insertion = strpos($flux, '<!-- inserer_alerte_urgence -->')) !== false // et qu'on a la chaîne d'insertion
			or
			strpos($flux,'<body') !== false // ou qu'on a une balise <body>
		)
	) {
		include_spip('inc/config');
		
		// On génère l'alerte
		$alerte = recuperer_fond(
			'inclure/alerte_urgence',
			array('signature' => lire_config('alerte_urgence/texte'))
		);
		
		// Si c'est l'insertion, on remplace
		if ($insertion !== false) {
			$flux = str_replace('<!-- inserer_alerte_urgence -->', $alerte, $flux);
		}
		// Sinon on la met au début du body
		else {
			$flux = preg_replace(
				'|<body[^>]*>|is',
				'$0'.$alerte,
				$flux
			);
		}
	}
	
	return $flux;
}


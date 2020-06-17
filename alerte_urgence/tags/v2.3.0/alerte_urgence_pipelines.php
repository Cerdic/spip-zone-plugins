<?php
/*
 * Plugin Alerte Urgence
 * (c) 2010 Cedric
 * Distribue sous licence GPL
 *
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insérer l'alerte sur toutes les pages du site, sauf si limité à l'accueil
 **/
function alerte_urgence_affichage_final($flux) {
	if (
		$GLOBALS['html'] // si c'est bien du HTML
		and include_spip('inc/config')
		and $config = lire_config('alerte_urgence')
		// S'il y a bien un texte à afficher
		and isset($config['texte'])
		and $config['texte']
		and (
			($insertion = strpos($flux, '<!-- inserer_alerte_urgence -->')) !== false // et qu'on a la chaîne d'insertion
			or
			strpos($flux,'<body') !== false // ou qu'on a une balise <body>
		)
		// Si pas de limite ou si limité à l'accueil et qu'on y est
		and (
			!isset($config['limiter_accueil'])
			or !$config['limiter_accueil']
			or (parse_url(self(), PHP_URL_PATH) == './')
		)
	) {
		// On génère l'alerte
		$alerte = recuperer_fond(
			'inclure/alerte_urgence',
			array('signature' => $config['texte'])
		);
		
		// Si c'est l'insertion, on remplace
		if ($insertion !== false) {
			$flux = str_replace('<!-- inserer_alerte_urgence -->', $alerte, $flux);
		}
		// Sinon on la met au début du body
		elseif (!$config['desactiver_placement_auto']) {
			$flux = preg_replace(
				'|<body[^>]*>|is',
				'$0'.$alerte,
				$flux
			);
		}
	}
	
	return $flux;
}


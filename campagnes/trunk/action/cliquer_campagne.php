<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');
include_spip('inc/headers');

/**
 * Incrémente le compteur de clics + redirige vers la bonne URL
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_cliquer_campagne_dist($arg=null) {
	if (is_null($arg)){
		//~ $securiser_action = charger_fonction('securiser_action', 'inc');
		//~ $arg = $securiser_action();
		$arg = _request('arg');
	}

	// Si on a bien un id valide et que c'est pas un bot
	// Et que la campagne existe
	if (
		$id_campagne = intval($arg)
		and !_IS_BOT
		and $campagne = sql_fetsel('url,id_encart', 'spip_campagnes', 'id_campagne = '.$id_campagne)
		
	) {
		include_spip('inc/campagnes');
		$infos = campagnes_recuperer_infos_visiteur();		
		
		// Si la personne n'a pas déjà cliqué dessus le jour même
		if (!sql_fetsel(
				'id_campagne',
				'spip_campagnes_clics',
				array(
					"id_campagne=$id_campagne",
					'cookie='.sql_quote($infos['cookie']),
					'date=DATE(NOW())',
				)
			)
		) {
			// On cherche la page d'où est venu le clic, soit explicitement soit par le referer
			$page = _request('referer');
			if (!$page) {
				$page = ltrim(str_replace($GLOBALS['url_site'], '', $_SERVER['HTTP_REFERER']), '/');
			}
			
			// On ajoute la date et la pub dans les infos à garder
			$infos = array_merge($infos, array('id_campagne' => $id_campagne, 'id_encart' => $campagne['id_encart'], 'page' => $page, 'date' => 'NOW()'));
			
			// On enregistre le clic
			$ok = sql_insertq(
				'spip_campagnes_clics',
				$infos
			);
		}
				
		// On redirige toujours vers la campagne
		redirige_par_entete($campagne['url']);
	}
	// Si on n'a pas trouvé de campagne, on redirige vers le site lui-même
	else {
		redirige_par_entete($GLOBALS['meta']['adresse_site']);
	}
}

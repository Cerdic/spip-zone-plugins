<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

/**
 * Incrémente le compteur de clics + redirige vers la bonne URL
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_cliquer_campagne_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// Si on a bien un id valide et que c'est pas un bot
	if ($id_campagne = intval($arg)
		and !_IS_BOT
		and $campagne = sql_fetsel('url,id_encart', 'spip_campagnes', 'id_campagne = '.$id_campagne)
	){
		include_spip('inc/campagnes');
		$infos = campagnes_recuperer_infos_visiteur();
		
		// On cherche la page d'où est venu le clic, soit explicitement soit par le referer
		$page = _request('referer');
		if (!$page){ $page = ltrim(str_replace($GLOBALS['url_site'], '', $_SERVER['HTTP_REFERER']), '/'); }
		
		// On ajoute la date et la pub
		$infos = array_merge($infos, array('id_campagne' => $id_campagne, 'id_encart' => $campagne['id_encart'], 'page' => $page, 'date' => 'NOW()'));
		
		// On enregistre le clic
		$ok = sql_insertq(
			'spip_campagnes_clics',
			$infos
		);
		
		// Si c'est bon on redirige
		if ($ok !== false){
			include_spip('inc/headers');
			redirige_par_entete($campagne['url']);
		}
	}
}

/*
<BOUCLE_stats_clics(RECLAMES_CLICS campagnes){id_campagne}{date>=#ENV{date_debut,}}>
</BOUCLE_stats_clics>
*/

?>

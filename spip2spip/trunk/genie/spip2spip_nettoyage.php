<?php

/**
 * Gestion du génie spip2spip_nettoyage
 *
 * @plugin SPIP2SPIP
 * @license GPL
 * 
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/autoriser');
include_spip('action/editer_objet');
include_spip('action/editer_liens');
include_spip('inc/config');

/**
 * Nettoie les articles importés si trop vieux
 *
 * @genie spip2spip_nettoyage
 *
 * @param int $last
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif : la tâche a été effectuée
 */

function genie_spip2spip_nettoyage_dist($last){
	$ancienete = intval(lire_config('spip2spip/intervalle_nettoyage', 0));
	
	if ($ancienete > 0) {
		// Trouver les articles importés trop vieux
		if ($resultats = sql_select(
				'id_article,date,s2s_id_article_distant', 
				'spip_articles', 
				array(
					"DATEDIFF(CURDATE(), date) > $ancienete",
					"s2s_id_article_distant IS NOT NULL",
					"statut IN ('prop', 'redac')"
					)
				)
			) {
			// boucler sur les resultats
			while ($res = sql_fetch($resultats)) {
				$id_article = $res['id_article'];
				
				autoriser_exception('modifier','article',$id_article);
				objet_instituer('article',$id_article,array("statut"=>'poubelle'));
				autoriser_exception('modifier','article',$id_article,false);
			}
		}
	}

	return 1;
}

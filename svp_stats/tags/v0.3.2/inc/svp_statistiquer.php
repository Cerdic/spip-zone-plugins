<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// - Adresse de la page fournissant les statistiques par json
if (!defined('_SVP_SOURCE_STATS')) {
	define('_SVP_SOURCE_STATS', 'https://stats.spip.net/spip.php?page=stats.json');
}

// ----------------------- Traitements des stats ---------------------------------

/**
 * Actualisation des statistiques des plugins presents dans la base.
 * @return boolean
 */

function svp_actualiser_stats() {
	
	// Initialisation du retour
	$retour = true;

	// Récupération des statistiques globales (toute version SPIP) et mise en base dans la table spip_plugins
	include_spip('inc/distant');
	$page = recuperer_page(_SVP_SOURCE_STATS);
	$infos = json_decode($page);
	if (!$stats = $infos->plugins) {
		// On ne fait que loger l'erreur car celle-ci n'a pas d'incidence sur le comportement
		// de SVP
		spip_log('MODULE STATS - Réponse du serveur incorrecte ou mal formée. Les statistiques ne seront pas mises à jour', 'svp_actions.' . _LOG_ERREUR);
		$retour = false;
	} else {
		foreach ($stats as $_stat) {
			$prefixe = strtoupper($_stat->nom);
			if ($id_plugin = sql_getfetsel('id_plugin', 'spip_plugins', array('prefixe='. sql_quote($prefixe)))) {
				// Si le plugin est bien dans la base on peut lui mettre a jour ses statistiques
				sql_updateq('spip_plugins', 
							array('nbr_sites'=> $_stat->sites, 'popularite'=> floatval(trim($_stat->pourcentage, '%'))),
							'id_plugin=' . intval($id_plugin));
			}
		}
	}

	// Détermination de la date (mois année) pour l'historique.
	$date = date('m-y');

	// Récupération des statistiques par branche SPIP et mise en base dans la table spip_plugins_stats.
	// La liste des branches en cours est fournie par SVP dans un tableau global.
	include_spip('inc/svp_outiller');
	foreach (array_keys($GLOBALS['infos_branches_spip']) as $_branche) {
		// On charge le JSON des stats pour chaque branche SPIP
		$page = recuperer_page(_SVP_SOURCE_STATS . "&v=${_branche}");
		$infos = json_decode($page);
		if (!$stats = $infos->plugins) {
			// On ne fait que loger l'erreur car celle-ci n'a pas d'incidence sur le comportement
			// de SVP
			spip_log("MODULE STATS - Réponse du serveur incorrecte ou mal formée. Les statistiques de la branche ${_branche} ne seront pas mises à jour", 'svp_actions.' . _LOG_ERREUR);
			$retour = false;
		} else {
			foreach ($stats as $_stat) {
				$prefixe = strtoupper($_stat->nom);
				$where = array('prefixe='. sql_quote($prefixe), 'branche_spip=' . sql_quote($_branche));
				// Suivant que l'enregistrement du plugin pour la branche donnée existe ou pas, on met à jour
				// ou on insère les statistiques.
				$historique = array();
				if ($historique_existant = sql_getfetsel('historique', 'spip_plugins_stats', $where)) {
					// Les stats existent déjà, on les met à jour avec l'historique mensuel.
					$historique = unserialize($historique_existant);
					$historique[$date] = $_stat->sites;
					sql_updateq(
						'spip_plugins_stats', 
						array(
							'nbr_sites'  => $_stat->sites,
							'popularite' => floatval(trim($_stat->pourcentage, '%')),
							'historique' => serialize($historique)
						),
						$where
					);
				} else {
					// Les stats n'existent pas on les insèrent pour la première fois.
					$historique[$date] = $_stat->sites;
					sql_insertq(
						'spip_plugins_stats', 
						array(
							'prefixe'      => $prefixe,
							'branche_spip' => $_branche,
							'nbr_sites'    => $_stat->sites,
							'popularite'   => floatval(trim($_stat->pourcentage, '%')),
							'historique'   => serialize($historique)
						)
					);
				}
			}
		}
	}
	
	return $retour;
}

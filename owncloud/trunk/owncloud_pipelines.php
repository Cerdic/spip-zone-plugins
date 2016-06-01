<?php

/**
 * Pipeline pour Owncloud
 *
 * @plugin     Owncloud
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\Owncloud\pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function owncloud_affiche_gauche($flux) {
	return owncloud_boite_info($flux, 'affiche_gauche');
}
function monitor_affiche_droite($flux) {
	return owncloud_boite_info($flux, 'affiche_droite');
}

/**
 * Afficher le bouton de peuplage du fichier json
 * @param array $flux
 * @return array
 */
function owncloud_boite_info($flux, $pipeline) {
	include_spip('inc/presentation');

	$flux['args']['pipeline'] = $pipeline;

	if (trouver_objet_exec($flux['args']['exec'] == 'liste_owncloud')) {
		$texte = recuperer_fond('prive/squelettes/navigation/outils_owncloud');

		$flux['data'] .= $texte;
	}

	return $flux;
}

/**
 * Supprimer le md5 de la table spip_ownclouds
 * 
 * @param array $objets
 * @return array
 */
// function owncloud_trig_supprimer_objets_lies($objets) {
// 	spip_log($objets, 'test.' . _LOG_ERREUR);
// 	foreach ($objets as $objet) {
// 		if ($objet['type'] == 'documents') {
// 			sql_delete('spip_ownclouds', 'md5=' . intval($objet['md5']));
// 		}
// 	}

// 	return $objets;
// }

function owncloud_supprimer_md5($flux) {
	if ($flux['args']['exec'] == 'documents') {

		$valeurs = pipeline(
						'medias_post_insertion',
						array(
							'args' => $flux['args'],
							'data' => $valeurs
						),
						array()
		);

		spip_log($valeurs, 'test.' . _LOG_ERREUR);

	}
}
// /**
//  * Insertion dans le pipeline post_edition (SPIP)
//  *
//  * Lors du changement de statut vers "archive", on met la date dans le champs date_archive
//  *
//  * @param $flux array
//  *      Le contexte du pipeline
//  * @return $flux array
//  *      Le contexte du pipeline modifié
//  */
// function owncloud_post_edition($flux) {
//         if ($flux['args']['action'] == 'instituer'
//                 && $flux['args']['statut_ancien'] != 'archive'
//                 && $flux['args']['statut_nouveau'] == 'archive') {
//                 sql_updateq(
//                         $flux['args']['table'],
//                         array(
//                                 'archive_date' => date('Y-m-d H:i:s'),
//                                 'archive_statut' => $flux['args']['statut_ancien']
//                         ),
//                         id_table_objet($flux['args']['table']).'='.intval($flux['args']['id_objet'])
//                 );
//         }
//         return $flux;
// }


/**
 * Taches periodiques de syncro de owncloud 
 *
 * @param array $taches_generales
 * @return array
 */
function owncloud_taches_generales_cron($taches_generales) {
	include_spip('inc/config');
	$config = lire_config('owncloud');
	
	if ($config['activer_synchro'] == 'on') {
		$taches_generales['owncloud'] = 90;
	}

	return $taches_generales;
}

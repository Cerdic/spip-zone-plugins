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
 * @pipeline post_edition
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function owncloud_post_edition($flux) {
	if (isset($flux['args']['action'])
		and $flux['args']['action'] == 'supprimer_document'
		and $flux['args']['table'] == 'spip_documents'
		and $id_objet = intval($flux['args']['id_objet'])
	) {
		sql_delete('spip_ownclouds', 'md5=' . intval($flux['args']['document']['md5']));
	}

	return $flux;
}

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

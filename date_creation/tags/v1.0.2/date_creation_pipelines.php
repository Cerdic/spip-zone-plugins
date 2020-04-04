<?php
/**
 * Utilisations de pipelines par Date de création
 *
 * @plugin     Date de création
 * @copyright  2018
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Datecreation\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

/**
 * Enregistrer la date de creation lors de l'insertion d'un objet
 *
 * @param array $flux
 *
 * @return array
 */
function date_creation_pre_insertion($flux) {
	include_spip('date_creation_administrations');
	date_creation_creer_champs_date_creation();

	$tables = unserialize(lire_config('date_creation/objets'));
	if (is_array($tables) && in_array($flux['args']['table'], $tables)) {
		$flux['data']['date_creation'] = date('Y-m-d H:i:s');
	}

	return $flux;
}

/**
 * Afficher la date de creation sur la fiche d'un objet
 *
 * @param array $flux
 *
 * @return array
 */
function date_creation_afficher_contenu_objet($flux) {
	if(defined('_MASQUER_DATE_CREATION')){
		return $flux;
	}
	
	$tables = unserialize(lire_config('date_creation/objets'));
	$table  = table_objet_sql($flux['args']['type']);

	if(defined('_MASQUER_DATE_CREATION_'.strtoupper($flux['args']['type']))){
		return $flux;
	}
	
	if (is_array($tables)
		&& in_array($table, $tables)
		&& $id_objet = $flux['args']['id_objet']
	) {
		$id_table_objet = id_table_objet($flux['args']['type']);
		$date_creation  = sql_getfetsel('date_creation', $table, $id_table_objet . '=' . intval($id_objet));
		if(intval($date_creation)) {
			$date_creation = '<span class="affiche">' . affdate_heure($date_creation) . '</span>';
		} else {
			$date_creation = defined('_MASQUER_DATE_CREATION_NULLE') ? '' : _T('date_creation:non_renseignee');
		} 
		if($date_creation) {
			$flux['data'] = '<div class="date_creation"><strong>' . _T('date_creation:date_creation') . " :</strong> " . $date_creation . '</div>' . $flux['data'];
		}
	}

	return $flux;
}
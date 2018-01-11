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
function datecreation_pre_insertion($flux) {
	include_spip('datecreation_administrations');
	datecreation_creer_champs_date_creation();

	$tables = unserialize(lire_config('datecreation/objets'));
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
function datecreation_afficher_contenu_objet($flux) {

	$tables = unserialize(lire_config('datecreation/objets'));
	$table  = table_objet_sql($flux['args']['type']);

	if (is_array($tables)
		&& in_array($table, $tables)
		&& $id_objet = $flux['args']['id_objet']
	) {
		$id_table_objet = id_table_objet($flux['args']['type']);
		$date_creation  = sql_getfetsel('date_creation', $table, $id_table_objet . '=' . intval($id_objet));
		$date_creation  = intval($date_creation) ? affdate_heure($date_creation) : _T('datecreation:non_renseignee');
		$flux['data']   .= '<div><strong>' . propre(_T('datecreation:date_creation') . " :</strong> " . $date_creation) . '</div>';
	}

	return $flux;
}
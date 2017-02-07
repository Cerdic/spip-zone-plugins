<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

/**
 * Chargement des donnees du formulaire
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_xiti_niveau_objet_charger($type, $id) {
	$valeurs = array();
	$table_objet_sql = table_objet_sql($type);
	$id_table_objet = id_table_objet($type);
	$valeurs['objet'] = $type;
	$valeurs['id_objet'] = intval($id);

	$row = sql_fetsel('*', 'spip_xiti_niveaux_liens', 'id_objet='.intval($id).' AND objet='.sql_quote($type));
	$objet = objet_info($table_objet_sql, 'field');
	if (isset($objet['id_secteur'])
		or (isset($objet['id_rubrique']) and $type != 'rubrique')
		or isset($objet['id_parent'])) {
		if (isset($objet['id_rubrique'])) {
			if ($type == 'rubrique') {
				$info = 'id_parent';
			} else {
				$info = 'id_rubrique';
			}
			$valeurs['id_rubrique'] = sql_getfetsel($info, $table_objet_sql, $id_table_objet.'='.intval($id));
		} elseif (isset($objet['id_parent'])) {
			$valeurs['id_rubrique'] = sql_getfetsel('id_parent', $table_objet_sql, $id_table_objet.'='.intval($id));
		} elseif (isset($objet['id_secteur'])) {
			$valeurs['id_secteur'] = sql_getfetsel('id_secteur', $table_objet_sql, $id_table_objet.'='.intval($id));
		}
	}

	if (in_array($type, array('article', 'rubrique')) and lire_config('xiti/secteur_xiti', '') == 'oui') {
		$id_secteur = sql_getfetsel('id_secteur', $table_objet_sql, $id_table_objet.' = '.intval($id));
		$config_secteur = lire_config('xiti/xtsite_xiti_'.intval($id_secteur));
		if ($config_secteur and $config_secteur != lire_config('xiti/xtsite_xiti')) {
			$valeurs['xtsite'] = $config_secteur;
		} else {
			$valeurs['xtsite'] = lire_config('xiti/xtsite_xiti');
		}
	}
	$valeurs['id_xiti_niveau'] = $row['id_xiti_niveau'];

	return $valeurs;
}

/**
 * Traitement
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_xiti_niveau_objet_traiter($type, $id) {
	$res = array('editable' => ' ');
	$actuel = sql_getfetsel(
		'id_xiti_niveau',
		'spip_xiti_niveaux_liens',
		'id_objet='.intval($id).' AND objet='.sql_quote($type)
	);
	if (_request('id_xiti_niveau') != $actuel) {
		sql_delete('spip_xiti_niveaux_liens', 'id_objet='.intval($id).' AND objet='.sql_quote($type));
		sql_insertq(
			'spip_xiti_niveaux_liens',
			array('id_xiti_niveau' => _request('id_xiti_niveau'), 'id_objet' => intval($id), 'objet' => $type)
		);
		$res['message_ok'] = _T('xiti_niveau:message_niveau_maj');
	} elseif (!_request('id_xiti_niveau') or _request('id_xiti_niveau') == '') {
		sql_delete('spip_xiti_niveaux_liens', 'id_objet='.intval($id).' AND objet='.sql_quote($type));
		$res['message_ok'] = _T('xiti_niveau:message_niveau_supprime');
	}
	return $res;
}

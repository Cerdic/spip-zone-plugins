<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function noizetier_type_noisette_stocker($plugin, $types_noisettes, $recharger) {

	$retour = true;

	// Mise à jour de la table des noisettes 'spip_noizetier_noisettes'.
	$from = 'spip_noizetier_noisettes';

	// -- Suppression des noisettes obsolètes ou de toute les noisettes d'un coup si on est en mode
	//    rechargement forcé.
	if (sql_preferer_transaction()) {
		sql_demarrer_transaction();
	}
	$where = array('plugin=' . sql_quote($plugin));
	if ($recharger) {
		sql_delete($from, $where);
	} elseif (!empty($types_noisettes['a_effacer'])) {
		$where[] = sql_in('noisette', $types_noisettes['a_effacer']);
		sql_delete($from, $where);
	}
	// -- Update des pages modifiées
	if (!empty($types_noisettes['a_changer'])) {
		// Ajouter le type et la composition compatible pour chaque type de noisette
		foreach ($types_noisettes['a_changer'] as $_cle => $_description) {
			$types_noisettes['a_changer'][$_cle] = noizetier_type_noisette_completer($_description);
		}
		sql_replace_multi($from, $types_noisettes['a_changer']);
	}
	// -- Insertion des nouvelles pages
	if (!empty($types_noisettes['a_ajouter'])) {
		// Ajouter le type et la composition compatible pour chaque type de noisette
		foreach ($types_noisettes['a_ajouter'] as $_cle => $_description) {
			$types_noisettes['a_ajouter'][$_cle] = noizetier_type_noisette_completer($_description);
		}
		sql_insertq_multi($from, $types_noisettes['a_ajouter']);
	}
	if (sql_preferer_transaction()) {
		sql_terminer_transaction();
	}

	return $retour;
}

function noizetier_type_noisette_completer($description) {

	// Initialiser les composants de l'identifiant du type de noisette:
	// - type_page-type_noisette si le type de noisette est dédié uniquement à une page
	// - type_page-composition-type_noisette si le type de noisette est dédié uniquement à une composition
	// - type_noisette sinon
	$description['type'] = '';
	$description['composition'] = '';
	$identifiants = explode('-', $description['noisette']);
	if (isset($identifiants[1])) {
		$description['type'] = $identifiants[0];
	}
	if (isset($identifiants[2])) {
		$description['composition'] = $identifiants[1];
	}

	return $description;
}
function noizetier_type_noisette_decrire($plugin, $noisette) {

	// Chargement de toute la configuration de la noisette en base de données.
	// Les données sont renvoyées brutes sans traitement sur les textes ni les tableaux sérialisés.
	$where = array('plugin=' . sql_quote($plugin), 'noisette=' . sql_quote($noisette));
	$description = sql_fetsel('*', 'spip_noizetier_noisettes', $where);

	return $description;
}

function noizetier_type_noisette_lister($plugin, $information = '') {

	$where = array('plugin=' . sql_quote($plugin));
	$select = $information ? array('noisette', $information) : '*';
	if ($info_noisettes = sql_allfetsel($select, 'spip_noizetier_noisettes', $where)) {
		if ($information) {
			$info_noisettes = array_column($info_noisettes, $information, 'noisette');
		} else {
			$info_noisettes = array_column($info_noisettes, null, 'noisette');
		}
	}

	return $info_noisettes;
}

function noizetier_noisette_lister($plugin, $squelette = '', $information = '') {

	$where = array('plugin=' . sql_quote($plugin));
	if ($squelette) {
		$where[] = 'squelette=' . sql_quote($squelette);
	}
	$select = $information ? array_merge(array('squelette', 'rang', 'id_noisette'), array($information)) : '*';

	if ($noisettes = sql_allfetsel($select, 'spip_noizetier', $where)) {
		$noisettes = $information
			? array_column($noisettes, $information, 'id_noisette')
			: array_column($noisettes, null, 'id_noisette');
	}

	return $noisettes;
}

function noizetier_noisette_stocker($plugin, $action, $description) {

	$id_noisette = 0;

	// Mise à jour en base de données.
	if ($action == 'creation') {
		// Compléter la description fournie avec les champs propres au noizetier, à savoir, ceux identifiant
		// la page/composition ou l'objet et le bloc.
		// On parse le squelette pour identifier les données manquantes.
		$complement = squelette_phraser($description['squelette']);
		$description = array_merge($description, $complement);

		if ($id_noisette = sql_insertq('spip_noizetier', $description)) {
			// On invalide le cache
			include_spip('inc/invalideur');
			suivre_invalideur("id='noisette/$id_noisette'");
		}
	} elseif ($action == 'modification') {
		// On sauvegarde l'id de la noisette et on le retire de la description pour éviter une erreur à l'update.
		$id_noisette = intval($description['id_noisette']);
		unset($description['id_noisette']);

		// Mise à jour de la noisette
		$where = array('id_noisette=' . $id_noisette);
		if (!sql_updateq('spip_noizetier', $description, $where)) {
			$id_noisette = 0;
		}
	}

	return $id_noisette;
}

function noizetier_noisette_config_ajax() {

	// On détermine la valeur par défaut de l'ajax des noisettes qui est stocké dans la configuration du plugin.
	include_spip('inc/config');
	$defaut_ajax = lire_config('noizetier/ajax_noisette') == 'on' ? true : false;

	return $defaut_ajax;
}

function squelette_phraser($squelette) {

	$complement = array(
		'type'        => '',
		'composition' => '',
		'objet'       => '',
		'id_objet'    => 0,
		'bloc'        => ''
	);


	if ($squelette) {
		$squelette = strtolower($squelette);
		$page = basename($squelette);
		$identifiants_page = explode('-', $page, 2);
		if (!empty($identifiants_page[1])) {
			// Forcément une composition
			$complement['type'] = $identifiants_page[0];
			$complement['composition'] = $identifiants_page[1];
		} else {
			// Page ou objet
			if (preg_match(',([a-z_]+)(\d+)$,s', $identifiants_page[0], $identifiants_objet)) {
				$complement['objet'] = $identifiants_objet[1];
				$complement['id_objet'] = $identifiants_objet[2];
			} else {
				$complement['type'] = $identifiants_page[0];
			}
		}

		$bloc = dirname($squelette);
		if ($bloc != '.') {
			$complement['bloc'] = basename($bloc);
		}
	}

	return $complement;
}
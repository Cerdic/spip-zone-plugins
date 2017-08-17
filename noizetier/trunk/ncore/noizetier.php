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
		sql_replace_multi($from, $types_noisettes['a_changer']);
	}
	// -- Insertion des nouvelles pages
	if (!empty($types_noisettes['a_ajouter'])) {
		sql_insertq_multi($from, $types_noisettes['a_ajouter']);
	}
	if (sql_preferer_transaction()) {
		sql_terminer_transaction();
	}

	return $retour;
}

function noizetier_type_noisette_decrire($plugin, $noisette) {

	// Chargement de toute la configuration de la noisette en base de données.
	// Les données sont renvoyées brutes sans traitement sur les textes ni les tableaux sérialisés.
	$where = array('plugin=' . sql_quote($plugin), 'noisette=' . sql_quote($noisette));
	$description = sql_fetsel('*', 'spip_noizetier_noisettes', $where);

	return $description;
}

function noizetier_type_noisette_lister($plugin, $information) {

	// Initialisation du tableau de sortie
	$info_noisettes = array();

	if ($information) {
		$select = array('noisette', $information);
		$where = array('plugin=' . sql_quote($plugin));
		if ($info_noisettes = sql_allfetsel($select, 'spip_noizetier_noisettes', $where)) {
			$info_noisettes = array_column($info_noisettes, $information, 'noisette');
		}
	}

	return $info_noisettes;
}



function noizetier_noisette_config_ajax() {

	// On détermine la valeur par défaut de l'ajax des noisettes qui est stocké dans la configuration du plugin.
	include_spip('inc/config');
	$defaut_ajax = lire_config('noizetier/ajax_noisette') == 'on' ? true : false;

	return $defaut_ajax;
}

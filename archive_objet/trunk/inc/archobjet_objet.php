<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Renvoie les informations d'archivage d'un objet connu par son id.
 *
 * @param string $objet    Type d'objet comme article
 * @param int    $id_objet Valeur du champ identifiant
 * @param array  $options  Permet de fournir le nom de la table et du champ id afin d'éviter des appels aux
 *                         fonctions objet.
 *
 * @return array Le tableau des informations d'archivage éventuellement vide.
 */
function objet_etat_archivage($objet, $id_objet, $options = array()) {

	// Initialisation du tableau des objets archivés.
	// Les tableaux sont toujours indexés par l'objet et l'id objet.
	static $est_archive = array();

	// Si l'objet n'a pas encore été stocké, il faut acquérir les champs d'archivage uniquement.
	if (!isset($est_archive[$objet][$id_objet])) {
		// Détermination de la table SQL.
		include_spip('base/objets');
		if (!isset($options['table'])) {
			$options['table'] = table_objet_sql($objet);
		}

		// Détermination du champ id
		if (!isset($options['champ_id'])) {
			$options['champ_id'] = id_table_objet($objet);
		}

		// Détermination des champs à récupérer et de la condition sur l'objet
		$select = array('est_archive', 'date_archive', 'raison_archive');
		$where = array(
			$options['champ_id'] . '=' . intval($id_objet)
		);

		if (!$archivage = sql_fetsel($select, $options['table'], $where)) {
			$archivage = array();
		} else {
			// On rajoute un identifiant littéral pour l'état d'archivage
			$archivage['etat'] = $archivage['est_archive'] ? 'archive' : 'desarchive';
		}

		// Mise en cache des données d'archivage de l'objet.
		$est_archive[$objet][$id_objet] = $archivage;
	}


	return $est_archive[$objet][$id_objet];
}

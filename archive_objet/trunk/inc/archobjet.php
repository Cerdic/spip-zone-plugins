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
function archivage_lire_etat_objet($objet, $id_objet, $options = array()) {

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

/**
 * Renvoie la liste des raisons d'archivage ou de désarchivage pour un type d'objet donné.
 *
 * @param string $objet Type d'objet comme article
 * @param int    $etat  Valeur de l'état d'archivage
 *
 * @return array Le tableau des identifiants de raison d'archivage ou de désarchivage
 */
function archivage_lister_raisons($objet, $etat) {

	// Construction de la liste des raisons
	$raisons = array();

	// -- Initialisation de la liste par des raisons standard valables pour tous les types d'objets
	//    et pour l'état courant de l'objet. Ces raisons sont fournies par le plugin Archive.
	$ids_raisons = array(
		"${etat}_aucune",
		"${etat}_defaut"
	);

	// -- Ajout des raisons additionnelles fournies par d'autres plugins pour le type d'objet et l'état
	//    d'archivage en question.
	$ids_raisons = pipeline(
		'liste_raisons_archivage',
		array(
			'args' => array(
				'objet' => $objet,
				'etat'  => $etat
			),
			'data' => $ids_raisons,
		)
	);

	// -- Calcul du tableau des raisons pour la saisie
	foreach ($ids_raisons as $_id_raison) {
		// La valeur aucune raison est en fait la chaine vide
		if ($_id_raison == "${etat}_aucune") {
			$raisons[''] = _T("archobjet:raison_${_id_raison}_label");
		} else {
			$raisons[$_id_raison] = _T("archobjet:raison_${_id_raison}_label");
		}
	}

	return $raisons;
}

/**
 * Renvoie la liste des raisons d'archivage ou de désarchivage pour un type d'objet donné.
 *
 * @param string $objet Type d'objet comme article
 * @param int    $etat  Valeur de l'état d'archivage
 *
 * @return array Le tableau des identifiants de raison d'archivage ou de désarchivage
 */
function archivage_lister_tables_objet() {

	// Initialisation de la liste des types d'objet archivable :
	// -- par défaut, les objets articles et auteurs sont archivables car fournis par SPIP sans plugins. Les rubriques
	//    ne sont pas incluses pour l'instant (mécanisme à définir).
	$tables = array('spip_articles', 'spip_auteurs');

	// Ajout des types d'objet additionnels fournis par d'autres plugins et pouvant être archivés.
	$tables = pipeline(
		'liste_types_objet_archivables',
		array(
			'args' => array(),
			'data' => $tables,
		)
	);

	return $tables;
}

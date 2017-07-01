<?php

/**
 * Retrourne un json de resultats d'une requete jquer autocmplete.
 * @param string $table
 *    le nom de l table
 * @return string Json objet contenat label et data.
 */
function la_recherche_objets($table) {

	// Le données de l'objet.
	$champ_id_table = id_table_objet($table);
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table);

	// Chercher le champ du titre, il y surement mieux.
	$titre = explode(',', $desc['titre']);
	$titre = explode('AS', $titre[0]);
	$champ_titre = trim($titre[0]);

	// LA requête
	$champs = array($champ_id_table,$champ_titre);
	$where = array(
		'statut LIKE "publie"',
		$champ_titre . ' LIKE ' . sql_quote('%' . _request('term') . '%')
	);

	// La requête.
	$sql = sql_select($champs, $table, $where);
	$objets = array();
	while ($data = sql_fetch($sql)) {
		$objets[] = array(
			'label' => $data[$champ_titre],
			'data' => $data[$champ_id_table],
			);
	}

	return json_encode($objets);
}

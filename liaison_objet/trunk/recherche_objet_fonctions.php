<?php

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/*Fournit un tableau avec id_objet=>donnees_objet*/

function tableau_recherche_objet($objet, $exclus, $lang = '') {
	include_spip('inc/pipelines_ecrire');
	//Les tables non conforme, faudrait inclure une pipeline
	$exceptions = charger_fonction('exceptions', 'inc');
	$exception_objet = $exceptions();
	if (!$champ_titre = $exception_objet['titre'][$objet])
		$champ_titre = 'titre';

	$ancien_objet = $objet;
	$e = trouver_objet_exec($objet);
	$objet = $e['type'];
	$id_table_objet = $e['id_table_objet'];
	if (!$objet) {
		$objet = $ancien_objet;
		$id_table_objet = 'id_' . $objet;
	}
	$table = table_objet_sql($objet);

	$tables = lister_tables_objets_sql();

	$traduction_nom_objet = _T($tables[$table]['texte_objet']);

	$where = array($champ_titre . ' LIKE ' . sql_quote('%' . _request('term') . '%'));
	if ($objet == 'document') {
		$where = array($champ_titre . ' LIKE ' . sql_quote('%' . _request('term') . '%') . ' OR fichier LIKE' . sql_quote('%' . _request('term') . '%'));
		$champ_titre = 'titre,fichier';
	}

	if (isset($tables[$table_dest]['statut'][0]['publie']))
		$statut = $tables[$table_dest]['statut'][0]['publie'];
	$exceptions_statut = array(
		'rubrique',
		'document'
	);
	if ($statut AND !in_array($objet, $exceptions_statut))
		$where[] = 'statut=' . sql_quote($statut);
	if ($objet == 'auteur')
		$where[] = 'statut !=' . sql_quote('5poubelle');
	if (isset($tables[$table_dest]['field']['lang']) AND $lang)
		$where[] = 'lang IN ("' . implode('","', $lang) . '")';
	$d = info_objet($objet, '', $champ_titre . ',' . $id_table_objet, $where);

	$data = array();
	if (is_array($d)) {
		foreach ($d as $r) {
			if (!$r['titre'])
				$r['titre'] = titre_objet_sel($objet, $r);
			if (!isset($exclus[$r[$id_table_objet] . '-' . $objet]))
				$data[] = array(
					'label' => $r[titre] . ' (' . $traduction_nom_objet . ')',
					'value' => $r[$id_table_objet] . '-' . $objet
				);
		}
	}
	return $data;
}
?>

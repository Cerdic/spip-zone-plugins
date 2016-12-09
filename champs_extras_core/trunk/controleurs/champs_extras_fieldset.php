<?php

/**
 * Controleur de Crayons pour les fieldset de champs extras déclarés.
 *
 * @param array $regs
 * @param array|null $c
 * @return array Liste html, erreur
 */
function controleurs_champs_extras_fieldset_dist($regs, $c = null) {
	list(,$crayon, $type, $champ, $id) = $regs;

	$table = table_objet_sql($type);
	$saisies = champs_extras_objet($table);

	// Restreindre aux fieldset
	$saisies = saisies_lister_avec_type($saisies, 'fieldset');
	$saisies = array_intersect_key($saisies, array($champ => ''));
	// Trouver les champs sql, si le fieldset est bien un champs extras
	$saisies_sql = champs_extras_saisies_lister_avec_sql($saisies);
	$champs = array_keys(saisies_lister_par_nom($saisies_sql));

	// Valeur actuelle du champ (tableau champ => valeur)
	$valeurs = valeur_colonne_table($type, $champs, $id);

	if (!$saisies OR $valeurs == false) {
		return array("cextras > $type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
	}

	// Éviter que le style CSS du label du fieldset soit utilisé pour les champs
	foreach (array(
		'color',
		'font-size',
		'font-family',
		'font-weight',
	    'line-height',
		'min-height',
		'text-align',
		'background-color'
	) as $property) {
		set_request($property, null);
	}

	$n = new Crayon(
		$type . '-champs_extras_fieldset-' . $id,
		$valeurs,
		array('controleur' => 'controleurs/champs_extras')
	);

	// Crayons utilise son propre formalisme pour le 'name' des saisies.
	include_spip('controleurs/champs_extras');
	$contexte = cextra_preparer_contexte_crayons($saisies, $valeurs, $n->key);

	$html = $n->formulaire($contexte);
	$status = $scripts = null;

	// probablement pas la meilleure idée du siècle…
	// mais tenter d'afficher correctement le picker de date du plugin saisies dans l'espace public
	if (!test_espace_prive() and saisies_lister_avec_type($saisies_sql, 'date')) {
		$scripts = '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/ui/jquery-ui.css') . '" />';
	}

	return array($html . $scripts, $status);
}
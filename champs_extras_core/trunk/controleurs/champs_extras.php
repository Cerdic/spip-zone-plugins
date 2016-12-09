<?php

/**
 * Controleur de Crayons pour les champs extras déclarés.
 *
 * @param array $regs
 * @param array|null $c
 * @return array Liste html, erreur
 */
function controleurs_champs_extras_dist($regs, $c = null) {
	list(,$crayon, $type, $champ, $id) = $regs;

	$table = table_objet_sql($type);
	$saisies = champs_extras_objet($table);
	// Restreindre aux vrais champs en bdd
	$saisies = champs_extras_saisies_lister_avec_sql($saisies);
	// Trouver notre saisie, si le champ est bien un champs extras
	$saisies = array_intersect_key($saisies, array($champ => ''));

	// Valeur actuelle du champ (tableau champ => valeur)
	$valeurs = valeur_colonne_table($type, $champ, $id);

	if (!$saisies OR $valeurs == false) {
		return array("cextras > $type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
	}

	$n = new Crayon(
		$type . '-champs_extras-' . $id,
		$valeurs,
		array('controleur' => 'controleurs/champs_extras')
	);

	// Crayons utilise son propre formalisme pour le 'name' des saisies.
	$contexte = cextra_preparer_contexte_crayons($saisies, $valeurs, $n->key);

	$html = $n->formulaire($contexte);
	$status = $scripts = null;

	// probablement pas la meilleure idée du siècle…
	// mais tenter d'afficher correctement le picker de date du plugin saisies dans l'espace public
	if (!test_espace_prive() and saisies_lister_avec_type($saisies, 'date')) {
		$scripts = '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/ui/jquery-ui.css') . '" />';
	}

	return array($html . $scripts, $status);
}

/**
 * Prépare le tableau de saisies pour générer le formulaire spécifique à Crayons
 *
 * Enlève certaines informations de la saisie pour simplifier l'édition rapide
 * avec crayons (pas de label, explication, ...)
 *
 * @param array $saisies
 * @param array $valeurs
 * @param string key
 * @return array Contexte pour le controleur champs extras du formulaire de crayons
 */
function cextra_preparer_contexte_crayons($saisies, $valeurs, $key) {
	// Si on n'édite qu'un seul champ (le plus probable actuellement)
	// on enlève certaines infos qui ne sont pas absolument utiles
	// pour une édition rapide
	$nettoyer = count($valeurs) == 1;

	$contexte = array(
		'_saisies' => array(),
		'erreurs' => array(),
	);

	foreach ($valeurs as $champ => $valeur) {
		$s = $saisies[$champ];
		$nom = 'content_' . $key . '_' . $champ;
		$s['options']['nom'] = $nom;
		if ($nettoyer) {
			unset($s['options']['label']);
			unset($s['options']['explication']);
			unset($s['options']['attention']);
		}
		$contexte['_saisies'][$nom] = $s;
		$contexte[$nom] = $valeur;
	}

	return $contexte;
}
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
 * avec crayons (pas de label, explication, ...).
 *
 * On prend en compte les champs de type fieldset.
 *
 * @param array $saisies
 * @param array $valeurs
 * @param string key
 * @return array Contexte pour le controleur champs extras du formulaire de crayons
 */
function cextra_preparer_contexte_crayons($saisies, $valeurs, $key, $profondeur = 0) {
	// Si on n'édite qu'un seul champ (le plus probable actuellement)
	// on enlève certaines infos qui ne sont pas absolument utiles
	// pour une édition rapide
	$nettoyer = count($valeurs) == 1;
	$contexte = array(
		'_saisies' => array(),
		'erreurs' => array(),
	);

	foreach ($saisies as $saisie) {
		$nom = $saisie['options']['nom'];
		$nom_crayons = 'content_' . $key . '_' . $nom;
		// changer le nom de la saisie (si elle fait partie des valeurs modifiées)
		// pour s'accorder avec ce qu'attends crayons.
		if (!empty($saisie['options']['sql']) and isset($valeurs[$nom])) {
			$saisie['options']['nom'] = $nom_crayons;
			$contexte[$nom_crayons] = $valeurs[$nom];
		}
		if ($nettoyer) {
			if ($saisie['saisie'] != 'fieldset') {
				unset($saisie['options']['label']);
			}
			unset($saisie['options']['explication']);
			unset($saisie['options']['attention']);
		}

		if (!empty($saisie['saisies'])) {
			$c = cextra_preparer_contexte_crayons($saisie['saisies'], $valeurs, $key, $profondeur+1);
			$saisie['saisies'] = $c['_saisies'];
			unset($c['_saisies'], $c['erreur']);
			$contexte += $c;
		}
		$contexte['_saisies'][$saisie['options']['nom']] = $saisie;
	}

	return $contexte;
}
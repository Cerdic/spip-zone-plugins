<?php

/**
 * Retourner le code HTML de la vue d'un fieldset de champs extras pour Crayons
 *
 * @note
 *     On perd l'information du nom de notre fieldset. On ne reçoit que le nom
 *     des champs qui ont été postés et qui correspondent à des champs dans la
 *     base de données.
 *
 * @uses champs_extras_objet()
 * @uses champs_extras_saisies_lister_avec_sql()
 * @uses cextras_appliquer_traitements_saisies()
 * @uses cextras_preparer_vue()
 *
 * @param string $type
 *     Type d'objet
 * @param string $modele
 *     Nom du modèle donné par le contrôleur
 * @param int $id
 *     Identifiant de l'objet
 * @param array $content
 *     Couples champs / valeurs postés.
 * @param $wid
 *     Identifiant du formulaire
 */
function vues_champs_extras_fieldset_dist($type, $modele, $id, $content, $wid) {
	include_spip('cextras_pipelines');
	include_spip('vues/champs_extras');

	$table = table_objet_sql($type);
	// Récupérer les saisies SQL de la table
	$saisies = champs_extras_objet($table);
	$saisies_sql = champs_extras_saisies_lister_avec_sql($saisies);
	// Ne conserver que les champs concernés ici
	$saisies_sql = array_intersect_key($saisies_sql, $content);

	$valeurs = cextras_appliquer_traitements_saisies($saisies_sql, $content);

	// On cherche le fieldset qui englobe tous les champs transmis.
	$saisies = cextras_saisies_retrouver_fieldset($saisies, array_keys($content));

	// Réduire l'affichage au minimum s'il n'y a qu'un champ à afficher
	$saisies = cextras_preparer_vue($saisies, count($valeurs) <= 1);

	$contexte = array(
		'saisies' => $saisies,
		'valeurs' => $valeurs,
	);

	return recuperer_fond('inclure/voir_saisies', $contexte);
}

/**
 * Retrouver la saisie fieldset qui contient les champs indiqués.
 *
 * @param array $saisies
 * @param array $noms
 * @return array
 */
function cextras_saisies_retrouver_fieldset($saisies, $noms) {
	// on stocke le nom des parents dans chaque saisie enfant
	$saisies = cextras_saisies_indiquer_parents($saisies);
	$_saisies = saisies_lister_par_nom($saisies);
	// tous les parents possibles
	$parents = array_keys($_saisies);
	// réduire à l'intersection des parents de chaque saisie
	$_saisies = array_intersect_key($_saisies, array_flip($noms));
	foreach ($_saisies as $s) {
		$parents = array_intersect($s['_parents'], $parents);
	}

	// théoriquement, le plus proche parent est le premier de la liste
	$parent = reset($parents);
	$saisie = saisies_chercher($saisies, $parent);
	return array($saisie);
}

/**
 * Enregistrer le nom de la saisie parente pour les enfants des fieldset
 * dans chaque enfant, dans la clé `_parent`
 *
 * @param array $saisies
 * @return array
 */
function cextras_saisies_indiquer_parents($saisies) {
	foreach ($saisies as $k => $saisie) {
		if (!isset($saisie['_parents'])) {
			$saisie['_parents'] = array();
		}
		if (!empty($saisie['saisies'])) {
			foreach ($saisie['saisies'] as $c => $s) {
				if (!isset($saisie['saisies'][$c]['_parents'])) {
					$saisie['saisies'][$c]['_parents'] = array();
				}
				$saisie['saisies'][$c]['_parents'][] = $saisie['options']['nom'];
				if (count($saisie['_parents'])) {
					$saisie['saisies'][$c]['_parents'] += $saisie['_parents'];
				}
			}
			$saisie['saisies'] = cextras_saisies_indiquer_parents($saisie['saisies']);
		}
		$saisies[$k] = $saisie;
	}
	return $saisies;
}
<?php

/**
 * Retourner le code HTML de la vue d'un champ (ou plusieurs) champs extras pour Crayons
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
function vues_champs_extras_dist($type, $modele, $id, $content, $wid) {
	include_spip('cextras_pipelines');

	$table = table_objet_sql($type);
	// Récupérer les saisies SQL de la table
	$saisies = champs_extras_objet($table);
	$saisies = champs_extras_saisies_lister_avec_sql($saisies);
	// Ne conserver que les champs concernés ici
	$saisies = array_intersect_key($saisies, $content);

	$valeurs = cextras_appliquer_traitements_saisies($saisies, $content);

	// Réduire l'affichage au minimum s'il n'y a qu'un champ à afficher
	$saisies = cextras_preparer_vue($saisies, count($valeurs) <= 1);

	$contexte = array(
		'saisies' => $saisies,
		'valeurs' => $valeurs,
	);

	return recuperer_fond('inclure/voir_saisies', $contexte);
}

/**
 * Préparer le tableau de saisie pour l'affichage
 *
 * @param array $saisies
 * @param bool $affichage_reduit
 * @return array
 */
function cextras_preparer_vue($saisies, $affichage_reduit = false){
	foreach ($saisies as $cle => $saisie) {
		$saisies[$cle]['options']['valeur_uniquement'] = ($affichage_reduit ? 'oui' : '');
		$saisies[$cle]['options']['sans_reponse'] = '';
		if (!empty($saisie['saisies'])) {
			$saisies[$cle]['saisies'] = cextras_preparer_vue($saisie['saisies'], $affichage_reduit);
		}
	}
	return $saisies;
}
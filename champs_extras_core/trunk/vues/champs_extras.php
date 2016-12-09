<?php

/**
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
	foreach ($saisies as $champ => $saisie) {
		$saisies[$champ]['options']['valeur_uniquement'] = (count($valeurs) <= 1 ? 'oui' : '');
		$saisies[$champ]['options']['sans_reponse'] = '';
	}

	$contexte = array(
		'saisies' => $saisies,
		'valeurs' => $valeurs,
	);

	return recuperer_fond('inclure/voir_saisies', $contexte);
}

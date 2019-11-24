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
 * @return void
 */
function inc_generer_where_est_archive_dist($idb, $boucles, $critere) {

	// Initialisation de la table sur laquelle porte le critère
	include_spip('base/objets');
	$boucle = &$boucles[$idb];
	$table = table_objet_sql($boucle->id_table);

	// On calcule un critère infixe à partir du critère conditionnel {est_archive?} et on récupère chacun
	// des éléments de la condition.
	// - l'opérateur est toujours '='
	// - l'opérande est 'est_article' préfixé par le type d'objet
	// - le code PHP assurant la comparaison par rapport à la variable est_article est toujours unique et simple
	//   à savoir la vriable elle-même
	// - la colonne correspond à 'est_article'.
	list($operande, $operateur, $code_variable, $colonne) = calculer_critere_infixe($idb, $boucles, $critere);

	// Construction du where est_archive=#ENV{est_archive} où la variable est égale à 0 ou 1
	$where = array("'$operateur'", "'$operande'", $code_variable[0]);

	// Maintenant il faut construire les cas d'exclusion de la condition précedente en insérant le défaut
	// est_archive=1 si aucune variable n'est trouvée dans l'environnement.
	$pred = calculer_argument_precedent($idb, $colonne, $boucles);
	$where = array(
		"'?'",
		"(is_array($pred))",
		critere_IN_cas($idb, $boucles, 'COND', $operande, $operateur, array($pred), $colonne),
		$where
	);
	$where = array("'?'", "!(is_array($pred)?count($pred):strlen($pred))", array("'='", "'est_archive'", 0), $where);

	// On ajoute le where calculé à la boucle en cours et on indique dans les modificateurs la présence d'un tel where.
	$boucles[$idb]->where[] = $where;
	$boucles[$idb]->modificateur['est_article'] = true;
}

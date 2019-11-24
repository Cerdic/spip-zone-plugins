<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Définition du critère {archive} et {!archive} plus pratique à utiliser que {est_archive=1} ou
 * {est_archive=0}.
 *
 */
function critere_archive_dist($idb, &$boucles, $critere) {

	// Initialisation de la table sur laquelle porte le critère
	include_spip('base/objets');
	$boucle = &$boucles[$idb];
	$table = table_objet_sql($boucle->id_table);

	// Vérifier que la table fait bien partie de la liste autorisée à utiliser l'archivage.
	include_spip('inc/config');
	$tables_autorisees = lire_config('archobjet/objets_archivables', array());
	if (in_array($table, $tables_autorisees)) {
		// Définition de la valeur du champ est_archive en fonction de l'existence du not ou pas
		$valeur = ($critere->not == '!') ? 0 : 1;
		$champ = 'est_archive';

		// Création du critère sur le champ 'est_archive'.
		$boucle->where[] = array("'='", "'$champ'", $valeur);
	}
}

/**
 * Compile la balise `#TYPE_OBJET_ARCHIVE` qui renvoie la liste des types d'objet autorisés à l'archivage
 * Chaque type d'objet est fourni avec son titre.
 * La signature de la balise est : `#TYPE_OBJET_ARCHIVE`.
 *
 * @balise
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_TYPE_OBJET_ARCHIVE_dist($p) {

	// Aucun argument à la balise.
	$p->code = "calculer_types_objet_archives()";

	return $p;
}

/**
 * @internal
 *
 * @return array
 */
function calculer_types_objet_archives() {

	// Liste des tables autorisées à l'archivage
	include_spip('inc/config');
	$tables_autorisees = lire_config('archobjet/objets_archivables', array());

	// Construction de la liste des types d'objets archivables
	include_spip('base/objets');
	$types_archives = array();
	foreach ($tables_autorisees as $_table) {
		if ($_table) {
			$type_objet = objet_type($_table);
			$types_archives[$type_objet] = table_objet($type_objet);
		}
	}

	return $types_archives;
}

/**
 * Compile la balise `#OBJET_ETAT_ARCHIVAGE` qui renvoie la liste des types d'objet autorisés à l'archivage
 * Chaque type d'objet est fourni avec son titre.
 * La signature de la balise est : `#TYPE_OBJET_ARCHIVE`.
 *
 * @balise
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_OBJET_ETAT_ARCHIVAGE_dist($p) {

	// Récupération des arguments de la balise.
	// -- seul l'argument information est optionnel.
	$objet = interprete_argument_balise(1, $p);
	$objet = str_replace('\'', '"', $objet);
	$id_objet = interprete_argument_balise(2, $p);
	$id_objet = isset($id_objet) ? $id_objet : '0';
	$information = interprete_argument_balise(3, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';

	// Calcul de la balise
	$p->code = "calculer_etat_archivage($objet, $id_objet, $information)";

	return $p;
}

/**
 * @internal
 *
 * @return array
 */
function calculer_etat_archivage($objet, $id_objet, $information) {

	// Tableau de l'archivage de l'objet
	include_spip('inc/archobjet_objet');
	$etat_archivage = objet_etat_archivage(
		$objet,
		$id_objet
	);

	return $information ? $etat_archivage[$information] : $etat_archivage;
}

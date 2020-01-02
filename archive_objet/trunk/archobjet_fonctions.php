<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Définition du critère {archive} et {!archive} plus pratique à utiliser que {est_archive=1} ou
 * {est_archive=0}.
 *
 * @param mixed $idb
 * @param mixed $boucles
 * @param mixed $critere
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
		$boucle->where[] = array("'='", "'${champ}'", $valeur);
	}
}

/**
 * Compile la balise `#TYPE_OBJET_AVEC_ARCHIVE` qui renvoie la liste des types d'objet autorisés à l'archivage
 * Chaque type d'objet est fourni avec son titre.
 * La signature de la balise est : `#TYPE_OBJET_AVEC_ARCHIVE`.
 *
 * @balise
 *
 * @param Champ $p
 *                 Pile au niveau de la balise.
 *
 * @return Champ
 *               Pile complétée par le code à générer.
 **/
function balise_TYPE_OBJET_AVEC_ARCHIVE_dist($p) {

	// Aucun argument à la balise.
	$p->code = 'calculer_type_objet_avec_archive()';

	return $p;
}

/**
 * @internal
 *
 * @return array
 */
function calculer_type_objet_avec_archive() {

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
 * Compile la balise `#TYPE_OBJET_AVEC_ARCHIVE` qui renvoie la liste des types d'objet autorisés à l'archivage
 * Chaque type d'objet est fourni avec son titre.
 * La signature de la balise est : `#TYPE_OBJET_AVEC_ARCHIVE`.
 *
 * @balise
 *
 * @param Champ $p
 *                 Pile au niveau de la balise.
 *
 * @return Champ
 *               Pile complétée par le code à générer.
 **/
function balise_TABLE_OBJET_ARCHIVABLE_dist($p) {

	// Aucun argument à la balise.
	$p->code = 'calculer_table_objet_archivable()';

	return $p;
}

/**
 * @internal
 *
 * @return array
 */
function calculer_table_objet_archivable() {

	// Liste des tables potentiellement archivable
	include_spip('inc/archobjet');
	$tables = archivage_lister_tables_objet();

	// On rajoute les traductions
	// -- on initialise le tableau [table] = traduction
	$tables_traduites = array();

	// -- on acquiert les déclarations de toutes les tables d'objet SQL.
	include_spip('base/objets');
	$descriptions = lister_tables_objets_sql();

	// On boucle sur chaque table archivable pour insérer la traduction
	foreach ($tables as $_table) {
		$tables_traduites[$_table] = _T($descriptions[$_table]['texte_objets']);
	}

	return $tables_traduites;
}

/**
 * Compile la balise `#OBJET_ETAT_ARCHIVAGE` qui renvoie la liste des types d'objet autorisés à l'archivage
 * Chaque type d'objet est fourni avec son titre.
 * La signature de la balise est : `#TYPE_OBJET_AVEC_ARCHIVE`.
 *
 * @balise
 *
 * @param Champ $p
 *                 Pile au niveau de la balise.
 *
 * @return Champ
 *               Pile complétée par le code à générer.
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
	$p->code = "calculer_etat_archivage(${objet}, ${id_objet}, ${information})";

	return $p;
}

/**
 * @internal
 *
 * @param mixed $objet
 * @param mixed $id_objet
 * @param mixed $information
 *
 * @return array
 */
function calculer_etat_archivage($objet, $id_objet, $information) {

	// Tableau de l'archivage de l'objet
	include_spip('inc/archobjet');
	$etat_archivage = archivage_lire_etat_objet(
		$objet,
		$id_objet
	);

	return $information ? $etat_archivage[$information] : $etat_archivage;
}

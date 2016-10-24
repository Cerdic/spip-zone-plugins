<?php
/**
 * Définit les autorisations du plugin encarts
 *
 * @plugin     encarts
 * @copyright  2013-2016
 * @author     Cyril
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function encarts_autoriser() { }


/* Exemple
function autoriser_configurer_encarts_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_encarts') => $type = 'encarts'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet encarts


/**
 * Autorisation de voir un élément de menu (encarts)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encarts_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}


/**
 * Autorisation de voir le bouton d'accès rapide de création (encart)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encartcreer_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('creer', 'encart', '', $qui, $opt);
}

/**
 * Autorisation de créer (encart)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encart_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de voir (encart)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encart_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (encart)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encart_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de supprimer (encart)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encart_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

/**
 * Autorisation à associer un encart à un objet donné.
 *
 * Il faut pouvoir modifier l'objet
 * ou être admin complet
 *
 * @example
 *     ```
 *     #AUTORISER{associer,encart,#ID_ENCART,'',#ARRAY{objet,#OBJET,id_objet,#ID_OBJET}}
 *     ```
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet auquel on veut associer un encart
 * @param  int $id Identifiant de l'objet auquel on veut associer un encart
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opts Options de cette autorisation
 *                       Doit contenir les clés `objet` et `id_objet`
 *                       pour rensigner le type et l'identifiant de l'objet
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_encart_associer_dist($faire, $type, $id, $qui, $opts) {

	$autoriser = (
		($qui['statut'] == '0minirezo' AND !$qui['restreint'])
		OR (autoriser('modifier', $opts['objet'], $opts['id_objet'], $qui))
	) ? true : false;

	return $autoriser;
}

/**
 * Autorisation à dissocier un encart d'un objet donné.
 *
 * Il faut être autorisé à associer un encart à l'objet,
 * et qu'il ne soit pas inséré dans le texte.
 *
 * @example
 *     ```
 *     #AUTORISER{dissocier,encart,#ID_ENCART,'',#ARRAY{objet,#OBJET,id_objet,#ID_OBJET}}
 *     ```
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opts Options de cette autorisation
 *                       Doit contenir les clés `objet` et `id_objet`
 *                       pour renseigner le type et l'identifiant de l'objet
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_encart_dissocier_dist($faire, $type, $id, $qui, $opts) {

	$autoriser = (
		autoriser('associer', 'encart', $id, $qui, $opts)
		AND (sql_getfetsel('vu', "spip_encarts_liens", "id_encart=" . intval($id) . " AND objet=" . sql_quote($opts['objet']) . " AND id_objet=" . intval($opts['id_objet'])) == 'non')
	) ? true : false;

	return $autoriser;
}


/**
 * Autorisation de lier/délier l'élément (encarts)
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associerencarts_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


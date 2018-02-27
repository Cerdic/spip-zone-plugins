<?php
/**
 * Définit les autorisations du plugin Chapitres
 *
 * @plugin     Chapitres
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Chapitres\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function chapitres_autoriser() {
}


// -----------------
// Objet chapitres




/**
 * Autorisation de créer (chapitre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_chapitre_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de voir (chapitre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_chapitre_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (chapitre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_chapitre_modifier_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or ($auteurs = chapitres_auteurs_objet('chapitre', $id) and in_array($qui['id_auteur'], $auteurs));
}

/**
 * Autorisation de supprimer (chapitre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_chapitre_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Lister les auteurs liés à un objet
 *
 * @param int $objet Type de l'objet
 * @param int $id_objet Identifiant de l'objet
 * @return array        Liste des id_auteur trouvés
 */
function chapitres_auteurs_objet($objet, $id_objet) {
	$auteurs = sql_allfetsel('id_auteur', 'spip_auteurs_liens', array('objet = ' . sql_quote($objet), 'id_objet = ' . intval($id_objet)));
	if (is_array($auteurs)) {
		return array_map('reset', $auteurs);
	}
	return array();
}

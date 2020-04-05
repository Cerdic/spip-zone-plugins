<?php
/**
 * Définit les autorisations du plugin Alertes
 *
 * @plugin     Alertes
 * @copyright  2016-2017
 * @author     Netapsys
 * @licence    GNU/GPL
 * @package    SPIP\Alertes\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function alertes_autoriser() {
}


// -----------------
// Objet alertes


/**
 * Autorisation de créer (alerte)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_alerte_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite', '6forum'));
}

/**
 * Autorisation de voir (alerte)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_alerte_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (alerte)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_alerte_modifier_dist($faire, $type, $id, $qui, $opt) {
	include_spip('alertes_fonctions');

	return in_array($qui['statut'], array('0minirezo')) or alerte_auteur_en_ligne($id, $qui['id_statut']);
}

/**
 * Autorisation de supprimer (alerte)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_alerte_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'] or alerte_auteur_en_ligne($id, $qui['id_statut']);
}

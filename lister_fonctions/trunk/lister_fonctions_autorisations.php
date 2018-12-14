<?php
/**
 * Définit les autorisations du plugin Lister les objets principaux de SPIP
 *
 * @plugin     Lister les objets principaux de SPIP
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\ListerFonctions\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function lister_fonctions_autoriser() {

}


/**
 * Autorisation de voir `lister_fonctions`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerfonctions_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation de configurer `lister_fonctions`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerfonctions_configurer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 3.
// ------
/**
 * Autorisation pour afficher le lien vers la page ?fonctions=configurer_lister_fonctions
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurerlisterfonctions_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation générique pour afficher le lien vers la page ?fonctions=lister_fonctions*
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerfonctions_menu_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/filtres');
	$info = chercher_filtre('info_plugin');
	$i = $info('lister_config', 'est_actif');

	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui' and (empty($i) or $i == false or $i === 0);
}

/**
 * Autorisation pour afficher le lien vers la page ?fonctions=lister_fonctionscompletes
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerfonctionscompletes_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_listerfonctions_menu_dist($faire, $type, $id, $qui, $opt);
}

/**
 * Autorisation pour afficher le lien vers la page ?fonctions=lister_fonctionsuser
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerfonctionsuser_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser_listerfonctions_menu_dist($faire, $type, $id, $qui, $opt);
}

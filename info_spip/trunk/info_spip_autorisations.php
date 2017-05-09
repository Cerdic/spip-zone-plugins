<?php
/**
 * Définit les autorisations du plugin Info SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function info_spip_autoriser() {

}


/**
 * Autorisation de voir `info_spip`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infospip_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation de configurer `info_spip`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infospip_configurer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 2
// ------
/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_info_spip
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurer_info_spip_bouton_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=info_config
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_info_config_bouton_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_info_spip
 * Pour le plugin Bando
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurer_info_spip_bando_bouton_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=info_config
 * Pour le plugin Bando
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_info_config_bando_bouton_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 3.
// cf. `menu` à la place de `bouton`
// ------
/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_info_spip
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurerinfospip_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=info_config
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infoconfig_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

?>

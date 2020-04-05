<?php
/**
 * Définit les autorisations du plugin Lister les extensions PHP chargées
 *
 * @plugin     Lister les extensions PHP chargées
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\ListerExtensionsphp\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function lister_extensions_php_autoriser() {

}


/**
 * Autorisation de voir `lister_extensions_php`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerextensionsphp_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation de configurer `lister_extensions_php`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerextensionsphp_configurer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 3.
// cf. `menu` à la place de `bouton`
// ------
/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_lister_extensions_php
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurerlisterextensionsphp_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=lister_extensions_php
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerextensionsphp_menu_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/filtres');
	$info = chercher_filtre('info_plugin');
	$i = $info('lister_config', 'est_actif');

	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui' and (empty($i) or $i == false or $i === 0);
}

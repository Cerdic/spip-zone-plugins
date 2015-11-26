<?php
/**
 * Définit les autorisations du plugin Les plugins nécessaires au site
 *
 * @plugin     Les plugins nécessaires au site
 * @copyright  2013-2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\ListerPlugins\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function lister_plugins_autoriser()
{

}


/**
 * Autorisation de voir `lister_plugins`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_listerplugins_voir_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation de configurer `lister_plugins`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_listerplugins_configurer_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 3.
// cf. `menu` à la place de `bouton`
// ------
/**
 * Autorisation pour afficher le lien vers la page ?plugins=configurer_lister_plugins
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_configurerlisterplugins_menu_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?plugins=lister_plugins
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_listerplugins_menu_dist($faire, $type, $id, $qui, $opt)
{
	include_spip('inc/filtres');
	$info = chercher_filtre('info_plugin');
	$i = $info('lister_config', 'est_actif');
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui' and (empty($i) or $i == false or $i === 0);
}

?>
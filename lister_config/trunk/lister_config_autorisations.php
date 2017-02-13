<?php

/**
 * Plugin Lister les pages de configurations
 * Licence GPL
 * Auteur : Teddy Payet.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Autorisation de voir le lien de menu.
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int $id       Identifiant de l'objet
 * @param array $qui    Description de l'auteur demandant l'autorisation
 * @param array $opt    Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_lister_config_menu_dist($faire, $type, $id, $qui, $opt) {
	include_spip('autoriser', 'inc');

	return autoriser('configurer', '_plugins');
}

/**
 * Autorisation de voir la page.
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int $id       Identifiant de l'objet
 * @param array $qui    Description de l'auteur demandant l'autorisation
 * @param array $opt    Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_listerconfig_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de voir le lien de menu.
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int $id       Identifiant de l'objet
 * @param array $qui    Description de l'auteur demandant l'autorisation
 * @param array $opt    Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_listerconfigplugins_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de voir la page.
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int $id       Identifiant de l'objet
 * @param array $qui    Description de l'auteur demandant l'autorisation
 * @param array $opt    Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_listerconfigplugins_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

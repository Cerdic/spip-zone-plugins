<?php
/**
 * Plugin Reassocier Auteurs Objets
 * Licence GPL (c) Matthieu Marcillaud
 * 
 * Fichier d'autorisation du plugin
 * 
 * @package SPIP\rao\Autorisations
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/** Fonction d'appel du pipeline **/
function rao_autoriser(){}



/**
 * Autorisation de voir un élément de menu (rao)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rao_menu_dist($faire, $type, $id, $qui, $opt) {
	// seulement des administrateurs
	return ($qui['statut'] == '0minirezo') and !$qui['restreint'];
} 

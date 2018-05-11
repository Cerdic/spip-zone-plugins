<?php
/**
 * Définit les autorisations du plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2016-2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Autorisations
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function metasplus_autoriser() {
}


/**
 * Autorisation de prévisualiser les métas+ d'un objet
 *
 * Il faut être admin et avoir le droit de modifier l'objet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_previsualiser_metasplus_dist($faire, $type, $id, $qui, $opt){

	$is_admin = ($qui['statut'] == '0minirezo');
	$autoriser_modifier = autoriser('modifier', $type, $id, $qui, $opt);
	$autoriser = ($is_admin and $autoriser_modifier);

	return $autoriser;
}
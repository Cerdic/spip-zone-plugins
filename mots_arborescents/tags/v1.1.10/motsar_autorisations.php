<?php
/**
 * Définit les autorisations du plugin Mots arborescents
 *
 * @plugin     Mots arborescents
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Motsar\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function motsar_autoriser(){}



/**
 * Autorisation de supprimer un mot
 *
 * Surcharge l'autorisation du plugin mots pour tenir compte des mots enfants.
 *
 * On ne peut pas supprimer un mot s'il possède des mots enfants.
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_mot_supprimer($faire, $type, $id, $qui, $opt) {
	// si l'autorisation normale ne passe déjà pas, partir !
	if (!autoriser_mot_supprimer_dist($faire, $type, $id, $qui, $opt)) {
		return false;
	}
	return sql_countsel('spip_mots','id_parent='.intval($id)) ? false : true;
}

<?php
/**
 * Plugin Groupes arborescents de mots clés
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/** declaration vide pour ce pipeline. **/
function gma_autoriser(){}


/**
 * Autorisation de supprimer un groupe de mots
 *
 * Surcharge l'autorisation du plugin mots
 * pour tenir compte des sous groupes.
 *
 * On ne peut pas supprimer un groupe de mots s'il possède des sous groupes.
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_groupemots_supprimer($faire, $type, $id, $qui, $opt) {
	// si l'autorisation normale ne passe déjà pas, partir !
	if (!autoriser_groupemots_supprimer_dist($faire, $type, $id, $qui, $opt)) {
		return false;
	}
	return sql_countsel('spip_groupes_mots','id_parent='.intval($id))?false:true;
}
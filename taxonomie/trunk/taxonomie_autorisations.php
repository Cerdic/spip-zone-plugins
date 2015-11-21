<?php
/**
 * Définit les autorisations du plugin Taxonomie
 *
 * @package    SPIP\TAXONOMIE\AUTORISATIONS
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline.
 *
 * @pipeline autoriser
**/
function taxonomie_autoriser(){}


// -----------------
// Objet taxons

/**
 * Autorisation de créer un taxon : il faut être au moins rédacteur.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

/**
 * Autorisation de voir un taxon : tout le monde est autorisé.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier un taxon : il faut pouvoir en créer un.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('creer', 'taxon', $id, $qui, $opt);
}

/**
 * Autorisation de supprimer un taxon : aucun taxon ne peut être supprimé individuellement.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return false;
}

/**
 * Autorisation d'iconifier un taxon : aucun taxon ne peut être iconifié actuellement.
 *
 * @param string	$faire
 * 		Action demandée.
 * @param string	$type
 * 		Type d'objet sur lequel appliquer l'action.
 * @param int		$id
 * 		Identifiant de l'objet.
 * @param array		$qui
 * 		Description de l'auteur demandant l'autorisation.
 * @param array		$opt
 * 		Options de cette autorisation.
 *
 * @return bool
 * 		`true` si l'autoriation est donnée, `false` sinon
**/
function autoriser_taxon_iconifier_dist($faire, $type, $id, $qui, $opt) {
	return false;
}

?>
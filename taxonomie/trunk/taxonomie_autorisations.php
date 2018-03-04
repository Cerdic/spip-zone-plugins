<?php
/**
 * Définit les autorisations du plugin Taxonomie
 *
 * @package    SPIP\TAXONOMIE\TAXON
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
 * Autorisation de modifier un taxon : il faut pouvoir en créer un et que l'id soit précisé.
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
	$autoriser = false;
	if (intval($id)) {
		$autoriser = autoriser('creer', 'taxon', $id, $qui, $opt);
	}

	return $autoriser;
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

/**
 * Autorisation de voir la liste des taxons : tout le monde est autorisé.
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
function autoriser_taxons_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}


/**
 * Autorisation sur l'entrée de menu affichant la liste des taxons : même autorisation que
 * `voir_taxons`, c'est-à-dire, tout le monde.
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
function autoriser_taxons_menu_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return
		autoriser('voir', '_taxons', $id, $qui, $opt);
}

// -----------------
// Objet especes


/**
 * Autorisation de voir un élément de menu (especes)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_especes_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
}


/**
 * Autorisation de voir le bouton d'accès rapide de création (espece)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espececreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'espece', '', $qui, $opt);
}

/**
 * Autorisation de créer (espece)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espece_creer_dist($faire, $type, $id, $qui, $opt) {

	include_spip('inc/taxonomie');
	include_spip('taxonomie_fonctions');

	// On vérifie qu'un règne est bien déjà chargé
	$regnes = regne_lister();
	$regne_existe = false;
	foreach ($regnes as $_regne) {
		if (taxonomie_regne_existe($_regne, $meta_regne)) {
			$regne_existe = true;
		}
	}

	// Il faut aussi être admin ou rédacteur.
	$autoriser = $regne_existe and in_array($qui['statut'], array('0minirezo', '1comite'));

	return $autoriser;
}

/**
 * Autorisation de voir (espece)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espece_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (espece)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espece_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de supprimer (espece)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espece_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

<?php
/**
 * Définit les autorisations du plugin produits
 *
 * @plugin     produits
 * @copyright  2014
 * @author     Les Développements Durables, http://www.ldd.fr
 * @licence    GNU/GPL
 * @package    SPIP\Produits\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function produits_autoriser(){}


// -----------------
// Objet produits


/**
 * Autorisation de voir un élément de menu (produits)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_produits_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de voir le bouton d'accès rapide de création (produit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_produitcreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'produit', '', $qui, $opt);
} 

/**
 * Autorisation de créer (produit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_produit_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite')) AND sql_countsel('spip_rubriques')>0); 
}

/**
 * Autorisation de voir (produit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_produit_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (produit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_produit_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de supprimer (produit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_produit_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


/**
 * Autorisation de lier/délier l'élément (produits)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerproduits_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


/**
 * Autorisation de créer l'élément (produits) dans une rubrique
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/

function autoriser_rubrique_creerproduitdans_dist($faire, $type, $id, $qui, $opt) {
	return produits_autoriser_creerproduitdans($id);
}

function autoriser_produit_creer_bouton_dist($faire, $type, $id, $qui, $opt) {
	if( isset($opt['contexte']['id_rubrique']) )
		return produits_autoriser_creerproduitdans($opt['contexte']['id_rubrique']);
	else
		return true ;
}

function produits_autoriser_creerproduitdans($id_rubrique) {
	include_spip('inc/config');
	$config = lire_config('produits') ;
	
	if($id_rubrique && $config['limiter_ajout']) {
		// La rubrique est-elle dans un des secteurs ?
		spip_log("creerproduitdans config ".print_r($config,true),"produits");
		$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
		if(is_array($config['limiter_ident_secteur']) && !in_array($id_secteur,$config['limiter_ident_secteur']))
			return false ;
	}
	return true;
}

?>
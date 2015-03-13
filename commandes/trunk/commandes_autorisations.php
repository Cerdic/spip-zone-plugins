<?php
/**
 * Définit les autorisations du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Autorisations
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function commandes_autoriser(){}


/**
 * Autorisation à passer une commande
 *
 * - un client (auteur+contact)
 * Nécessite le plugin Contacts et organisations
 * Todo : autoriser en absence du plugin
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commander_dist($faire, $type, $id, $qui, $opts){
	if (
		$id_auteur = $qui['id_auteur'] > 0
		and $contact = sql_getfetsel('id_contact', 'spip_contacts_liens', 'objet = '.sql_quote('auteur').' and id_objet = '.sql_quote($id_auteur))
	)
		return true;
	else
		return false;
}


/**
 * Autorisation à voir une commande
 *
 * - l'auteur de la commande
 * - admin (mais pas restreint)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commande_voir_dist($faire, $type, $id, $qui, $opts) {
	return
		$qui['id_auteur'] == sql_getfetsel('id_auteur', 'spip_commandes', 'id_commande = '.sql_quote($id)) OR 
			( $qui['statut'] == '0minirezo'
			 AND !$qui['restreint'] );
}


/**
 * Autorisation à supprimer une commande
 *
 * - statut `encours`
 * - admin (mais pas restreint) ou auteur de la commande
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commande_supprimer_dist($faire, $type, $id, $qui, $opts) {
	return
		sql_getfetsel('statut', table_objet_sql('commande'), "id_commande=".intval($id)) == 'encours'
		AND (( $qui['statut'] == '0minirezo' AND !$qui['restreint'] )
		  OR $qui['id_auteur'] == sql_getfetsel('id_auteur', table_objet_sql('commande'), "id_commande=".intval($id)));
}


/**
 * Autorisation à supprimer un détail d'une commande
 *
 * - par défaut la même chose que pour modifier la commande
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commande_supprimerdetail_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('modifier', 'commande', $id, $qui, $opts);
}


/**
 * Autorisation à modifier une commande
 *
 * - l'auteur de la commande si celle-ci est encore "encours"
 * - sinon admin (mais pas restreint)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commande_modifier_dist($faire, $type, $id, $qui, $opts) {
	$infos_commande = sql_fetsel('id_auteur, statut', table_objet_sql('commande'), "id_commande=".intval($id));
	
	if (
		$infos_commande
		and (
			($infos_commande['statut'] == 'encours' and $qui['id_auteur'] == $infos_commande['id_auteur'])
			or
			($qui['statut'] == '0minirezo' and !$qui['restreint'])
		)
	) {
		return true;
	}
	
	return false;
}


/**
 * Autorisation à dater une commande
 *
 * Idem autorisation modifier
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commande_dater_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('modifier', 'commande', $id, $qui, $opts);
}

?>

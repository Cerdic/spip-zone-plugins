<?php
/**
 * Définit les autorisations du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2013
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Autorisations
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function commandes_autoriser($flux){ return $flux; }

/**
 * Autorisation à passer une commande
 * - un client (auteur+contact)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de la commande
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/ 
function autoriser_commander_dist($faire, $quoi, $id, $qui, $ops){
	if (
		$id_auteur = $qui['id_auteur'] > 0
		and $contact = sql_getfetsel('id_contact', 'spip_contacts_liens', 'objet = '.sql_quote('auteur').' and id_objet = '.sql_quote($id_auteur))
	)
		return true;
	else
		return false;
}

/**
 * Autorisation à voir(?) une commande
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
function autoriser_commande_dist($faire, $quoi, $id, $qui, $opts) {
	return
		$qui['id_auteur'] == sql_getfetsel('id_auteur', 'spip_commandes', 'id_commande = '.sql_quote($id)) OR 
			( $qui['statut'] == '0minirezo'
			 AND !$qui['restreint'] );
}

/**
 * Autorisation à supprimer un détail d'une commande
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
function autoriser_commande_supprimerdetail_dist($faire, $type, $id, $qui, $opts) {
	return
		$qui['id_auteur'] == sql_getfetsel('id_auteur', table_objet_sql('commande'), "id_commande=".intval($id))
		OR ( $qui['statut'] == '0minirezo' AND !$qui['restreint'] );
}

?>

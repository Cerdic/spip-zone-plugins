<?php
/**
 * Fichier en doublon (et plus synchro) avec commandes_autorisations.php
 * A supprimer ?
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Pour le pipeline
function commandes_autoriser($flux){ return $flux; }

// Qui peut passer une commande : un client (auteur+contact)
function autoriser_commander_dist($faire, $quoi, $id, $qui, $options){
	if (
		$id_auteur = $qui['id_auteur'] > 0
		and $contact = sql_getfetsel('id_contact', 'spip_contacts_liens', 'objet = '.sql_quote('auteur').' and id_objet = '.sql_quote($id_auteur))
	)
		return true;
	else
		return false;
}

// on ne laisse pas les redacteurs voir les commandes
// ni voir les commandes dans la recherche spip
// Sauf pour celles dont il est l'auteur
function autoriser_commande_dist($faire, $quoi, $id, $qui, $options) {
	return
		$qui['id_auteur'] == sql_getfetsel('id_auteur', 'spip_commandes', 'id_commande = '.sql_quote($id)) OR 
			( $qui['statut'] == '0minirezo'
			  AND !$qui['restreint'] );
}

?>

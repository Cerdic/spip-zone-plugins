<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('collections_fonctions');

// declaration vide pour ce pipeline.
function collections_autoriser(){}

// -----------------
// Objet collections


// bouton de menu
function autoriser_collections_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_collectioncreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'collection', '', $qui, $opts);
} 

// creer
function autoriser_collection_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite','6forum'));
}

// voir les fiches completes
function autoriser_collection_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation à lier un média à une collection
 *
 * Peuvent le faire :
 * -* les admins de collections
 * -* les auteurs liés à la collection (participants)
 * -* les administrateurs du site
 */
function autoriser_collection_lierobjet_dist($faire, $type, $id, $qui, $opt) {
	return  collection_admin($id,$qui) OR collection_auteur($id,$qui) OR (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}
// modifier
function autoriser_collection_modifier_dist($faire, $type, $id, $qui, $opt) {
	return collection_admin($id,$qui) OR (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}

// supprimer
function autoriser_collection_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return collection_admin($id,$qui) OR (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}


// associer (lier / delier)
function autoriser_associercollections_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

/**
 * Autorisation d'association d'auteurs à une collection
 * La collection doit être coopérative
 */ 
function autoriser_collection_associerauteurs_dist($faire, $type, $id, $qui, $opt){
	$type = sql_getfetsel('type_collection','spip_collections','id_collection='.intval($id));
	if($type != 'coop')
		return false;
	
	return in_array($qui['statut'],array('0minirezo','1comite','6forum')); 
}


?>
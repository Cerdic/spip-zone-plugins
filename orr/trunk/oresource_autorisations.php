<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function recherche_autorisation($idressource,$statut_connecte,$autorisation){
$result= false;
$res = sql_select(
	array(
		"auto.id_orr_autorisation AS idauto",
		"auto.orr_type_objet AS type",
		"auto.orr_statut AS statut",
		"auto.orr_autorisation_valeur AS valeur"),
	array(
		"spip_orr_autorisations AS auto",
		"spip_orr_autorisations_liens AS lien"),
	array(
		"auto.id_orr_autorisation = lien.id_orr_autorisation",
		"lien.objet='orr_ressource'",
		"lien.id_objet=$idressource"));

	while ($r=sql_fetch($res)) {
		
		if (  ($r['type'] == "statut") AND ($r['statut'] == "$statut_connecte") ) {
			if (strpos($r['valeur'], $autorisation) !== false) {
				$result = true;
			}
		}
	}
	return $result;
}

// declaration vide pour ce pipeline.
function oresource_autoriser(){}


// -----------------
// Objet orr_ressources


// bouton de menu
function autoriser_orrressources_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
}


// creer
function autoriser_orrressource_creer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// voir les fiches completes
function autoriser_orrressource_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// modifier
function autoriser_orrressource_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// supprimer
function autoriser_orrressource_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}


// -----------------
// Objet orr_reservations




// creer
function autoriser_orrreservation_creer_dist($faire, $type, $id, $qui, $opt) {
	$statut=$qui['statut'];
	$autorisation="C";
	$resultat=recherche_autorisation($id,$statut,$autorisation);
	return $resultat;
}

// voir les fiches completes
function autoriser_orrreservation_voir_dist($faire, $type, $id, $qui, $opt) {
	$statut=$qui['statut'];
	$autorisation="V";
	$resultat=recherche_autorisation($id,$statut,$autorisation);
	return $resultat;
}

// modifier
function autoriser_orrreservation_modifier_dist($faire, $type, $id, $qui, $opt) {
	$statut=$qui['statut'];
	$autorisation="M";
	$resultat=recherche_autorisation($id,$statut,$autorisation);
	return $resultat;
}

// supprimer
function autoriser_orrreservation_supprimer_dist($faire, $type, $id, $qui, $opt) {
	$statut=$qui['statut'];
	$autorisation="S";
	$resultat=recherche_autorisation($id,$statut,$autorisation);
	return $resultat;
}


// associer (lier / delier)
function autoriser_associerorrreservations_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


?>

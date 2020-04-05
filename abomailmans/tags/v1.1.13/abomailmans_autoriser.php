<?php
/**
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * © 2007 - 2012
 */

/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
*/
function abomailmans_autoriser(){}

if (!defined("_ECRIRE_INC_VERSION")) return;

// autorisation des boutons
function autoriser_abomailman_bouton_dist($faire, $type, $id, $qui, $opt) {
	spip_log('bouton autoriser','test');
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_abomailman_creer_dist($faire, $type, $id, $qui, $opt){
	spip_log('creer autoriser','test');
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_abomailman_modifier_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut']=='0minirezo')  AND !$qui['restreint'];
}

?>
<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2012
 * $Id: abomailmans_autoriser.php 31752 2009-09-23 00:09:48Z kent1@arscenic.info $
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function abomailmans_autoriser(){}

// acces aux listes abomailmans = tous les admins
function autoriser_abomailmans_dist($faire, $type, $id, $qui, $opt) {
	return (($qui['statut'] == '0minirezo') 
			AND !$qui['restreint']
			);
}
// autorisation des boutons
function autoriser_abomailmans_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_abomailmans_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_abomailmans_modifier_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut']=='0minirezo')  AND !$qui['restreint'];
}

?>
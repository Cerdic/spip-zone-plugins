<?php

/**
 * Fonction d'autorisation pour l'edition (insertion ou modif) d'une notation
 * 
 * $opt recoit $objet et $id_objet au cas ou.
 * 
 * @return bool true/false
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function notation_autoriser(){}

function autoriser_notation_modifier_dist($faire, $type, $id, $qui, $opt){
	include_spip('inc/config'); // lire_config
	// la config interdit de modifier la note ?
	if ($id AND !lire_config('notation/change_note'))
		return false;
		
	// sinon est-on autorise a voter ?
	$acces = lire_config('notation/acces','all');

	if ($acces!='all'){
		// tous visiteur
		if ($acces=='ide' && $qui['statut']=='')
			return false;
		// auteur
		if ($acces=='aut' && !in_array($qui['statut'],array("0minirezo","1comite")))
			return false;
		// admin
		if ($acces=='adm' && !$qui['statut']=="0minirezo")
			return false;
	}
	return true;
}

/**
 * Autorisation pouvant être utilisée pour limiter la divulgation des  noms des personnes qui notent
 * 
 * @param unknown_type $faire
 * @param unknown_type $type
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opt
 */
function autoriser_notation_administrer_dist($faire,$type,$id,$qui,$opt){
	return $qui['statut'] == '0minirezo';
}

/**
 * Moderer les notes ?
 * -* modifier l'objet correspondant (si note attache a un objet)
 * -* droits par defaut sinon (admin complet pour moderation complete)
 * Enter description here ...
 * @param unknown_type $faire
 * @param unknown_type $type
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opt
 */
function autoriser_moderernote_dist($faire, $type, $id, $qui, $opt) {
	return
		autoriser('modifier', $type, $id, $qui, $opt);
}
?>
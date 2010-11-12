<?php

/**
 * Fonction d'autorisation pour l'edition (insertion ou modif) d'une notation
 * 
 * $opt recoit $objet et $id_objet au cas ou.
 * 
 * @return bool true/false
 */

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


?>

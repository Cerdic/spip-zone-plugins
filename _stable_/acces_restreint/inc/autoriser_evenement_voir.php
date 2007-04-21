<?php

//Cette fonction permet à d'autres plugins d'interagir, en particulier accès retsreint
function inc_autoriser_evenement_voir($id_evenement) {
	static $evenements_exclus=NULL;
	if ($evenements_exclus===NULL){
		$evenements_exclus = AccesRestreint_liste_evenements_exclus(_DIR_RESTREINT!="");
		$evenements_exclus = array_flip($evenements_exclus);
	}
	
	if (isset($evenements_exclus[$id_evenement]))
		return false;
	return true;
}


?>
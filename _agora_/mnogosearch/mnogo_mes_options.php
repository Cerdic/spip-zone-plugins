<?php

function mnogo_checkresults(){
	global $mnogo_resultats_synthese;
	global $mnogo_resultats;
	if (!isset($mnogo_resultats_synthese['MNOGO_TOTAL'])){
		include_spip('inc/mnogo_distant');
		mnogo_getresults();
	}
	return true;
}

function retour_balise_MNOGO_RECHERCHE(){
	global $mnogo_resultats_synthese;
	mnogo_checkresults();	
	var_dump($mnogo_resultats_synthese);
	return $mnogo_resultats_synthese['MNOGO_RECHERCHE'];
}


?>
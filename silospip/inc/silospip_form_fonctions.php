<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
\***************************************************************************/


function silospip_champs_formulaire(){
	$prefix = 'silo_';
	$champs = array();
	// $champs[] = $prefix."id_site";
	$champs[] = $prefix."nom";
	$champs[] = $prefix."domaine";
	$champs[] = $prefix."titre";
	$champs[] = $prefix."descriptif";
//	$champs[] = $prefix."date";
//	$champs[] = $prefix."id_createur";
	$champs[] = $prefix."lang";
//	$champs[] = $prefix."maj";
//	$champs[] = $prefix."statut";

	return $champs;
  
}

?>

<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr) /b_b
*
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*
* Recuperation des parametres, la fonction de ponderation
*
**/


function notation_get_ponderation(){
	$ponderation = lire_config('notation/ponderation');
	if ($ponderation == '') $ponderation = 30;
		$ponderation = intval($ponderation);
	if ($ponderation < 1) $ponderation = 1;
		return $ponderation;
}

function notation_get_acces(){
	$acces = lire_config('notation/acces');
	if ($acces == '') $acces = 'all';
		return $acces;
}

function notation_get_nb_notes(){
	$nb = intval(lire_config('notation/nombre'));
	if ($nb < 1) $nb = 5;
		if ($nb > 10) $nb = 10;
			return $nb;
}

// Calcule de la note ponderee
function notation_ponderee ($note, $nb){
   $note_ponderee = round($note*(1-exp(-5*$nb/notation_get_ponderation()))*100)/100;
   return $note_ponderee;
}

?>
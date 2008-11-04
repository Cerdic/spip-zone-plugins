<?php
/**
* Plugin Notation 
* par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/

// Affichage des etoiles cliquables
function notation_etoile_click($nb, $id) { 
	include_spip('inc/notation');
	$ret = '';
	if ($nb>0 && $nb<=0.5){
		$nb=1;
	}else{
		$nb = round($nb);
	}
	for ($i=1; $i<=notation_get_nb_notes(); $i++){
		$ret .= "<input name='notation$id' type='radio' class='auto-submit-star' value='$i' ";
		if($i==$nb){
			$ret .= "checked='checked' ";
		}
		$ret .= "/>\n";
	}
	return $ret;
}

// Affichage d'un nombre sous forme d'etoiles
function notation_etoile($nb,$id){
	include_spip('inc/notation');
	if ($nb>0 && $nb<=0.5){
		$nb=1;
	}else{
		$nb = round($nb);
	}
	for ($i=1; $i<=notation_get_nb_notes(); $i++){
		$ret .= "<input name='star$id' type='radio' class='star' disabled='disabled' ";
		if($i==$nb){
			$ret .= "checked='checked' ";
		}
		$ret .= "/>\n";

	}
	return $ret;
}

/**
* les balises du plugin
*  
**/


function balise_NOTATION_ETOILE($p){
	$nb = interprete_argument_balise(1,$p);
	$id = interprete_argument_balise(2,$p);
	$p->code = "notation_etoile($nb,$id)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_NOTATION_ETOILE_CLICK($p){
	$nb = interprete_argument_balise(1,$p);
	$id = interprete_argument_balise(2,$p);
	$p->code = "notation_etoile_click($nb,$id)";
	$p->interdire_scripts = false;
	return $p;
}


?>

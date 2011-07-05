<?php

function propre_latex($t) {
	
	$t = latex_echappe_coloration($t);

	$t = echappe_html($t);
	$t = appliquer_regles_wheel($t,array('latex/latex.yaml'));
	$t = echappe_retour($t);
	
	$t = latex_retour_coloration($t);

	return $t;
}


function latex_echappe_coloration($texte){
	return appliquer_regles_wheel($texte,array('latex/latex-code.yaml'));
}

function appliquer_regles_wheel($texte,$regles){
	$ruleset = SPIPTextWheelRuleset::loader(
			$regles
		);
	$wheel = new TextWheel($ruleset);
	return  $wheel->text($texte);
}

function latex_retour_coloration($t){
	$t = preg_replace_callback(",<latex>(.*)</latex>,u",latex_retour_coloration_decode,$t);
	return $t;	
}

function latex_retour_coloration_decode($t){
	return base64_decode($t[1]);	
}
function latex_recuperer_php($t){
	
	return str_replace('&lt;?','<?',$t);
	
}	

?>
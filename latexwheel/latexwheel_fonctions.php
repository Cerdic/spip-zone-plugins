<?php

function propre_latex($t) {

	$t = echappe_html(latex_echappe_coloration($t));
	
	$t = appliquer_regles_wheel($t,array('latex/latex.yaml'));
	$t = echappe_retour($t);


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
?>
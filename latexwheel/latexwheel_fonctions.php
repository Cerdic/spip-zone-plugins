<?php

function propre_latex($t) {
	
	$t = echappe_html(latex_echappe_coloration($t)); 
	
	$t = appliquer_regles_wheel($t,array('latex/latex.yaml'));
	$t = latex_traiter_modeles($t);
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

function latex_recuperer_php($t){
	
	return str_replace('&lt;?','<?',$t);
	
}	

function latex_traiter_modeles($texte) {
	/* Je reprend le code des spip2latex_traiter_modeles du plugin spip2latex/*
	include_spip('inc/lien');

	/*
	 * code, cadre/frame et math sont deja traites et sont base64-encodes
	 * On ne devrait pas les voir ici.
	 */
	$modeles_builtin = array('<sc>', '<sup>', '<sub>', '<del>', '<quote>',
				 '<cadre>', '<frame>', '<poesie>', '<poetry>',
				 '<code>', '<math>');

	$modele_regex = sprintf("@%s@is", _RACCOURCI_MODELE);
	if (preg_match_all($modele_regex, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			
			/*
			 * Seront traites plus tard.
			 */
			if (in_array(trim($reg[0]), $modeles_builtin))
				continue;

			/*	
			 * Supprimer les echappements dans l'appel du
			 * modele.
			 * XXX seulement _ ?
			 */
			$modele = sprintf("<latex_%s", 
					  substr($reg[0], 1));
			$s = array("@\\_");
			$r = array("_");
			$modele = str_replace($s, $r, $modele);
			
			$search[] = $reg[0];
			$replace[] = $modele;
		}
	
		$texte = str_replace($search, $replace, $texte);
		$texte = traiter_modeles($texte);
	}

	return $texte;
}


?>
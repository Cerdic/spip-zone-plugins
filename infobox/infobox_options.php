<?php
/**
 * Plugin Infobox pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */
 
function balise_INFOBOX($p) {

	$_id = interprete_argument_balise(1, $p);
	
	if ($_id) {
	
		$_cc = 1;
		$_tc = array();
		
		while(interprete_argument_balise($_cc,$p)) {
			$_tc[$_cc] .= interprete_argument_balise($_cc,$p);
			$_cc++;
		}
		$_tc[1] = substr($_tc[1],1,strlen($_tc[1])-2); // On efface les simple quote!
		$_tc[2] = substr($_tc[2],1,strlen($_tc[2])-2); // On efface les simple quote!

		$class		= addslashes($_tc[1]);
		$message	= "- ".addslashes($_tc[2])."<br/> ";
		
		$envoi 		= met_sous_enveloppe($class,$message);
	
	} else {
		
		$class 		= "divers";
		$message 	= "<br/>Veuillez mettre des param&egrave;tres &agrave; la balise infobox...";
		
		$envoi 		= met_sous_enveloppe($class,$message);
	}

	$p->code = "$envoi";
	$p->interdire_scripts = false;
	return $p;
}

function met_sous_enveloppe($class,$message) {

	$envoi = "'<script type=\"text/javascript\">$(#document).ready(function(){
		$(function(){
			$(\'#infobox fieldset.infobox fieldset.$class\').append(\'$message\'); 
		});
		alert(\'message envoyé à infobox\');
	});</script>'";

	return $envoi;
}


?>
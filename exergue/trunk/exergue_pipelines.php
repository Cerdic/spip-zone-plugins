<?php

function exergue_pre_propre($letexte) {

	// trouver des balises exergue
	if (preg_match_all(",<(exergue)>(.*)<\/(exergue)>,Uims",
	$letexte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {							
			// ne pas mettre le <div...> s'il n'y a qu'une ligne
			if (is_int(strpos($reg[0],"\n"))) {
				$letexte = str_replace($reg[0], "<div class=\"spip_exergue\">"."\n\n" . $reg[2] . "</div>", $letexte);
			} else {
				$letexte = str_replace($reg[0], "<span class=\"spip_exergue\">" . $reg[2] . "</span>", $letexte);		
			}
		}
	}
	return $letexte;
	
}

function exergue_post_propre($letexte) {
	/* nettoyer les ancres  <p><a name="exergue"></a></p> */
	$letexte = str_replace('<p><a name="exergue"></a></p>','<a name="exergue"></a>',$letexte);
	$letexte = str_replace('<p><a id="exergue"></a></p>','<a id="exergue"></a>',$letexte);

	return $letexte;
}


function exergue_insert_head($flux) {
	$flux .= '<script src="'.find_in_path("js/exergue.js").'" type="text/javascript"></script>';

	return $flux;
}

function exergue_insert_head_css($flux) {
	return $flux . "<link rel='stylesheet' href='".find_in_path("css/exergue.css")."' type='text/css' />";
}

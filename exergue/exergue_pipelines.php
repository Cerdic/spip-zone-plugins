<?php

function exergue_pre_propre($letexte) {
	
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


?>
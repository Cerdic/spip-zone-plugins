<?php

function exergue_pre_propre($letexte) {
	
	if (preg_match_all(",<(exergue)>(.*)<\/(exergue)>,UimsS",
	$letexte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {		
			$letexte = str_replace($reg[0], "<div class=\"spip_exergue\">" . $reg[2] . "</div>", $letexte);
		}
	}
	return $letexte;
	
}


?>
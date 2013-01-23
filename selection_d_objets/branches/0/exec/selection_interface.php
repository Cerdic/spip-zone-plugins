<?php


function exec_selection_interface() {
		include_spip("inc/utils");
		include_spip("public/assembler");
		$contexte = array('id_rubrique'=>$_GET["id_rubrique"]);

		$p = evaluer_fond("prive/selection_interface", $contexte);
		$ret .= $p["texte"];
		
		
		echo $ret;
		
}		


?>
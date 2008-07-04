<?php


function exec_selection_interface() {
		include_spip("inc/utils");
		$contexte = array('id_rubrique'=>$_GET["id_rubrique"]);

		$p = evaluer_fond("selection_interface", $contexte);
		$ret .= $p["texte"];
		
		
		echo $ret;
		
}		


?>
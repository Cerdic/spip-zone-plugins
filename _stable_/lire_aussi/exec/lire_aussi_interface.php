<?php


function exec_lire_aussi_interface() {
		include_spip("inc/utils");
		$contexte = array('id_article'=>$_GET["id_article"]);

		$p = evaluer_fond("lire_aussi_interface", $contexte);
		$ret .= $p["texte"];
		
		
		echo $ret;
		
}		


?>
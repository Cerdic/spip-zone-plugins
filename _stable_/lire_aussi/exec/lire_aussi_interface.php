<?php


function exec_lire_aussi_interface() {
		include_spip("inc/utils");
		$contexte = array('id_article'=>$_GET["id_article"]);
		$ret .= recuperer_fond("lire_aussi_interface", $contexte);
		
		
		echo $ret;
		
}		


?>
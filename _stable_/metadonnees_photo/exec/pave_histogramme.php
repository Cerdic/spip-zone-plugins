<?php


function exec_pave_histogramme() {
		include_spip("inc/utils");
		
		$fichier = $_GET["fichier"];
//		$fichier = ereg_replace("^"._DIR_IMG, "", $fichier);
		
		$contexte = array('fichier'=>$fichier);

		$p = evaluer_fond("inc_histogramme_small", $contexte);
		$ret .= $p["texte"];
		
		
		echo $ret;
		
}		


?>
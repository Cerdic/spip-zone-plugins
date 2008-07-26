<?php


function exec_pave_exif() {
		include_spip("inc/utils");
		
		$fichier = $_GET["fichier"];
		$compteur = $_GET["compteur"];
//		$fichier = ereg_replace("^"._DIR_IMG, "", $fichier);
		
		$contexte = array('fichier'=>$fichier, 'compteur'=>$compteur);

		$p = evaluer_fond("pave_exif", $contexte);
		$ret .= $p["texte"];
		
		
		echo $ret;
		
}		


?>
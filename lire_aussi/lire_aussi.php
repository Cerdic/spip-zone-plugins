<?php


function lire_aussi_interface($vars="") {
		$exec = $vars["args"]["exec"];
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];


	if ($exec == "articles" AND $id_article > 0) {
		include_spip("inc/utils");


		
		
		$contexte = array('id_article'=>$id_article);

		$ret .= "<div id='pave_lire_aussi'>";
		
		$page = evaluer_fond("lire_aussi_interface", $contexte);
		$ret .= $page["texte"];

		$ret .= "</div>";


	}

	$data .= $ret;
	
	$vars["data"] = $data;

	return $vars;

}

?>
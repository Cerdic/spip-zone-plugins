<?php


function lire_aussi_affiche_milieu($flux="") {
		$exec = $flux["args"]["exec"];
		$id_rubrique = $flux["args"]["id_rubrique"];
		$id_article = $flux["args"]["id_article"];
		$data =	$flux["data"];


	if (($exec == "articles" OR $exec == "article") AND $id_article > 0) {
		include_spip("inc/utils");


		
		
		$contexte = array('id_article'=>$id_article);

		$ret .= "<div id='pave_lire_aussi'>";
		
		$page = evaluer_fond("lire_aussi_interface", $contexte);
		$ret .= $page["texte"];

		$ret .= "</div>";

		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$ret,$p,0);
		else
			$flux['data'] .= $ret;


	}


	return $flux;

}

?>
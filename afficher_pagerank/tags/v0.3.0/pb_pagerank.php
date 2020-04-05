<?php

function pb_pagerank_affiche_gauche($vars=""){
	$exec = $vars["args"]["exec"];
	$id_rubrique = $vars["args"]["id_rubrique"];
	$id_article = $vars["args"]["id_article"];
	$data =	$vars["data"];
	$ret = "";

	if ($exec == "accueil") {
		$url_site = lire_meta("adresse_site");
		$pagerank = afficher_pagerank($url_site);
		$ret .= "<div class='box note'><div style='padding-left: 60px;'>";
		$ret .= "<div>PageRank : <span style='font-weight:bold;color: red;'>$pagerank</div>";
		$ret .= "<div><img src='"._DIR_PLUGIN_PAGERANK."images/pagerank$pagerank.gif' alt='pagerank $pagerank' /></div>";
		$ret .= "</div>";
		$ret .= "</div>";
	}
	$data .= $ret;
	$vars["data"] = $data;
	return $vars;
}

function pb_pagerank_header_prive($flux) {
	if ($_GET["exec"] == "stats_referers" || $_GET["exec"] == "stats_visites") {
		$flux .= '<script type="text/javascript" src="'.find_in_path('pagerank_prive.js').'"></script>';
	}
	return $flux;
}
?>

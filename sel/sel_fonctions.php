<?php

// balise #TOTAL_VISITES
function aff_total_visites() {
	$query = "SELECT SUM(visites) AS total_absolu FROM spip_visites";
	$result = spip_query($query);
	if ($row = spip_fetch_array($result))
		{ return $row['total_absolu']; }
	else { return "0";}
}

function balise_TOTAL_VISITES($p) {
	$p->code = "aff_total_visites()";
	$p->statut = 'php';
	return $p;
}




// balise #CITATION
function aff_citation_hasard() {
	$lignes = file ('./squelettes/citations.txt');
		$hasard = array_rand ($lignes,1);
    		return $lignes[$hasard];

}

function balise_CITATION($c) {
	$c->code = "aff_citation_hasard()";
	$c->statut = 'php';
	return $c;
}


function filtre_arobase ($chaine) {
	$result = explode("@", $chaine);
	return $result[0];
}













?>
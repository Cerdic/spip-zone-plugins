<?php
include_spip('inc/acces_restreint');

// {tout_voir}
function critere_tout_voir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['tout_voir'] = true;
}

/*function critere_archive_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['archive'] = true;
}*/

//
// <BOUCLE(ARTICLES)>
//
function boucle_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_articles_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	/*
	$marchive = $id_table .'.archive';
	if (!$boucle->modificateur['criteres']['archive']) {
		if (!$GLOBALS['var_preview']) {
			$boucle->where[]= array("'='", "'$marchive'", "'\"non\"'");
			//$boucle->where[]= array("'>'", "'$id_table" . ".date_archive'", "'NOW()'");
			$boucle->where[]= array("'($id_table.date_archive > NOW() OR $id_table.date_archive=0)'");
		}
	}
	*/

	return boucle_ARTICLES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(BREVES)>
//
function boucle_BREVES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_breves_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	
	return boucle_BREVES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(FORUMS)>
//
function boucle_FORUMS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_forum_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_FORUMS_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SIGNATURES)>
//
function boucle_SIGNATURES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_signatures_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_SIGNATURES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(DOCUMENTS)>
//
function boucle_DOCUMENTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_documents_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_DOCUMENTS_dist($id_boucle, $boucles);
}

//
// <BOUCLE(RUBRIQUES)>
//
function boucle_RUBRIQUES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_rubriques_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_RUBRIQUES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(HIERARCHIE)>
//
function boucle_HIERARCHIE($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_rubriques_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_HIERARCHIE_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SYNDICATION)>
//
function boucle_SYNDICATION($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_syndic_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_SYNDICATION_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SYNDIC_ARTICLES)>
//
function boucle_SYNDIC_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_syndic_articles_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	return boucle_SYNDIC_ARTICLES_dist($id_boucle, $boucles);

//
// <BOUCLE(EVENEMENTS)>
//
function boucle_EVENEMENTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		if (!in_array($t, $boucles[$id_boucle]->select))
		  $boucle->select[]= $t; # pour postgres, neuneu ici
	
		$boucle->hash = '
		// ACCES RESTREINT
		$acces_where = AccesRestreint_evenements_accessibles_where("'.$t.'");
		' . $boucle->hash ;
	
		// et le filtrage d'acces filtre !
		$boucle->where[] = '$acces_where';
	}
	
	return boucle_EVENEMENTS_dist($id_boucle, $boucles);
}
}

?>
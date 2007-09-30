<?php

// * Acces restreint, plugin pour SPIP * //
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/acces_restreint');
include_spip('inc/acces_restreint');

//include_spip('inc/acces_restreint');

// {tout_voir}
function critere_tout_voir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['tout_voir'] = true;
}

//
// <BOUCLE(ARTICLES)>
//
function boucle_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.id_rubrique';
		$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
		$boucle->where[] = 'AccesRestreint_rubriques_accessibles_where("'.$t.'")';
	}
	return boucle_ARTICLES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(BREVES)>
//
function boucle_BREVES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.id_rubrique';
		$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
		$boucle->where[] = 'AccesRestreint_rubriques_accessibles_where("'.$t.'")';
	}
	return boucle_BREVES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(FORUMS)>
//
function boucle_FORUMS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.id_rubrique';
		$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
		$boucle->where[] = 'AccesRestreint_rubriques_accessibles_where("'.$t.'")';

		$t = $boucle->id_table . '.id_article';
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_articles_accessibles_where("'.$t.'")';

		$t = $boucle->id_table . '.id_breve';
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_breves_accessibles_where("'.$t.'")';
	}
	return boucle_FORUMS_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SIGNATURES)>
//
function boucle_SIGNATURES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])) {
		$t = $boucle->id_table . '.id_article';
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_articles_accessibles_where("'.$t.'")';
	}
	return boucle_SIGNATURES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(DOCUMENTS)> ; ici la notion d'acces est tres discutable
//
function boucle_DOCUMENTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_documents_accessibles_where("'.$t.'")';
	}
	return boucle_DOCUMENTS_dist($id_boucle, $boucles);
}

//
// <BOUCLE(RUBRIQUES)>
//
function boucle_RUBRIQUES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_rubriques_accessibles_where("'.$t.'")';
	}
	return boucle_RUBRIQUES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(HIERARCHIE)>
//
function boucle_HIERARCHIE($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_rubriques_accessibles_where("'.$t.'")';
	}
	return boucle_HIERARCHIE_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SYNDICATION)>
//
function boucle_SYNDICATION($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.id_rubrique';
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_rubriques_accessibles_where("'.$t.'")';
	}
	return boucle_SYNDICATION_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SYNDIC_ARTICLES)>
//
function boucle_SYNDIC_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_syndic_articles_accessibles_where("'.$t.'")';
	}
	return boucle_SYNDIC_ARTICLES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(EVENEMENTS)>
//
function boucle_EVENEMENTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	if (!isset($boucle->modificateur['tout_voir'])){
		$t = $boucle->id_table . '.' . $boucle->primary;
		$boucle->select = array_merge($boucle->select, array($t));
		$boucle->where[] = 'AccesRestreint_evenements_accessibles_where("'.$t.'")';
	}
	return boucle_EVENEMENTS_dist($id_boucle, $boucles);
}


?>
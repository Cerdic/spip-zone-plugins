<?php

$GLOBALS['contrib_local'] = 13;
$GLOBALS['contrib_non_local'] = 14;
$GLOBALS['contrib_analyse'] = 24;

/*
 * <BOUCLE(OP_RUBRIQUES)>
 */

function boucle_OP_RUBRIQUES_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_op_rubriques";
        return calculer_boucle($id_boucle, $boucles);
}

function balise_CONTRIBUTIONS_LOCALES($p) {
	$p->code = 'return_contrib_local()';
	$p->statut = 'php';
	return $p;
}

function balise_CONTRIBUTIONS_NON_LOCALES($p) {
	$p->code = 'return_contrib_non_local()';
	$p->statut = 'php';
	return $p;
}

function balise_CONTRIBUTIONS_ANALYSES($p) {
	$p->code = 'return_analyse()';
	$p->statut = 'php';
	return $p;
}

function return_contrib_local() {
	$reponse = $GLOBALS['contrib_local'];
	return $reponse;
}
function return_contrib_non_local() {
	$reponse = $GLOBALS['contrib_non_local'];
	return $reponse;
}
function return_analyse() {
	$reponse = $GLOBALS['contrib_analyse'];
	return $reponse;
}

function getAuteurOpName($id_article) {
	$query = "SELECT nom FROM spip_op_auteurs WHERE id_article = '$id_article'";
	$result = spip_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function getAuteurOpMail($id_article) {
	$query = "SELECT email FROM spip_op_auteurs WHERE id_article = '$id_article'";
	$result = spip_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function getAuteurOpPhone($id_article) {
	$query = "SELECT phone FROM spip_op_auteurs WHERE id_article = '$id_article'";
	$result = spip_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
	}

function getAuteurOpGroup($id_article) {
	$query = "SELECT group_name FROM spip_op_auteurs WHERE id_article = '$id_article'";
	$result = spip_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

?> 

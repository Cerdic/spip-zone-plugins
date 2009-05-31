<?php
/* *********************************************************************
   *
   * Copyright (c) 2006-2008
   * Xavier Burot
   * fichier : public/genea_criteres.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

// -- Definition de criteres supplementaires ----------------------------

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// {importance xxx} permet de classer par importance un champ de la table
//
function critere_importance_dist($idb, &$boucles, $crit){
//	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
//	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
//		$type=$r[1];
//		$op=$r[2];
//		$op_val=$r[3];
//	}
	$champ = $boucle->id_table . '.' . $type;
	$boucle->select[] = 'COUNT('.$champ.') AS importance';
	$boucles[$idb]->group[] = $champ;
}

//
// {initial yyyy x} permet de filter un champs par son initial ----------
//
// FONCTION EN SUSPENS CAR PB DE RECUPERATION DE LA VALEUR DE #GET{lettre}
// A LA PLACE D'UNE LETTRE.
function critere_initial_dist($idb, &$boucles, $crit){
	$tbl_initial = array(
		'?' => '?',
		'A' => 'aA',
		'B' => 'bB',
		'C' => 'cC',
		'D' => 'dD',
		'E' => 'eE',
		'F' => 'fF',
		'G' => "gG",
		'H' => "hH",
		'I' => "iI",
		'J' => "jJ",
		'K' => "kK",
		'L' => "lL",
		'M' => "mM",
		'N' => "mN",
		'O' => "oO",
		'P' => "pP",
		'Q' => "qQ",
		'R' => "rR",
		'S' => "sS",
		'T' => "tT",
		'U' => "uU",
		'V' => "vV",
		'W' => "wW",
		'X' => "xX",
		'Y' => "yY",
		'Z' => "zZ");
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	if (count($params) < 1)	erreur_squelette(_T('genea:zbug_manque_parametre_initial'), "BOUCLE$idb");
	$params = array_shift($params);
	//$champs = ($params[0]->type = 'texte') ? calculer_liste(array($params[0]), array(), $boucles, $boucle->parent) :
	list($champs, $val) = split('[=]', $params[0]->texte);
	if (isset($params[1])) {
		$val = "'".calculer_liste(array($params[1]), array(), $boucles, $boucle->parent)."'";
	}
	$val = strtoupper($val); // Passage en majuscule pour appel du tableau de valeur
	//if (($val<'A') || ($val>'Z') && ($val!='?')) erreur_squelette(_T('genea:zbug_initial_non conforme'), "BOUCLE$idb");
	//echo "$champs - $val<br />";
	$table = $boucle->id_table;
	$where = array("'REGEXP'", "'$table.$champs'", "'\'^[". $tbl_initial[$val]. "]\''");
	//print_r ($where); echo "<br />";
	$boucle->where[] = ($crit->not ? array("'NOT'", $where) : $where);
}

//
// Surcharge du critere PAR de base afin de prendre en compte la boucle FOR
// Christian Lefebvre, Oct. 2005 - Distribué sous licence GPL
//
function critere_par($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if($boucle->type_requete!='for') {
		return critere_par_dist($idb, &$boucles, $crit);
	}
	$tri= $crit->param[0];
	if ($tri[0]->type != 'texte') {
		$par =
			calculer_liste($tri, array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		$par = array_shift($tri);
		$par = $par->texte;
	}
	if ($crit->not) $par="!$par";
	$boucle->order=array($par);
}
?>
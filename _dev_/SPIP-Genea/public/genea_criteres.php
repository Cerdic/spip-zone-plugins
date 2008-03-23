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
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$champ = $boucle->id_table . '.' . $type;
	$boucle->select[] = 'COUNT('.$champ.') AS importance';
	$boucles[$idb]->group[] = $champ;
}

function critere_initial_dist($idb, &$boucles, $crit){
	$tbl_initial = array(
		'A' => 'aA',
		'B' => 'bB',
		'C' => 'cC',
		'D' => 'dD',
		'E' => 'eE',
		'F' => 'fF',
		'G' => "gG",
		'G' => "hH",
		'G' => "iI",
		'G' => "jJ",
		'G' => "kK",
		'G' => "lL",
		'G' => "mM",
		'G' => "mN",
		'G' => "oO",
		'G' => "pP",
		'G' => "qQ",
		'G' => "rR",
		'G' => "sS",
		'G' => "tT",
		'G' => "uU",
		'G' => "vV",
		'G' => "wW",
		'G' => "xX",
		'G' => "yY",
		'G' => "zZ");
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
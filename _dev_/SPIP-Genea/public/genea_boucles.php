<?php
/* *********************************************************************
   *
   * Copyright (c) 2006-2008
   * Xavier Burot
   * fichier : public/genea_boucles.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

// -- Definition des boucles utilisables --------------------------------

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/genea_base');

//
// <BOUCLE(GENEA)>
//
function boucle_GENEA_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = 'spip_genea';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_INDIVIDUS)>
//
function boucle_GENEA_INDIVIDUS_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = 'spip_genea_individus';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_FAMILLES)>
//
function boucle_GENEA_FAMILLES_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = 'spip_genea_familles';
	if (isset($boucle->modificateur['criteres']['id_individu'])) {
		$boucle->from[] = "spip_genea_individus AS indiv";
		$where = array("'OR'",
			array("'='", "'$id_table.id_epoux'", "'indiv.id_individu'"),
			array("'='", "'$id_table.id_epouse'", "'indiv.id_individu'"));
		$boucle->where[] = ($crit->not ? array("'NOT'", $where) : $where);
	}
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_EVT)>
//
function boucle_GENEA_EVT_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = 'spip_genea_evt';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_SOURCES)>
//
function boucle_GENEA_SOURCES_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = 'spip_genea_sources';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(FOR)>
// Christian Lefebvre, Oct. 2005 - Distribué sous licence GPL
//
function boucle_FOR_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	//var_export($boucle);
	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

	$debut=1;
	$fin=null;

	foreach($boucle->criteres as $critere) {
	  if($critere->op!='=') continue;
	  $val= calculer_liste($critere->param[1],
						   array(), $boucles, $boucle->id_parent);

	  switch($critere->param[0][0]->texte) {
	  case 'debut': $debut= $val; break;
	  case 'fin'  : $fin  = $val; break;
	  }
	}

	if($fin===null) {
	  erreur_squelette("genea:zbug_fin_non_definie",
					   $boucle->id_boucle);
	}
	//echo "\nboucle_FOR($debut, $fin, $pas)\n";

	if(count($boucle->order)==0) {
		$pas=1;
		$op1='<=';
		$op2='+=';
	} elseif($boucle->order[0]{0}=='!') {
		$pas= substr($boucle->order[0], 1);
		$op1='>=';
		$op2='-=';
		$zz=$debut;
		$debut= $fin;
		$fin= $zz;
	} else {
		$pas= $boucle->order[0];
		$op1='<=';
		$op2='+=';
	}

	$code=<<<CODE
	\$code=array();
	for(\$i=$debut; \$i$op1$fin; \$i$op2$pas) {
	\$SP++;
		\$Numrows['$id_boucle']['compteur_boucle']=\$i;
		\$code[]=$boucle->return;
	\$SP--;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

?>
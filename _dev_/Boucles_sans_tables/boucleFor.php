<?php

/** BOUCLE FOR
 * Christian Lefebvre, Oct. 2005
 * Distribué sous licence GPL
 */

$GLOBALS['tables_principales']['spip_for'] =
	array('field' => array(
			 "debut" => "int",
			 "fin" => "int"), 'key' => array());
$GLOBALS['table_des_tables']['for'] = 'for';

function critere_par($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if($boucle->type_requete!='for') {
		return critere_par_dist($idb, $boucles, $crit);
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

function boucle_FOR($id_boucle, &$boucles) {
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
	  erreur_squelette("pas de fin définie",
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

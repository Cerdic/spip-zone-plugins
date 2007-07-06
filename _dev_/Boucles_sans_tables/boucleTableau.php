<?php

/** BOUCLE TABLEAU
 * Christian Lefebvre, Oct. 2005
 * Distribué sous licence GPL 2
 *
 * on accepte les critères {var=...} pour aller chercher le contenu d'une
 * variable globale, {fonction=...} pour appeler une fonction ou {valeur} pour
 * utiliser la valeur d'une boucle tableau englobante.
 */

$tableau = array(
	"var" => "varchar(100)",
	"fonction" => "varchar(100)",
	"cle" => "varchar(100)",
	"valeur" => "varchar(100)"
);
$tableau_key = array(
	"PRIMARY KEY"	=> "cle"
);

$GLOBALS['tables_principales']['spip_tableau'] =
	array('field' => &$tableau, 'key' => &$tableau_key);
$GLOBALS['table_des_tables']['tableau'] = 'tableau';

$GLOBALS['tables_principales']['spip_affecter'] =
	array('field' => &$tableau, 'key' => array());
$GLOBALS['table_des_tables']['affecter'] = 'affecter';

// A REMPLACER PAR CA ?
// $GLOBALS['tables_des_serveurs_sql']['']['tableau'] =
// 	array('field' => &$tableau, 'key' => &$tableau_key);
// $GLOBALS['tables_des_serveurs_sql']['']['affecter'] =
// 	array('field' => $tableau, 'key' => $tableau_key);
// MAIS POURQUOI CA MARCHE PAS ?

function boucle_TABLEAU($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'" . ereg_replace("'","\'",join('',$boucle->separateur))
				  . "'");
	else
	  $code_sep="''";

	if($boucle->limit) {
		list($start,$end)=explode(',', $boucle->limit);
		$end= "($start+$end>count(\$__t)?count(\$__t):$start+$end)";
		$total= "($end-$start)";
	} else {
		$start='0'; $total= $end= 'count($__t)';
	}

	if($boucle->mode_partie) {
		$start= $start."+$boucle->partie-1";
		$incr=$boucle->total_parties;
		$total="floor(($total+$incr-".$boucle->partie.")/$incr)";
	} else {
		$incr=1;
	}
	$init=null; $var=null; $cle='';

	foreach($boucle->criteres as $critere) {
	  if($critere->op=='valeur') {
		$var= '$Pile[$SP][\'valeur\']';
		$init= "\$__t= &$var;";
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='valeur') {
		$var= calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent);
		$init= "\$__t= &$var;";
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='var') {
		$var= '$GLOBALS['.calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).']';
		$init= "\$__t= &$var;";
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='fonction') {
		$init= "eval('\$__t='.".calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).".';');";
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='cle') {
		$cle.= '['.calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).']';
	  }
	}

	if($init===null) {
	  erreur_squelette("pas de variable s&eacute;lectionn&eacute;e",
					   $boucle->id_boucle);
	  return;
	}

	if($cle) {
		$init.="\$__t= \$__t$cle;";
	}

	// s'il y a des limites ou un increment, il faut ruser
	if($boucle->limit || $boucle->mode_partie) {
		$code=<<<CODE
	$init
	\$SP++;
	if(!is_array(\$__t) || empty(\$__t)) { return ''; }
	\$__t_k= array_keys(\$__t);
	\$code=array();
	\$Pile[\$SP]['var']=&\$__t;
	\$Numrows['$id_boucle']['total']=$total;
	\$Numrows['$id_boucle']['grand_total']=count(\$__t);
	for(\$i= $start; \$i<$end; \$i+=$incr) {
		\$Numrows['$id_boucle']['compteur_boucle']= \$i;
		\$Pile[\$SP]['cle']= \$__t_k[\$i];
		\$Pile[\$SP]['valeur']= \$__t[\$__t_k[\$i]];
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;
	// sinon, un brave foreach fait l'affaire
	} else {
		$code=<<<CODE
	$init
	error_log("__t=".var_export(\$__t, 1));
	\$SP++;
	if(!is_array(\$__t) || empty(\$__t)) { return ''; }
	\$code=array();
	\$Pile[\$SP]['var']=&\$__t;
	\$i= 1;
	\$Numrows['$id_boucle']['total']=$total;
	\$Numrows['$id_boucle']['grand_total']=count(\$__t);
	foreach(\$__t as \$k => \$v) {
		\$Numrows['$id_boucle']['compteur_boucle']=\$i++;
		\$Pile[\$SP]['cle']=\$k;
		\$Pile[\$SP]['valeur']=\$v;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;
	}
	return $code;
}

function boucle_AFFECTER($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'" . ereg_replace("'","\'",join('',$boucle->separateur))
				  . "'");
	else
	  $code_sep="''";

	$var=null; $cle='';

	foreach($boucle->criteres as $critere) {
	  if($critere->op=='=' && $critere->param[0][0]->texte=='var') {
		$var= '$GLOBALS['.calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).']';
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='cle') {
		$cle.= '['.calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).']';
	  }
	}

	if($var===null) {
	  erreur_squelette("pas de variable s&eacute;lectionn&eacute;e",
					   $boucle->id_boucle);
	  return;
	}

	$code=<<<CODE
	\$__t= &${var}$cle;
	\$SP++;
	\$__t=$boucle->return;
	return '';
CODE;

	return $code;
}

function balise_TABLEAU($p) {
	$var=null; $cle='';

	if ($p->param && !$p->param[0][0]) {
		$var=  $p->param[0][1][0]->texte;
		if($var=='valeur') {
			$var= '$Pile[$SP][\'valeur\']';
		} else {
			$var= "\$GLOBALS['$var']";
		}

		// les cles
		foreach(array_slice($p->param[0], 2) as $pp) {
			$cle.= '['.calculer_liste($pp,
				$p->descr, $p->boucles, $p->id_boucle).']';
		}
	} else {
	  erreur_squelette("pas de variable s&eacute;lectionn&eacute;e dans balise TABLEAU",
					   $boucle->id_boucle);
	  return;
	}
	$p->code = "(${var}$cle)";
	$p->interdire_scripts = true;
	return $p;
}

function balise_AFFECTER($p) {
	$var=null; $cle='';

	if ($p->param && !$p->param[0][0]) {
		$var=  $p->param[0][1][0]->texte;

		// les cles
		foreach(array_slice($p->param[0], 2) as $pp) {
			$cle.= '['.calculer_liste($pp,
				$p->descr, $p->boucles, $p->id_boucle).']';
		}
	} else {
	  erreur_squelette("pas de variable s&eacute;lectionn&eacute;e dans balise AFFECTER",
					   $boucle->id_boucle);
	  return;
	}

	if($var{0}=='+') {
		$var= substr($var, 1);
		$complement= '[]';
	} else {
		$complement= '';
	}

	$p->code = "((\$GLOBALS['$var']$cle$complement="
		.calculer_liste($p->avant, $p->descr, $p->boucles, $p->id_boucle).'.'
		.calculer_liste($p->apres, $p->descr, $p->boucles, $p->id_boucle).")?'':'')";
	$p->interdire_scripts = false;
	return $p;
}

function champ($tableau, $champ) {
  return ($tableau[$champ])?$tableau[$champ]:'';
}

function toto() {
  return array('aze', 'qsd', 'wxc');
}

?>

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

function boucle_TABLEAU($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'" . ereg_replace("'","\'",join('',$boucle->separateur))
				  . "'");
	else
	  $code_sep="''";

	$var=null; $cle='';

	foreach($boucle->criteres as $critere) {
	  if($critere->op=='valeur') {
		$var= '$Pile[$SP][\'valeur\']';
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='var') {
		$var= '$GLOBALS['.calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).']';
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='fonction') {
		$var= calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent);
	  } elseif($critere->op=='=' && $critere->param[0][0]->texte=='cle') {
		$cle.= '['.calculer_liste($critere->param[1],
			array(), $boucles, $boucle->id_parent).']';
	  }
	}

	if($var===null) {
	  erreur_squelette("pas de variable sélectionnée",
					   $boucle->id_boucle);
	  return;
	}

	$code=<<<CODE
	\$__t= ${var}$cle;
	\$SP++;
	if(empty(\$__t)) { return ''; }
	\$code=array();
	\$Pile[\$SP]['var']=&\$__t;
	foreach(\$__t as \$k => \$v) {
		\$Numrows['$id_boucle']['compteur_boucle']=\$i;
		\$Pile[\$SP]['cle']=\$k;
		\$Pile[\$SP]['valeur']=\$v;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

function champ($tableau, $champ) {
  return ($tableau[$champ])?$tableau[$champ]:'';
}

function toto() {
  return array('aze', 'qsd', 'wxc');
}

?>

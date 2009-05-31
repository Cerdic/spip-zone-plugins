<?php

/** BOUCLE IF
 * Christian Lefebvre, Oct. 2005
 * Distribué sous licence GPL
 */

$GLOBALS['tables_principales']['spip_if'] =
	array('field' => array(), 'key' => array());
$GLOBALS['table_des_tables']['if'] = 'if';

function boucle_IF($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	//var_export($boucle);

	$code=<<<CODE

	if(eval("return ".$boucle->where.";")) {
		\$SP++;
		return $boucle->return;
	}
CODE;

	return $code;
}

function balise_IF($p) {
	if (!$p->param || $p->param[0][0]) {
	  erreur_squelette("pas de condition dans balise IF", $boucle->id_boucle);
	}
	$var=  calculer_liste($p->param[0][1],
						  $p->descr, $p->boucles, $p->id_boucle);
	$var= addcslashes($var, "\\'");
	$code=
		calculer_liste($p->avant,
					   $p->descr, $p->boucles, $p->id_boucle)
		.'.'.
		calculer_liste($p->apres,
					   $p->descr, $p->boucles, $p->id_boucle);
	$p->avant= $p->apres= "";
	$p->code= "'<'.'?php /*DEBUT IF*/ if($var) { ?'.'>'.". $code .".'<'.'?php } /*FIN IF*/ ?'.'>'";
	$p->interdire_scripts = false;
	return $p;
}

function critere_condition($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if($boucle->type_requete=='if') {
	  $boucle->where = calculer_liste($crit->param[0], array(),
									  $boucles, $boucles[$idb]->id_parent);
	} else {
	  error("Heu ...");
	}
}

?>

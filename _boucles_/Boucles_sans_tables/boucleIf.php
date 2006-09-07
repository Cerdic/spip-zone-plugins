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
		//\$SP++;
		return $boucle->return;
	}
CODE;

	return $code;
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

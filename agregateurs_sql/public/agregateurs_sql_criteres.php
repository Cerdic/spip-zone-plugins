<?php

/**  Critere {is_null champ} - Franck Van Lancker - */
function critere_is_null($idb, &$boucles, $crit)
{
	
	$not = ($crit->not ? ' NOT' : '');
	$param = "IS$not NULL";
	$boucle = &$boucles[$idb];
	$field = $crit->param[0][0]->texte;
	
	if( preg_match('~\s~si', $field) )
		erreur_squelette(_T('zbug_info_erreur_squelette'), $crit->op);
	
	$boucle->where[]= array("'$param'", "'$boucle->id_table." . "$field'", "''");
}

/* $func : array(FUNC => balise) */
function calcul_critere_fonctions($func, $idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$_fusion = calculer_liste($crit->param[1], array(), $boucles, $boucle->id_parent);

	$params = $crit->param;
	$champ = reset($params);
	$champ = $champ[0]->texte;

	// option DISTINCT {compte DISTINCT(id_article) }
	$filter="";
	if (preg_match('/^([a-zA-Z]+)\(\s*([a-zA-Z_]+)\s*\)$/', trim($champ), $r)) {
		$filter = $r[1]; // DISTINCT
		$champ = $r[2]; // id_article
	}
	
	$sel = $filter ? "$filter($champ)" : $champ;
	foreach ($func as $f => $as) {
		$boucle->select[]= "$f($sel) AS $as" . "_$champ";
	}
}


?>
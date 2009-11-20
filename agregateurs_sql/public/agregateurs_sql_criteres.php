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



?>
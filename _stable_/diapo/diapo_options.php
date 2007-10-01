<?php
function critere_diapo($idb, &$boucles, $crit) {
	$idb_pag=$boucles[substr($idb,0,-6)]->modificateur['debut_nom']?$boucles[substr($idb,0,-6)]->modificateur['debut_nom']:substr($idb,0,-6);
	$idb_diapo='intval(_request("diapo".'.$idb_pag.'))';
	$idb_debut='intval(_request("debut".'.$idb_pag.'))';
	$pas = !isset($crit->param[0]) ? "''" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);

	$pas = ($pas== "''") ? '10' : "((\$a = intval($pas)) ? \$a : 10)";
	
	$boucle = &$boucles[$idb];
	$boucle->mode_partie = 'p+';
	$boucle->partie = '((('.$idb_diapo.'<('.$idb_debut.'+'.$pas.')) && ('.$idb_diapo.'>='.$idb_debut.'))' .
			//diapo selectionnee dans la plage de pagination
			'?'.$idb_diapo.
			//diapo selectionnee hors plage de pagination
			':'.$idb_debut.')';
	
	$boucle->modificateur['debut_nom'] = $idb_pag;
	
	$boucle->total_parties = '1';
	$boucle->fragment = 'fragment_'.$boucle->descr['nom'].$idb;
}
function diapo_seq($id, $max) {
	return ($id<$max)?$id:0;
}
?>
<?php

include_spip('inc/op_actions');

// {openPublishing}
function critere_openPublishing($idb, &$boucles, $crit) {
	
        $boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	$id_rubrique = $id_table.'.id_rubrique';

	$list = '';
	foreach (lire_config('op') as $key => $val) {
		if ((substr($key,0,3)) == "op_") {
			if ($val == "openPublishing") {
				$op_rub = substr($key,3,1);
				$list .= $op_rub .',';
			}
		}
	}
	$list = substr($list,0,strlen($list)-1);
	$boucle->where[] = array("'IN'", "'$id_rubrique'","'($list)'");
}

function balise_AGENDA($p) {
	$p->code ='return_agenda()';
	$p->statut = 'php';
	return $p;
}

function return_agenda() {
	$config = lire_config('op');
	return $config["RubAgenda"];
}
?>
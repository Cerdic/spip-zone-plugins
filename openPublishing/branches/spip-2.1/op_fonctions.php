<?php

// {openPublishing}
function critere_openPublishing($idb, &$boucles, $crit) {
	
        $boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	$id_rubrique = $id_table.'.id_rubrique';

	$list = '';
	foreach (lire_config('op') as $key => $val) {
		if ((substr($key,0,3)) == "op_") {
			if ($val == "openPublishing") {
				$op_rub = substr($key,3);
				$list .= $op_rub .',';
			}
		}
	}
	$list = substr($list,0,strlen($list)-1);
	$boucle->where[] = array("'IN'", "'$id_rubrique'","'($list)'");
}

// {openKey}
function critere_openKey($idb, &$boucles, $crit) {
	
        $boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;

	$id_groupe = $id_table.'.id_groupe';

	$list = '';
	foreach (lire_config('op') as $key => $val) {
		if ((substr($key,0,7)) == "groupe_") {
			if ($val == "openPublishing") {
				$op_rub = substr($key,7);
				$list .= $op_rub .',';
			}
		}
	}
	$list = substr($list,0,strlen($list)-1);
	$boucle->where[] = array("'IN'", "'$id_groupe'","'($list)'");
}

function return_agenda() {
	$config = lire_config('op');
	return $config["RubAgenda"];
}
function balise_AGENDA($p) {
	$p->code ='return_agenda()';
	$p->statut = 'php';
	return $p;
}

/* indispensable lorsque aucun plugin ne viens ce brancher. renvoie une chaine vide
   ce n'est pas propre ... mais bon.*/
function op_OP_squelette($flux) {
	return '';
}

?>

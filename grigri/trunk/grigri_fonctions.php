<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/*
 * balise TGRIGRI
 *
 * @arg1 = objet
 * @arg2 = liste des grigri separes par un espace, ex : grigri1 grigri2
 * @arg3 = orderBy, ex : id_rubrique DESC
 * @arg4 = forcerArray, si retour id_objet est unique, return un array
 *
 * ex : #TGRIGRI{rubrique, grigri1 grigri2, id_rubrique DESC}
 *
 * return Array des id_objet sauf si 1 seul id_objet (string)
 */
function balise_TGRIGRI_dist($p){
	$objet        = interprete_argument_balise (1, $p);
	$Lgrigri      = interprete_argument_balise (2, $p);
	$orderBy      = interprete_argument_balise (3, $p);
	$forcerArray = interprete_argument_balise (4, $p);

	$objet   = str_replace("'" , "", $objet);
	$Tgrigri = explode(' ', trim(str_replace("'" , "", $Lgrigri)));
	$orderBy = str_replace("'" , "", $orderBy);

	if ($objet and count($Tgrigri)) {
		$table = table_objet_sql($objet);
		$id    = id_table_objet($objet);
		$where = sql_in('grigri', $Tgrigri);
		$res   = sql_allfetsel($id, $table, $where, '', $orderBy);
		$res = array_column($res,$id);
		$L = implode(',', $res);
		if (count($res) == 1 and !$forcerArray) {
			$p->code = '"'.$res[0].'"';
		} else {
			$p->code = 'array(' . implode(', ', $res) . ')';
		}
		$p->interdire_scripts = false;
	}
	return $p;
}


function tables_grigri(){
	$T = array();
	foreach (lister_tables_objets_sql() as $k => $d ) {
		if (array_key_exists('grigri',$d['field'])){
			$T[] = $k;
		}
	}
	return $T;
}

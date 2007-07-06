<?php

include_spip('inc/op_actions');

/*
 * <BOUCLE(op_rubriques)>
 */

function boucle_OP_RUBRIQUES_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;

        $boucle->from[$id_table] =  "spip_op_rubriques";
	$boucle->from["rubriques"] =  "spip_rubriques";
	$boucle->where[]= array("'='", "'rubriques.id_rubrique'", "'$id_table.op_rubrique'");
	$boucle->group[] = $boucle->id_table . '.' . $boucle->primary;

        return calculer_boucle($id_boucle, $boucles);
}

/*
 * <BOUCLE(op_auteurs)>
 */

function boucle_OP_AUTEURS_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_op_auteurs";
        return calculer_boucle($id_boucle, $boucles);
}

function balise_AGENDA($p) {
	$p->code ='return_agenda()';
	$p->statut = 'php';
	return $p;
}

function return_agenda() {
	$reponse = op_get_rubrique_agenda();
	return $reponse;
}
?>
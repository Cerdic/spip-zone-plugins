<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * critere {orphelins} selectionne les sélections sans liens avec un objet éditorial
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SELECTIONS_orphelins_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not?"":"NOT";

	$select = sql_get_select("DISTINCT id_selection","spip_selections_liens as oooo");
	$where = "'".$boucle->id_table.".id_selection $not IN ($select)'";
	if ($cond){
		$_quoi = '@$Pile[0]["orphelins"]';
		$where = "($_quoi)?$where:''";
	}

	$boucle->where[]= $where;
}

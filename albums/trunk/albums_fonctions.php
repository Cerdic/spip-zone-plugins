<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * critere {orphelins} selectionne les albums sans liens avec un objet editorial
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_orphelins_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not?"":"NOT";

	$select = sql_get_select("DISTINCT id_album","spip_albums_liens as oooo");
	$where = "'".$boucle->id_table.".id_album $not IN ($select)'";
	if ($cond){
		$_quoi = '@$Pile[0]["orphelins"]';
		$where = "($_quoi)?$where:''";
	}

	$boucle->where[]= $where;
}

// retire un element d une liste concatenee
function supprimer_element_concat($balise, $element, $delimiteur){
	$element = (string)$element;
	$delimiteur = (string)$delimiteur;
	// tranforme la liste en tableau
	$tableau = explode($delimiteur, $balise);
	// supprime l element du tableau
	unset($tableau[array_search($element, $tableau)]);
	// recree la liste
	$balise = implode($delimiteur, $tableau);
	
	return $balise;
}

// renvoie un champ d un objet
function champ($balise, $champ, $id) {
	$id_objet = 'id_' . $balise;
	$table_objet = 'spip_' . $balise . 's';
	$trouver_table = charger_fonction('trouver_table', 'base');
	$test_table = $trouver_table($table_objet);
	
	if (isset($test_table)){
		$fetch_titre = sql_select($champ,"$table_objet","$id_objet=$id");
		$table_titre = sql_fetch($fetch_titre);
		$titre = $table_titre['titre'];
		
		return $titre;
	} else {
		return false;
	}
}

?>

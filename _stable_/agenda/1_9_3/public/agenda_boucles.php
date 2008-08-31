<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

//
// <BOUCLE(EVENEMENTS)>
//
function boucle_EVENEMENTS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	if (!isset($boucle->modificateur['criteres']['statut'])) {
		// Restreindre aux elements publies
		// uniquement les evenements d'un article publie
		if (!$GLOBALS['var_preview'])
			if (!isset($boucle->modificateur['lien']) AND !isset($boucle->modificateur['tout'])
			AND (!isset($boucle->lien) OR !$boucle->lien) AND (!isset($boucle->tout) OR !$boucle->tout)) {
				$boucle->from["articles"] =  "spip_articles";
				$boucle->where[]= array("'='", "'articles.id_article'", "'$id_table.id_article'");
				$boucle->where[]= array("'='", "'articles.statut'", "'\"publie\"'");
			}
	}
	return calculer_boucle($id_boucle, $boucles); 
}


?>
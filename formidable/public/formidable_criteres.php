<?php


	// {tri_donnee champ}
/**
 * Depuis la boucle FORMULAIRES_REPONSES, trier les résulats en fonction d'un champ
 * de la table FORMULAIRES_REPONSES_CHAMPS
 * {tri_selon_donnee} 
 *
 * @global array $exceptions_des_tables
 * @param string $idb
 * @param array $boucles
 * @param <type> $crit
 * 
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 */

function critere_tri_selon_donnee_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$t = $boucle->id_table;
	
	if ($t=='formulaires_reponses'){
		$not = $crit->not;
		$_quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		$k = count($boucle->join)+1;
		$boucle->join[$k]= array($t,'id_formulaires_reponse');
		$boucle->from["L$k"]= 'spip_formulaires_reponses_champs';
		$op = array("'='", "'L$k.nom'", "_q(".$_quoi.")");
		$boucle->where[]= array("'?'","!in_array($_quoi,array('date','id_formulaires_reponse'))",$op,"''");
		$boucle->order[]= "(in_array($_quoi,array('date','id_formulaires_reponse'))?'$t.'.$_quoi:(strncmp($_quoi,'date_',5)==0?'STR_TO_DATE(L$k.valeur,\'%d/%m/%Y\')':'L$k.valeur'))".($not?".' DESC'":"");
	}
}

?>

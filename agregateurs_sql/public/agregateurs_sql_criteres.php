<?php
/**
 * http://www.spip-contrib.net/Classer-les-articles-par-nombre-de#forum409210
 * Permet de faire un comptage par table liee
 * exemple
 * <BOUCLE1(AUTEURS){compteur articles}{par compteur_articles}>
 * #ID_AUTEUR : #COMPTEUR{articles}
 * </BOUCLE1>
 * pour avoir les auteurs classes par articles et le nombre d'article de chacun
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_compteur($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	
	$_fusion = calculer_liste($crit->param[1], array(), $boucles, $boucle->id_parent);
	$params = $crit->param;
	$table = reset($params);
	$table = $table[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$table,$r)){
		$table=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type = objet_type($table);
	$type_id = id_table_objet($type);
	$table_sql = table_objet_sql($type);
	
	
	$trouver_table = charger_fonction('trouver_table','base');
	$arrivee = array($table, $trouver_table($table, $boucle->sql_serveur));
	$depart = array($boucle->id_table,$trouver_table($boucle->id_table, $boucle->sql_serveur));

	if ($compt = calculer_jointure($boucle,$depart,$arrivee)){
		if ($_fusion!="''"){
			// en cas de jointure, on ne veut pas du group_by sur la cle primaire !
			// cela casse le compteur !
			foreach($boucle->group as $k=>$group)
				if ($group == $boucle->id_table.'.'.$boucle->primary)
					unset($boucle->group[$k]);
			$boucle->group[] = '".($gb='.$_fusion.')."';
		}

		$boucle->select[]= "COUNT($compt.$type_id) AS compteur_$table";	
		if ($op)
			$boucle->having[]= array("'".$op."'", "'compteur_".$table."'",$op_val);
	}
}


?>
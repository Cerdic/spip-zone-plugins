<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

/* le critere {tableau ...} des boucles pour:POUR */
function critere_POUR_tableau_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0])){
		$table = calculer_liste($crit->param[0], array(), $boucles, $boucle->id_parent);
		$boucle->having[]=array("'tableau'",$table);
	}
}

/* le critere {si ...} des boucles condition:CONDITION */
function critere_CONDITION_si_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0])){
		$si = calculer_liste($crit->param[0], array(), $boucles, $boucle->id_parent);
		$boucle->having[]='($test='.$si.')?array(\'tableau\',\'1:1\'):\'\'';
	}
}

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
	$op = false;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$table,$r)){
		$table=$r[1];
		if (count($r)>=3) $op=$r[2];
		if (count($r)>=4) $op_val=$r[3];
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

/**
 * {tri [champ_par_defaut][,sens_par_defaut][,nom_variable]}
 * champ_par_defaut : un champ de la table sql
 * sens_par_defaut : -1 ou inverse pour decroissant, 1 ou direct pour croissant
 * nom_variable : nom de la variable utilisee (par defaut tri_nomboucle)
 * 
 * {tri titre}
 * {tri titre,inverse}
 * {tri titre,-1}
 * {tri titre,-1,truc}
 * 
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_tri_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];

	// definition du champ par defaut
	$_champ_defaut = !isset($crit->param[0][0]) ? "''" : calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucle->id_parent);
	$_sens_defaut = !isset($crit->param[1][0]) ? "1" : calculer_liste(array($crit->param[1][0]), array(), $boucles, $boucle->id_parent);
	$_variable = !isset($crit->param[2][0]) ? "'$idb'" : calculer_liste(array($crit->param[2][0]), array(), $boucles, $boucle->id_parent);

	$_tri = "((\$t=(isset(\$Pile[0]['tri'.$_variable]))?\$Pile[0]['tri'.$_variable]:$_champ_defaut)?preg_replace(',[^\w],','',\$t):'')";
	
	$_sens_defaut = "(is_array(\$s=$_sens_defaut)?(isset(\$s[\$st=$_tri])?\$s[\$st]:reset(\$s)):\$s)";
	$_sens ="((intval(\$t=(isset(\$Pile[0]['sens'.$_variable]))?\$Pile[0]['sens'.$_variable]:$_sens_defaut)==-1 OR \$t=='inverse')?-1:1)";

	$boucle->modificateur['tri_champ'] = $_tri;
	$boucle->modificateur['tri_sens'] = $_sens;
	$boucle->modificateur['tri_nom'] = $_variable;
	// faut il inserer un test sur l'existence de $tri parmi les champs de la table ?
	// evite des erreurs sql, mais peut empecher des tri sur jointure ...
	$boucle->hash .= "
	\$senstri = '';
	\$tri = $_tri;
	if (\$tri){
		\$senstri = $_sens;
		\$senstri = (\$senstri<0)?' DESC':'';
	};
	";
	$boucle->order[] = "\$tri.\$senstri";
}

?>
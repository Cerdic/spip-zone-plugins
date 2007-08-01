<?php
function critere_frequence($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'freq.id_'.$type;
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$boucle->select[]= 'COUNT('.$type_id.') AS frequence';
	$boucle->from['freq']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'freq.".$boucle->primary."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'frequence'",$op_val);
	
}
function critere_popularite($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'pop.id_'.$type;
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$boucle->select[]= 'SUM('.$type_id.') AS popularite_relative';
	$boucle->from['pop']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'pop.".$boucle->primary."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'popularite_relative'",$op_val);
	
}
function critere_frequence_branche($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'freq.id_'.$type;
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$boucle->select[]= 'COUNT('.$type_id.') AS frequence';
	$boucle->from['freq']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'freq.".$boucle->primary."'");
	$boucle->from['lf']="spip_".$type."s";
	$boucle->where[]= array("'='", "'".$type_id."'", "'lf.id_".$type."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'frequence'",$op_val);
	
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_rubrique', $boucles);
	$c = "calcul_mysql_in('lf.id_rubrique', calcul_branche($arg), '')";
	$c = "($arg ? $c : 1)";			
	if ($not)
		$boucle->where[]= array("'NOT'", $c);
	else
		$boucle->where[]= $c;
}
function critere_popularite_branche($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'pop.id_'.$type;
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$boucle->select[]= 'SUM('.$type_id.') AS popularite_relative';
	$boucle->from['pop']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'freq.".$boucle->primary."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'popularite_relative'",$op_val);
	
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_rubrique', $boucles);
	$c = "calcul_mysql_in('lf.id_rubrique', calcul_branche($arg), '')";
	$c = "($arg ? $c : 1)";
	if ($not)
		$boucle->where[]= array("'NOT'", $c);
	else
		$boucle->where[]= $c;
}

function nuage_note($score,$scoremax=1,$max=10) {
	$score = pow(($score/$scoremax),1.5); # lissage
	return ceil($max*$score);
}

function balise_FREQUENCE_dist($p) {
	$p->code = '$Pile[$SP][\'frequence\']';
	$p->interdire_scripts = false;
	return $p;
}
function balise_POPULARITE_RELATIVE_dist($p) {
	$p->code = '$Pile[$SP][\'popularite_relative\']';
	$p->interdire_scripts = false;
	return $p;
}

?>

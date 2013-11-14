<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009-2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * {enfants} ou {enfants #ID_RUBRIQUE}
 * renvoit tous les enfants d'une rubrique ou article
 * directs (liens descendants) ou indirects (liens transverses)
 *
 * @global <type> $exceptions_des_tables
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 * @param <type> $tous
 */
function critere_enfants($idb, &$boucles, $crit, $tous=true) {
	global $exceptions_des_tables;
	$boucle = &$boucles[$idb];

	if (isset($crit->param[0])){
		$arg = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	else
		$arg = kwote(calculer_argument_precedent($idb, 'id_rubrique', $boucles));

	if ($boucle->type_requete == 'rubriques' OR isset($exceptions_des_tables[$boucle->id_table]['id_parent'])) {
		$id_parent = isset($exceptions_des_tables[$boucle->id_table]['id_parent']) ?
			$exceptions_des_tables[$boucle->id_table]['id_parent'] :
			'id_parent';
		$mparent = $boucle->id_table . '.' . $id_parent;
	}
	else {
		$mparent = $boucle->id_table . '.id_rubrique';
	}

	$where = array();

	if ($tous!=='indirects')
		$where[] = "is_array(\$r=$arg)?sql_in('$mparent',\$r):array('=', '$mparent', \$r)";

	if ($tous!=='directs'
	  AND in_array(table_objet_sql($boucle->type_requete),array_keys(lister_tables_objets_sql()))){
		$type = objet_type($boucle->type_requete);
		$cond = "is_array(\$r=$arg)?sql_in('rl.id_parent',\$r):'rl.id_parent='.\$r";
		$sous = "sql_get_select('rl.id_objet','spip_rubriques_liens as rl',$cond.' AND objet=\'$type\'')";
		$where[] = "array('IN', '".$boucle->id_table.".".$boucle->primary."', '('.$sous.')')";
	}
	if (count($where)==2)
		$where = array("'OR'",$where[0],$where[1]);
	else
		$where = reset($where);

	$boucle->where[]= $where;
}

function critere_enfants_directs_dist($idb, &$boucles, $crit) {
	critere_enfants($idb, $boucles, $crit, 'directs');
}

function critere_enfants_indirects_dist($idb, &$boucles, $crit) {
	critere_enfants($idb, $boucles, $crit, 'indirects');
}

/**
 * {parents}
 * renvoit tous les parents d'une rubrique ou article
 * {parents #ID_RUBRIQUE}
 * renvoit tous les parents d'une rubrique
 * directs (liens ascendants) ou indirects (liens transverses)
 *
 * @global <type> $exceptions_des_tables
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 * @param <type> $tous
 */
function critere_parents($idb, &$boucles, $crit, $tous=true) {
	global $exceptions_des_tables;
	$boucle = &$boucles[$idb];
	$boucle_parent = $boucles[$boucle->id_parent];

	$primary = $boucle->id_table.".".$boucle->primary;

	$where = array();

	if ($tous!=='indirects'){
		$argp = kwote(calculer_argument_precedent($idb, $boucle_parent->type_requete == 'rubriques' ? 'id_parent' : 'id_rubrique', $boucles));
		$where[] = "is_array(\$r=$argp)?sql_in('$primary',\$r):array('=', '$primary', \$r)";
	}

	if ($tous!=='directs'
	  AND in_array(table_objet_sql($boucle_parent->type_requete),array_keys(lister_tables_objets_sql()))){
		$arg = kwote(calculer_argument_precedent($idb, id_table_objet(objet_type($boucle_parent->type_requete)), $boucles));
		$type = objet_type($boucle_parent->type_requete);
		$sous = "sql_get_select('rl.id_parent','spip_rubriques_liens as rl','rl.id_objet='.$arg.' AND objet=\'$type\'')";
		$where[] = array("'IN'", "'$primary'", "'('.$sous.')'");
	}
	if (count($where)==2)
		$where = array("'OR'",$where[0],$where[1]);
	else
		$where = reset($where);

	$boucle->where[]= $where;
}

function critere_parents_directs_dist($idb, &$boucles, $crit) {
	critere_parents($idb, $boucles, $crit, 'directs');
}
function critere_parent($idb, &$boucles, $crit) {
	critere_parents($idb, $boucles, $crit, 'directs');
}

function critere_parents_indirects_dist($idb, &$boucles, $crit) {
	critere_parents($idb, $boucles, $crit, 'indirects');
}


/**
 * Calcul d'une branche
 * (liste des id_rubrique contenues dans une rubrique donnee)
 * pour le critere {branche}
 *
 * @param <type> $id
 * @return <type>
 */
function calcul_branche_polyhier_in($id, $tous=true) {

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = array_map('intval', $id);

	// Notre branche commence par la rubrique de depart
	$branche = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while (
		$id = array_merge(
		$filles_directes = ($tous!=='indirects'?array_map('reset',sql_allfetsel('id_rubrique', 'spip_rubriques',sql_in('id_parent', $id))):array()),
		$filles_indirectes = ($tous!=='directs'?array_map('reset',sql_allfetsel('id_objet', 'spip_rubriques_liens',"objet='rubrique' AND " . sql_in('id_parent', $id))):array())
		)) {

		// enlever les rubriques deja trouvee, sinon on risque de tourner en rond a l'infini en cas
		// de polyhierarchie bouclee
		$id = array_diff($id,$branche);
		$branche = array_merge($branche,$id);
	}

	return implode(',',$branche);
}



/**
 * {branche ?} ou {branche #ID_RUBRIQUE}
 * {branche_directe ?} ou {branche_directe #ID_RUBRIQUE}
 * {branche_principale ?} ou {branche_principale #ID_RUBRIQUE}
 * {branche_complete ?} ou {branche_complete #ID_RUBRIQUE}
 *
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_branche($idb, &$boucles, $crit, $tous='elargie') {

	$not = $crit->not;
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0])){
		$arg = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	else
		$arg = kwote(calculer_argument_precedent($idb, 'id_rubrique', $boucles));


	//Trouver une jointure
	$champ = "id_rubrique";
	$desc = $boucle->show;
	//Seulement si necessaire
	if (!array_key_exists($champ, $desc['field'])){
		$cle = trouver_jointure_champ($champ, $boucle);
		$trouver_table = charger_fonction("trouver_table", "base");
		$desc = $trouver_table($boucle->from[$cle]);
		if (count(trouver_champs_decomposes($champ, $desc))>1){
			$decompose = decompose_champ_id_objet($champ);
			$champ = array_shift($decompose);
			$boucle->where[] = array("'='", _q($cle.".".reset($decompose)), '"'.sql_quote(end($decompose)).'"');
		}
	}
	else $cle = $boucle->id_table;


	$c = "sql_in('$cle" . ".$champ', \$b = calcul_branche_polyhier_in($arg,".($tous===true?'true':"'directs'").")"
	  . ($not ? ", 'NOT'" : '') . ")";
	$where[] = $c;
	
	if ($tous!=='directs'
	  AND in_array(table_objet_sql($boucle->type_requete),array_keys(lister_tables_objets_sql()))){
		$type = objet_type($boucle->type_requete);
		$primary = $boucle->id_table.".".$boucle->primary;
		$sous = "sql_get_select('rl.id_objet','spip_rubriques_liens as rl',sql_in('rl.id_parent',\$b" . ($not ? ", 'NOT'" : '') . ").' AND objet=\'$type\'')";
		$where[] = "array('IN', '$primary', '('.$sous.')')";
	}

	if (count($where)==2)
		$where = "array('OR',".$where[0].",".$where[1].")";
	else
		$where = reset($where);

	$boucle->where[]= !$crit->cond ? $where :
	  ("($arg ? $where : " . ($not ? "'0=1'" : "'1=1'") .')');
}

function critere_branche_principale_dist($idb, &$boucles, $crit) {
	critere_branche($idb, $boucles, $crit, 'directs');
}
// un alias
function critere_branche_directe_dist($idb, &$boucles, $crit) {
	critere_branche($idb, $boucles, $crit, 'directs');
}

function critere_branche_complete_dist($idb, &$boucles, $crit) {
	critere_branche($idb, $boucles, $crit, true);
}

/*
 * Déclarer un fonction générique pour pouvoir chercher dans les champs des rubriques liées
 *
 */
function inc_rechercher_joints_objet_rubrique_dist($table, $table_liee, $ids_trouves, $serveur){
	$cle_depart = id_table_objet($table);
	$s = sql_select(
		"id_objet as $cle_depart, id_parent as id_rubrique",
		'spip_rubriques_liens',
		array("objet='$table'", sql_in('id_parent', $ids_trouves)),
		'','','','',$serveur
	);
	return array($cle_depart, 'id_rubrique', $s);
}

?>

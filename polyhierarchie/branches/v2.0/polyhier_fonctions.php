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
		$sous = "sql_get_select('rl.id_objet','spip_rubriques_liens as rl',$cond.' AND rl.objet=\'$type\'')";
		$where[] = "array('IN', '".$boucle->id_table.".".$boucle->primary."', '(SELECT * FROM('.$sous.') AS subquery)')";
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
		$sous = "sql_get_select('rl.id_parent','spip_rubriques_liens as rl','rl.id_objet='.$arg.' AND rl.objet=\'$type\'')";
		$where[] = array("'IN'", "'$primary'", "'(SELECT * FROM('.$sous.') AS subquery)'");
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

	$type = objet_type($boucle->type_requete);
	$primary = $boucle->id_table.".".$boucle->primary;

	//Trouver une jointure
	$champ = "id_rubrique";
	$desc = $boucle->show;
	//Seulement si necessaire
	if (!array_key_exists($champ, $desc['field'])){
		$trouver_table = charger_fonction("trouver_table", "base");
		$cle = "";
		// peut-etre deja une jointure qui fournit id_rubrique ?
		foreach($boucle->from as $k=>$t){
			$desc = $trouver_table($t);
			if (isset($desc['field']['id_rubrique'])){
				$cle = $k;
				break;
			}
		}
		if (!$cle){
			$cle = trouver_jointure_champ($champ, $boucle);
			$desc = $trouver_table($boucle->from[$cle]);
			if (count(trouver_champs_decomposes($champ, $desc))>1){
				$decompose = decompose_champ_id_objet($champ);
				$champ = array_shift($decompose);
				$boucle->where[] = array("'='", _q($cle.".".reset($decompose)), '"'.sql_quote(end($decompose)).'"');
			}
		}
		// si le champ id_rubrique est recuperer par jointure, c'est le type et la primary de la table jointe
		// qu'il faut chercher dans la table spip_rubriques_liens (ie cas des evenements)
		if ($cle AND $desc) {
			$type_jointure = objet_type($boucle->from[$cle]);
			$primary_jointure = $cle . "." . id_table_objet($boucle->from[$cle]);
		}
	}
	else $cle = $boucle->id_table;


	$c = "sql_in('$cle" . ".$champ', \$b = calcul_branche_polyhier_in($arg,".($tous===true?'true':"'directs'").")"
	  . ($not ? ", 'NOT'" : '') . ")";
	$where[] = $c;
	
	if ($tous!=='directs'
	  AND in_array(table_objet_sql($boucle->type_requete),array_keys(lister_tables_objets_sql()))){
		// S'il y a une jointure, on cherche toujours les liaisons avec celle-ci
		if (isset($type_jointure)) {
			$sous_jointure = "sql_get_select('rl.id_objet','spip_rubriques_liens as rl',sql_in('rl.id_parent',\$in_rub" . ($not ? ", 'NOT'" : '') . ").' AND rl.objet=\'$type_jointure\'')";
			$where_jointure = "array('IN', '$primary_jointure', '(SELECT * FROM('.$sous_jointure.') AS subquery)')";
		}
		
		// S'il n'y a pas de jointure (cas par défaut) ou que l'objet est explicitement configuré pour être classé avec polyhier
		// on cherche les liaisons sur l'objet
		if (
			!isset($type_jointure)
			or (include_spip('inc/config') and in_array(table_objet_sql($type), lire_config('polyhier/lier_objets', array())))
		) {
			$sous_objet = "sql_get_select('rl.id_objet','spip_rubriques_liens as rl',sql_in('rl.id_parent',\$in_rub" . ($not ? ", 'NOT'" : '') . ").' AND rl.objet=\'$type\'')";
			$where_objet = "array('IN', '$primary', '(SELECT * FROM('.$sous_objet.') AS subquery)')";
		}
		
		// S'il y a les deux, on fait un OR
		if (isset($where_jointure) and isset($where_objet)) {
			$where[] = "array('OR', $where_jointure, $where_objet)";
		}
		// Sinon s'il n'y a que jointure
		elseif (isset($where_jointure)) {
			$where[] = $where_jointure;
		}
		// Sinon que sur l'objet
		else {
			$where[] = $where_objet;
		}
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



/**
 * Compile la balise `#URL_POLYHIER{#ID_RUBRIQUE,article,#ID_ARTICLE}`
 * qui génère l'URL d'un article contextualisee a l'une de ses rubriques parentes
 *
 * Si la rubrique passee en argument n'est pas une rubrique parente elle est ignoree
 * Si les URLs ne contiennent pas l'URL de la rubrique parente (URL arbo), la rubrique contextuelle est ajoutee en query string
 *
 * @balise
 * @example
 *     ```
 *     #URL_POLYHIER{#ENV{id_rubrique,#ID_RUBRIQUE},article,#ID_ARTICLE}
 *     ```
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_URL_POLYHIER_dist($p) {

	$_id_rubrique = interprete_argument_balise(1, $p);
	$_type = interprete_argument_balise(2, $p);
	$_id = interprete_argument_balise(3, $p);

	$code = "urlencode_1738(generer_url_polyhier_entite($_id, $_type, $_id_rubrique))";
	$p->code = $code;
	if (!$p->etoile) {
		$p->code = "vider_url($code)";
	}
	$p->interdire_scripts = false;

	return $p;
}

/**
 * Generer l'URL polyhierarchique d'un objet relativement a une rubrique parente secondaire
 * on cherche le parent secondaire qui est dans la branche de la rubrique fournie et on le passe en argument
 * de l'URL. Si le module URL le gere (arbopoly) l'URL reposera sur l'URL de ce parent secondaire,
 * sinon elle restera en argument
 *
 * a charge pour les squelettes de gerer la presence du id_rubrique=xxx dans le contexte de la page de l'objet concerne
 *
 * Ne fonctionne que si l'objet est lui même rattache en polyhierarchie,
 * pas si il est enfant direct d'une rubrique enfant secondaire
 *
 * @param int $id_objet
 * @param string $objet
 * @param int $id_rubrique
 * @param string $args
 * @param string $ancre
 * @return string
 */
function generer_url_polyhier_entite($id_objet, $objet, $id_rubrique=null, $args='', $ancre='') {

	// si id_rubrique contextuel passe en argument et si c'est bien un parent polyhierarchique
	$parents_poly = sql_allfetsel('id_parent','spip_rubriques_liens', 'objet='.sql_quote($objet). ' AND id_objet='.intval($id_objet));
	$parents_poly = array_map('reset',$parents_poly);

	$maxiter = 100;
	$branche = $r = array($id_rubrique);
	while (!($id_parent = array_intersect($parents_poly, $r))
	  and $maxiter--
	  and $filles = sql_allfetsel('id_rubrique','spip_rubriques',sql_in('id_parent', $r) . " AND " . sql_in('id_rubrique', $branche, 'NOT'))) {
		$r = array_map('reset', $filles);
		$branche = array_merge($branche, $r);
	}

	if ($id_parent = reset($id_parent)) {
		// le vrai parent
		$champ_parent = ($objet == 'rubrique' ? 'id_parent' : 'id_rubrique');
		$args .= ($args?'&':'')."$champ_parent=$id_parent";
	}
	$url = generer_url_entite($id_objet, $objet, $args, $ancre, true);
	return $url;
}

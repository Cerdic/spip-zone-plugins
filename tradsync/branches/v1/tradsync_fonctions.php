<?php
/**
 * Plugin tradsync
 * Licence GPL (c) 2010 Matthieu Marcillaud
 * 
 */

/**
 * 
 * {traductions en} : tous les elements anglais etant des traductions
 * 	 = {!origine_traduction}{lang=en}
 *
 * {!traductions en} : tous les elements d'origine non traduits en anglais
 *
 */
function critere_traductions_dist($idb, &$boucles, $crit) {
	global $exceptions_des_tables;
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;

	if (!$primary OR strpos($primary,',')) {
		erreur_squelette(_T('zbug_doublon_sur_table_sans_cle_primaire'), "BOUCLE$idb");
		return;
	}
	$_table = $boucle->id_table;
	$table = $boucle->type_requete;
	$table_sql = table_objet_sql(objet_type($table));

	$_id_trad = isset($exceptions_des_tables[$boucle->id_table]['id_trad']) ?
		$exceptions_des_tables[$boucle->id_table]['id_trad'] :
		'id_trad';



	if (isset($crit->param[0])){
		$_lang = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		#$_lang = '@$Pile[0]["lang"]';
		$_lang = "''";
		// rendons obligatoire ce parametre
		return (array('tradsync:zbug_critere_necessite_parametre', array('critere' => $crit->op )));
	}

	// base de {origine_traduction}
	$origine_traduction = array("'OR'",
			array("'='", "'$_table.$_id_trad'", "'$_table.$primary'"),
			array("'='", "'$_table.$_id_trad'", "'0'")
	);
	$lang = array("'='","'$_table.lang'", "sql_quote($_lang)");
	
	if ($not) {
		$boucle->where[] = $origine_traduction;
		$boucle->where[] = array("'NOT'", $lang);
		$boucle->where[] =
			array("'NOT'",
				array("'IN'", "'$_table.$primary'",
					"'('.sql_get_select( 'strad.$_id_trad', '$table_sql AS strad', 'strad.lang = ' . sql_quote($_lang)).')'"));
	} else {
		$boucle->where[] = $lang;
		$boucle->where[] = array("'NOT'", $origine_traduction);
		
		/* // environ equivalent
		$boucle->where[] = array("'IN'", "'$boucle->id_table." .
			"$_id_trad'", "'('.sql_get_select( 'strad.$primary', '$table_sql AS strad',
				'strad.$_id_trad=0 OR strad.$_id_trad = strad.$primary').')'");
		*/
	}
}




/**
 * 
 * {origine_modifiee} : tous les elements traduits dont la source a ete modifiee (date plus recente)
 *
 * {!origine_modifiee} : tous les elements traduits dont la source n'est pas plus recente
 *
 */
function critere_origine_modifiee_dist($idb, &$boucles, $crit) {
	global $exceptions_des_tables;
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;

	if (!$primary OR strpos($primary,',')) {
		erreur_squelette(_T('zbug_doublon_sur_table_sans_cle_primaire'), "BOUCLE$idb");
		return;
	}
	
	$_table = $boucle->id_table;
	$table = $boucle->type_requete;
	$table_sql = table_objet_sql(objet_type($table));

	$_id_trad = isset($exceptions_des_tables[$boucle->id_table]['id_trad']) ?
		$exceptions_des_tables[$boucle->id_table]['id_trad'] :
		'id_trad';

	$boucle->from['origine'] = "$table_sql"; 
	$boucle->where[] = array("'='", "'$_table.$_id_trad'", "'origine.$primary'");

	// base de {origine_traduction}
	$origine_traduction = array("'OR'",
			array("'='", "'$_table.$_id_trad'", "'$_table.$primary'"),
			array("'='", "'$_table.$_id_trad'", "'0'")
	);
	$boucle->where[] = array("'NOT'", $origine_traduction);
	
	if ($not) {
		$boucle->where[] = array("'<='", "'origine.maj'", "'$_table.maj'");
	} else {
		$boucle->where[] = array("'>'", "'origine.maj'", "'$_table.maj'");
	}
}






/**
 * 
 * {polyglotte titre} : tous les elements ayant un <multi> dans le titre
 * {polyglotte titre,fr} : tous les elements ayant un <multi> avec [fr] dans le titre
 *
 * {!polyglotte titre} : l'inverse...
 * {!polyglotte titre,fr} : l'inverse...
 *
 */
function critere_polyglotte_dist($idb, &$boucles, $crit) {
	global $exceptions_des_tables;
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;

	if (!$primary OR strpos($primary,',')) {
		erreur_squelette(_T('zbug_doublon_sur_table_sans_cle_primaire'), "BOUCLE$idb");
		return;
	}
	
	$_table = $boucle->id_table;
	$table = $boucle->type_requete;
	$table_sql = table_objet_sql(objet_type($table));


	if (isset($crit->param[0][0])){
		$_champ = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		#$_lang = '@$Pile[0]["lang"]';
		$_champ = "''";
		// rendons obligatoire ce parametre
		return (array('tradsync:zbug_critere_necessite_parametre', array('critere' => $crit->op )));
	}

	if (isset($crit->param[1][0])){
		$_lang = calculer_liste(array($crit->param[1][0]), array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		#$_lang = '@$Pile[0]["lang"]';
		$_lang = "''";
	}	

	$regexp = "(($_lang) ? sql_quote('<multi>.*\['.$_lang.'\].*</multi>') : sql_quote('<multi>.*</multi>'))";
	$where = array("'REGEXP'", "'$_table.'.$_champ", $regexp);
	
	if ($not) {
		$where = array("'NOT'", $where);
	}
	$boucle->where[] = $where;
}



?>

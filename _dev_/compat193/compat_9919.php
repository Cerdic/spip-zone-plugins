<?php

#include_spip('inc/filtres');
#if(version_svn_courante(_DIR_RACINE) >= 9919) {

  function spip_abstract_serveur($ins_sql, $serveur) {
   	 return sql_serveur($ins_sql, $serveur);
  }
  
  function spip_abstract_select(
    $select = array(), $from = array(), $where = array(),
  	$groupby = '', $orderby = array(), $limit = '',
  	$sousrequete = '', $having = array(),
  	$table = '', $id = '', $serveur='') {
    return sql_select(
      $select, $from, $where,
      $groupby, $orderby, $limit,
      $sousrequete, $having,
      $table, $id, $serveur);
  }
  
  function spip_abstract_fetch($res, $serveur='') {
    return sql_fetch($res, $serveur);
  }
  
  function spip_abstract_count($res, $serveur='') {
    return sql_count($res, $serveur);
  }
  
  function spip_abstract_free($res, $serveur='') {
    return sql_free($res, $serveur);
  }

  function spip_abstract_insert($table, $noms, $valeurs, $serveur='') {
    return sql_insert($table, $noms, $valeurs, $serveur);
  }

  function spip_abstract_update($table, $exp, $where, $serveur='') {
    return sql_update($table, $exp, $where, $serveur);
  }

  function spip_abstract_delete($table, $where, $serveur='') {
    return sql_delete($table, $where, $serveur);
  }

  function spip_abstract_replace($table, $values, $keys, $serveur=''){
    return sql_replace($table, $values, $keys, $serveur);
  }

  function spip_abstract_showtable($table, $serveur='', $table_spip = false) {
    return sql_showtable($table, $serveur, $table_spip);
  }

  function spip_abstract_create($nom, $champs, $cles, $autoinc=false, $temporary=false, $serveur='') {
    return sql_create($nom, $champs, $cles, $autoinc, $temporary, $serveur);
  }

  function spip_abstract_multi($sel, $lang, $serveur='') {
    return sql_multi($sel, $lang, $serveur);
  }

  function spip_abstract_fetsel(
  	$select = array(), $from = array(), $where = array(),
  	$groupby = '', $orderby = array(), $limit = '',
  	$sousrequete = '', $having = array(),
  	$table = '', $id = '', $serveur='') {
    return sql_fetsel(
  	$select, $from, $where,
  	$groupby, $orderby, $limit,
  	$sousrequete, $having,
  	$table, $id, $serveur);
  }
  
  function spip_abstract_countsel($from = array(), $where = array(),
  	$groupby = '', $limit = '', $sousrequete = '', $having = array(),
  	$serveur='') {
    return sql_countsel($from, $where,
    	$groupby, $limit, $sousrequete, $having,
    	$serveur);
  }

  function spip_sql_error($query, $serveur='') {
    return sql_error($query, $serveur);
  }

  function spip_sql_errno($serveur='') {
    return sql_errno($serveur);
  }
  
  // r9916
  function sql_calendrier_mois($annee,$mois,$jour) {
	return quete_calendrier_mois($annee,$mois,$jour);
  }
  
  function sql_calendrier_semaine($annee,$mois,$jour) {
	return quete_calendrier_semaine($annee,$mois,$jour);
  }
  
  function sql_calendrier_jour($annee,$mois,$jour) {
	return quete_calendrier_jour($annee,$mois,$jour);
  }
  
  function sql_calendrier_interval($limites) {
	return quete_calendrier_interval($limites);
  }
  
  function  sql_calendrier_interval_forums($limites, &$evenements) {
	return quete_calendrier_interval_forums($limites, &$evenements);
  }
  
  function sql_calendrier_interval_articles($avant, $apres, &$evenements) {
	return quete_calendrier_interval_articles($avant, $apres, &$evenements);
  }
  
  function sql_calendrier_interval_rubriques($avant, $apres, &$evenements) {
	return quete_calendrier_interval_rubriques($avant, $apres, &$evenements);
  }
  
  function sql_calendrier_interval_breves($avant, $apres, &$evenements) {
	return quete_calendrier_interval_breves($avant, $apres, &$evenements);
  }
  
  function sql_calendrier_interval_rv($avant, $apres) {
	return quete_calendrier_interval_rv($avant, $apres);
  }
  
  function sql_calendrier_taches_annonces () {
	return quete_calendrier_taches_annonces ();
  }
  
  function sql_calendrier_taches_pb () {
	return quete_calendrier_taches_pb ();
  }
  
  function sql_calendrier_taches_rv () {
	return quete_calendrier_taches_rv ();
  }
  
  function sql_calendrier_agenda ($annee, $mois) {
	return quete_calendrier_agenda ($annee, $mois);
  }
  
  //r9918
  function sql_rubrique_fond($contexte) {
	return quete_rubrique_fond($contexte);
  }
  
  function sql_chapo($id_article) {
	return quete_chapo($id_article);
  }
  
  function sql_parent($id_rubrique) {
	return quete_parent($id_rubrique);
  }
  
  function sql_profondeur($id) {
	return quete_profondeur($id);
  }
  
  function sql_rubrique($id_article) {
	return quete_rubrique($id_article);
  }
  
  function sql_petitions($id_article, $table, $id_boucle, $serveur, &$cache) {
	return quete_petitions($id_article, $table, $id_boucle, $serveur, &$cache);
  }
  
  function sql_accepter_forum($id_article) {
	return quete_accepter_forum($id_article);
  }
  
  function trouver_def_table($nom, &$boucle) {
	global $tables_principales, $tables_auxiliaires, $table_des_tables, $tables_des_serveurs_sql; 
 
	$nom_table = $nom; 
	$s = $boucle->sql_serveur; 
	if (!$s) { 
		$s = 'localhost'; 
		if (in_array($nom, $table_des_tables)) 
			$nom_table = 'spip_' . $nom; 
		} 
	$desc = $tables_des_serveurs_sql[$s]; 


	if (isset($desc[$nom_table])) 
		return array($nom_table, $desc[$nom_table]); 

	include_spip('base/auxiliaires'); 
	$nom_table = 'spip_' . $nom; 
	if ($desc = $tables_auxiliaires[$nom_table]) 
		return array($nom_table, $desc); 

	if ($desc = sql_showtable($nom, $boucle->sql_serveur)) 
		if (isset($desc['field'])) { 
			// faudrait aussi prevoir le cas du serveur externe 
			$tables_principales[$nom] = $desc; 
			return array($nom, $desc); 
		} 
	erreur_squelette(_T('zbug_table_inconnue', array('table' => $nom)), 
	$boucle->id_boucle); 

	return false;
  }

#}

?>
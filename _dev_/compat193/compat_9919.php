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

#}

?>
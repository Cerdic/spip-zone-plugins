<?php

/* compatiblité descendante avec spip 1.9.1 et 1.9.2
 *
 * içi sur surchagée les fonctions spécifiques à spip 1.9.3
 * afin de permettre au nouveau code de fonctionner sur les
 * anciens spip. Cette surcharge est déstinée à disparaitre
 * dans les version future du plugin openPublishing
 */


function sql_quote($var) {
	return spip_abstract_quote($var);
}

function sql_delete($table,$where) {
	return spip_query("DELETE FROM $table WHERE $where");
}

function sql_insertq($table,$array) {

	$expend_key = '';
	$expend_value = '';
	foreach ($array as $key => $value) {
		$expend_key .= $key.',';
		$expend_value .= $value.',';
	}
	$expend_array = '('.substr($expend_key,0,strlen($expend_key)-1).') VALUES ('
			.substr($expend_value,0,strlen($expend_value)-1).')';

	spip_query("INSERT INTO $table $expend_array");
	return mysql_insert_id();
}

function sql_fetsel($what,$from,$where) {
	return spip_fetch_array(spip_query("SELECT $what FROM $from WHERE $where"));
}

function sql_update($table,$array,$where) {

	$expend = '';
	foreach ($array as $key => $value) {
		$expend .= $key.' = '.$value .',';
	}
	$expend = substr($expend,0,strlen($expend)-1);

	return spip_query('UPDATE '.$table.' SET '.$expend.' WHERE '.$where);
}

?>
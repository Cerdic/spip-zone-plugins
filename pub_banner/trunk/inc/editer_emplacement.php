<?php

/**
 * Edition d'un emplacement
 */
function inc_editer_emplacement_dist($id_emp, $datas){
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $ok = sql_updateq($GLOBALS['_PUBBAN_CONF']['table_empl'], $datas, "id_empl=".intval($id_emp), '', _BDD_PUBBAN) )
		return true;
	return false;
}

?>
<?php

/**
 * Edition d'une publicite
 */
function inc_editer_pub_dist($id_pub, $datas){
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $ok = sql_updateq($GLOBALS['_PUBBAN_CONF']['table_pub'], $datas, "id_pub=".intval($id_pub), '', _BDD_PUBBAN) )
		return true;
	return false;
}

?>
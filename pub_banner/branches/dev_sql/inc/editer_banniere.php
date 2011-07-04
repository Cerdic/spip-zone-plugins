<?php

/**
 * Edition d'un emplacement
 */
function inc_editer_banniere_dist($id_emp, $datas){
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $ok = sql_updateq('spip_bannieres', $datas, "id_banniere=".intval($id_emp), '') )
		return true;
	return false;
}

?>
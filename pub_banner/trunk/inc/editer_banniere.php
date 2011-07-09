<?php

/**
 * Edition d'une banniere
 */
function inc_editer_banniere_dist($id_banniere, $datas){
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $ok = sql_updateq('spip_bannieres', $datas, "id_banniere=".intval($id_banniere), '') )
		return true;
	return false;
}

?>
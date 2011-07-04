<?php

/**
 * Edition d'une publicite
 */
function inc_editer_pub_dist($id_pub, $datas){
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $ok = sql_updateq('spip_publicites', $datas, "id_publicite=".intval($id_pub), '') )
		return true;
	return false;
}

?>
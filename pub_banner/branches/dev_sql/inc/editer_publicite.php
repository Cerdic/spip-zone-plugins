<?php

/**
 * Edition d'une publicite
 */
function inc_editer_publicite_dist($id_publicite, $datas){
	if (!is_array($datas)) return;
	include_spip('base/abstract_sql');
	if ( $ok = sql_updateq('spip_publicites', $datas, "id_publicite=".intval($id_publicite), '') )
		return true;
	return false;
}

?>
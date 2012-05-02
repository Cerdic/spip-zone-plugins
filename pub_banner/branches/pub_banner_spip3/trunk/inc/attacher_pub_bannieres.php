<?php

function inc_attacher_pub_bannieres_dist($id_publicite, $empls){
	if (!strlen($empls)) return;
	include_spip('base/abstract_sql');
	if (!is_array($empls)) $empls = array( $empls );
	$old_emp = pubban_bannieres_de_la_pub($id_publicite);
	$ok = true;

	foreach($empls as $k=>$id_banniere) {
		if(!$old_emp OR (is_array($old_emp) AND !in_array($id_banniere, $old_emp)) )
			$ok = sql_insertq('spip_bannieres_publicites', array('id_publicite'=>$id_publicite, 'id_banniere'=>$id_banniere), '');
	}
	if ($old_emp AND count($old_emp)) 
		foreach($old_emp as $k=>$id_old) {
			if( !in_array($id_old, $empls) )
				$ok = sql_delete('spip_bannieres_publicites', 'id_publicite='.intval($id_publicite).' AND id_banniere='.intval($id_old));
	}
	return $ok;
}

?>
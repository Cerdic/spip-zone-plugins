<?php

function inc_attacher_pub_emplacements_dist($id_pub, $empls){
	if (!strlen($empls)) return;
	include_spip('base/abstract_sql');
	if (!is_array($empls)) $empls = array( $empls );
	$old_emp = pubban_emplacements_de_la_pub($id_pub);
	$ok = true;

	foreach($empls as $k=>$id_empl) {
		if(!$old_emp OR (is_array($old_emp) AND !in_array($id_empl, $old_emp)) )
			$ok = sql_insertq($GLOBALS['_PUBBAN_CONF']['table_join'], array('id_pub'=>$id_pub, 'id_empl'=>$id_empl), '', _BDD_PUBBAN);
	}
	if ($old_emp AND count($old_emp)) 
		foreach($old_emp as $k=>$id_old) {
			if( !in_array($id_old, $empls) )
				$ok = sql_delete($GLOBALS['_PUBBAN_CONF']['table_join'], 'id_pub='.intval($id_pub).' AND id_empl='.intval($id_old), _BDD_PUBBAN);
	}
	return $ok;
}

?>
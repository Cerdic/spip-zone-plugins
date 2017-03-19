<?php


function maxigos_install() {
	$maxigos_res = sql_countsel('spip_types_documents', 'extension="sgf"');
	if ($maxigos_res == 0) {sql_insertq('spip_types_documents', array('titre'=>'SGF', 'extension'=>'sgf', 'inclus'=>'non', 'upload'=>'oui'));}
}

function maxigos_unistall() {
	$maxigos_res = sql_countsel('spip_types_documents', 'extension="sgf"');
	if ($maxigos_res > 1) {sql_delete('spip_types_documents', 'extension="sgf"');}
}

?>

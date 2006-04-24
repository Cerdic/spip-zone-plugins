<?php
// declarer les tables exportees dans $tables_auxiliaires
// pour qu'elles soient prises en compte dans le dump
if (isset($GLOBALS['meta']['csvimport_tables_auth'])){
	global $tables_auxiliaires;
	$liste = array_keys(unserialize($GLOBALS['meta']['csvimport_tables_auth']));
	foreach($liste as $table=>$infos){
		if ($infos['dyn_declare_aux'])
			$tables_auxiliaires[$table]=false; // on l'init pour savoir qu'elle existe, mais false pour que le compilo fasse un show_table si besoin
	}
}
?>

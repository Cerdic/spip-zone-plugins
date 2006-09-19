<?php
// declarer les tables exportees dans $tables_auxiliaires
// pour qu'elles soient prises en compte dans le dump
if (isset($GLOBALS['meta']['csvimport_tables_auth'])){
	global $tables_auxiliaires;
	$csvimport_liste = array_keys(unserialize($GLOBALS['meta']['csvimport_tables_auth']));
	foreach($csvimport_liste as $csvimport_table=>$csvimport_infos){
		if ($csvimport_infos['dyn_declare_aux'])
			$tables_auxiliaires[$csvimport_table]=false; // on l'init pour savoir qu'elle existe, mais false pour que le compilo fasse un show_table si besoin
	}
}
?>

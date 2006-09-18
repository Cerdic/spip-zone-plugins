<?php

function url_visu_objet($id_table,$id_objet){
	include_spip('inc/indexation');
	$liste = liste_index_tables();
	if (!isset($liste[$id_table])) return "";
	$script = "";
	$table = $liste[$id_table];
	$f = str_replace('spip_','',$table);
	if (find_in_path("exec/$f.php"))
		$script = $f;
	if (!$script){
		$f2 = str_replace(array('rubriques','syndic'),array('naviguer','sites'),$f);
		if (find_in_path("exec/$f2.php"))
			$script = $f2;
	}
	if (!$script){
		if (find_in_path("exec/{$f2}_tous.php"))
			$script = $f2."_tous";
	}
	if (!$script){
		if (find_in_path("exec/{$f}_tous.php"))
			$script = $f."_tous";
	}
	if ($script){
		return "index.php?exec=$script&".primary_index_table($table)."=".$id_objet;
	}
	return "";
}


?>
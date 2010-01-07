<?php

function step_rechercher_liste_des_champs($tables){
	$tables['plugin']['nom'] = 8;
	$tables['plugin']['prefixe'] = 8;
	$tables['plugin']['shortdesc'] = 6;
	$tables['plugin']['description'] = 3;
	$tables['plugin']['tags'] = 1;
	return $tables;
}

?>

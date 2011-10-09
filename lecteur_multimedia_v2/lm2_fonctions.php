<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function lm2_joli_titre($titre){
	$titre = basename($titre);
	$titre = str_replace('.mp3','',$titre);
	$titre = str_replace('^ ','',$titre);
	$titre = str_replace("_"," ", $titre );
	$titre = str_replace("'"," ",$titre );

	return $titre ;
}
?>
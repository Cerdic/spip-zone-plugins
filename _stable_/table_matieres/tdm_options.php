<?php

$tm_lg_ancre  = 35;
$tm_sep_ancre = '-';
$tm_min_ancre = 3;

#tester la presence de CFG
if(function_exists('lire_config')) {
	$tm_lg_ancre  = ($t = lire_config('table_matieres/lg' )) ? $t :  $tm_lg_ancre;
	$tm_sep_ancre = ($t = lire_config('table_matieres/sep')) ? $t :  $tm_sep_ancre;
	$tm_min_ancre = ($t = lire_config('table_matieres/min')) ? $t :  $tm_min_ancre;
}

define( "_LG_ANCRE", $tm_lg_ancre);
define("_SEP_ANCRE", $tm_sep_ancre);
define("_MIN_ANCRE", $tm_min_ancre);
	
define("_RETOUR_TDM", '<a href="#tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" /></a>');

$table_des_traitements['TEXTE']['articles']= 'TableMatieres_LienRetour(TableMatieres_AjouterAncres(%s))';
$table_des_traitements['TABLE_MATIERES']['articles']= 'TableMatieres_LienRetour(TableMatieres_AjouterAncres(%s), true)';

?>

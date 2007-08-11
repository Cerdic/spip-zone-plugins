<?php

#tester la presence de CFG
if(!function_exists('lire_config')) {
	function lire_config($texte) {
		return 0;
	}
}

define( "_LG_ANCRE", ($t = lire_config('table_matieres/lg' )) ? $t :  35);
define("_SEP_ANCRE", ($t = lire_config('table_matieres/sep')) ? $t : '-');
define("_MIN_ANCRE", ($t = lire_config('table_matieres/min')) ? $t :   3);

define("_RETOUR_TDM", '<a href="#tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" /></a>');

$table_des_traitements['TEXTE']['articles']= 'TableMatieres_LienRetour(propre(TableMatieres_AjouterAncres(%s)))';
$table_des_traitements['TABLE_MATIERES']['articles']= 'TableMatieres_LienRetour(propre(TableMatieres_AjouterAncres(%s)), true)';

?>
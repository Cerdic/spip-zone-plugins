<?php

define("_LG_ANCRE", 35);
define("_RETOUR_TDM", '<a href="#tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" /></a>');

$table_des_traitements['TEXTE']['articles']= 'TableMatieres_LienRetour(propre(TableMatieres_AjouterAncres(%s)))';
$table_des_traitements['TABLE_MATIERES']['articles']= 'TableMatieres_LienRetour(propre(TableMatieres_AjouterAncres(%s)), true)';

?>
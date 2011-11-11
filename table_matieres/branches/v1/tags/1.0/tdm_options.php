<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// tester la presence de CFG
$tm = @unserialize($GLOBALS['meta']['table_matieres']);

define('_LG_ANCRE', isset($tm['lg']) ? $tm['lg'] : 35);
define('_SEP_ANCRE', isset($tm['sep']) ? $tm['sep'] : '-');
define('_MIN_ANCRE', isset($tm['min']) ? $tm['min'] : 3);
define('_RETOUR_TDM', '<a href="#tdm" class="tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" /></a>');

include_spip('public/interfaces');
$table_des_traitements['TEXTE']['articles'] =
	str_replace(
		'%s',
		'TableMatieres_LienRetour(TableMatieres_AjouterAncres(%s))',
		isset($table_des_traitements['TEXTE']['articles'])
			? $table_des_traitements['TEXTE']['articles']
			: $table_des_traitements['TEXTE'][0]
	);
$table_des_traitements['TABLE_MATIERES']['articles']= 'TableMatieres_LienRetour(TableMatieres_AjouterAncres(%s), true)';

?>

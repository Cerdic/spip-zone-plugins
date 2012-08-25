<?php
/*
 * Plugin cp
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */

 /*
function cp_config_tab_fichier()
{
$tab_cp_fichier = array(	'code_postal'	=>	array('nom'=>'Codes postaux','fichier'=>'codes_postaux_insee.csv'));
return $tab_cp_fichier;
}
*/
function cp_config_tab_table()
{
$tab_cp_fichier = array(	'code_postals'	=>	array('nom'=>'Codes postaux'));
return $tab_cp_fichier;
}

function cp_config_tab_fichier()
{
return array('code_postal'=>array('nom'=>'Geonames : Codes postaux FRANCE','url_fichier'=>'http://download.geonames.org/export/zip/FR.zip','nom_fichier'=>'code_postaux_FR.txt'));
}

function cp_config_correspondance_colonne()
{
return array('code_postal'=>array(
					'colonnes'=>array(
						"code"		=>	1,
						"titre"		=>	2),
					'liaison'=>2,
					//'filtre'=>array('cle'=>7,'valeur'=>'ADM4')
					));
}



?>

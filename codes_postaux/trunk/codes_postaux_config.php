<?php
/*
 * Plugin codes_postaux
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */

 /*
function codes_postaux_config_tab_fichier()
{
$tab_codes_postaux_fichier = array(	'code_postal'	=>	array('nom'=>'Codes postaux','fichier'=>'codes_postaux_insee.csv'));
return $tab_codes_postaux_fichier;
}
*/
function codes_postaux_config_tab_table()
{
$tab_codes_postaux_fichier = array(	'code_postals'	=>	array('nom'=>'Codes postaux'));
return $tab_codes_postaux_fichier;
}

function codes_postaux_config_tab_fichier()
{
return array(	'code_postal'=>array(	'nom'=>'Geonames : Codes postaux FRANCE',
										'url_fichier'=>'http://download.geonames.org/export/zip/FR.zip','nom_fichier'=>'codes_postaux_FR.txt',
										'description'=>array('pays','code','titre','region','code_region','departement','code_departement','arrondissement','code_arrondissement','latitude','longitude','?')));
}

function codes_postaux_config_correspondance_colonne()
{
return array('code_postal'=>array(
					'colonnes'=>array("code","titre"),
					'liaison'=>2,
					//'filtre'=>array('cle'=>7,'valeur'=>'ADM4')
					));
}



?>

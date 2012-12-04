<?php
/*
 * Plugin COG
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */
function cog_config_tab_fichier()
{
$tab_cog_fichier = array(
'communes'				=> array(	'nom'=>'Communes',
									'fichier'=>'http://www.insee.fr/fr/methodes/nomenclatures/cog/telechargement/2012/txt/comsimp2012.zip'),
'cantons'				=> array(	'nom'=>'Cantons',
									'fichier'=>'http://www.insee.fr/fr/methodes/nomenclatures/cog/telechargement/2012/txt/canton2012.txt'),
'arrondissements'		=> array(	'nom'=>'Arrondissements',
									'fichier'=>'http://www.insee.fr/fr/methodes/nomenclatures/cog/telechargement/2012/txt/arrond2012.txt'),
'departements' 			=> array(	'nom'=>'Départements',
									'fichier'=>'http://www.insee.fr/fr/methodes/nomenclatures/cog/telechargement/2012/txt/depts2012.txt'),
'regions' 				=> array(	'nom'=>'Régions',
									'fichier'=>'http://www.insee.fr/fr/methodes/nomenclatures/cog/telechargement/2012/txt/reg2012.txt'),
'epcis' 				=> array(	'nom'=>'EPCI',
									'fichier'=>array('epci.txt',array('epci_composition.txt',0,1))),
'epcis_natures' 		=> array(	'nom'=>'Natures des EPCI',
									'fichier'=>'epci_nature.txt'),
'epcis_communes_liens'	=> array(	'nom'=>'Relations EPCI - commune',
									'fichier'=>'epci_composition.txt'),
'zauers' 				=> array(	'nom'=>'ZAUER',
									'fichier'=>array('ZAUER.txt',array('zauer_composition.txt',0,1))),
'zauers_espace' 		=> array(	'nom'=>'Espaces Urbains des ZAUER',
									'fichier'=>'epci_nature.txt'),
'zauers_categories' 	=> array(	'nom'=>'Categories des ZAUER',
									'fichier'=>'epci_nature.txt'),
'zauers_communes_liens'	=> array(	'nom'=>'Relations ZAUER - commune',
									'fichier'=>'epci_nature.txt')
					);
return $tab_cog_fichier;
}

function cog_config_tab_table()
{
$tab_cog_fichier = array(
'communes'				=>	array('nom'=>'Commune'),
'cantons'				=>	array('nom'=>'Cantons'),
'arrondissements' 		=>	array('nom'=>'Arrondissement'),
'departements' 			=>	array('nom'=>'Département'),
'regions' 				=>	array('nom'=>'Région'),
'epcis' 				=>	array('nom'=>'EPCI'),
'epcis_natures' 		=>	array('nom'=>'Nature des EPCI'),
'zauers' 				=>	array('nom'=>'ZAUER'),
'zauers_espace' 		=>	array('nom'=>'Espace Urbain des ZAUER'),
'zauers_categories' 	=>	array('nom'=>'Categorie des ZAUER'),
'communes_liens'		=>	array('nom'=>'table de relations avec les communes')
);
return $tab_cog_fichier;
}
?>
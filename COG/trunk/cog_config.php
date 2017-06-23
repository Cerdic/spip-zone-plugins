<?php
/*
 * Plugin COG
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */
function cog_config_tab_fichier($objet=null)
{
$tab_cog_fichier = array(
'cog_commune'				=> array(	'nom'=>'Communes',
									'cle_unique'=>array('departement','code'),
									'fichier'=>'https://www.insee.fr/fr/statistiques/fichier/2114819/comsimp2016-txt.zip'),
'cog_canton'				=> array(	'nom'=>'Cantons',
									'cle_unique'=>array('departement','arrondissement','code'),
									'fichier'=>'https://www.insee.fr/fr/statistiques/fichier/2114819/canton2016-txt.zip'),
'cog_arrondissement'		=> array(	'nom'=>'Arrondissements',
									'cle_unique'=>array('departement','code'),
									'fichier'=>'https://www.insee.fr/fr/statistiques/fichier/2114819/arrond2016-txt.zip'),
'cog_departement' 			=> array(	'nom'=>'Départements',
									'fichier'=>'https://www.insee.fr/fr/statistiques/fichier/2114819/depts2016-txt.zip'),
'cog_region' 				=> array(	'nom'=>'Régions',
									'fichier'=>'region2016.txt'),
'cog_regions_ancienne' 		=> array(	'nom'=>'Anciennes définitions des régions',
									'fichier'=>'region2015.txt'),
'cog_epci' 					=> array(	'nom'=>'EPCI',
									'fichier'=>'https://www.insee.fr/fr/statistiques/fichier/2510634/intercommunalite-01-01-2017.zip',
									'xls'=>array(	'cog_epci' => array('fichier_csv'=>'epci.txt','onglet'=>'Liste des EPCI au 01-01-2013','colonnes'=>range('A','C'),'ligne_depart'=>2,'ligne_arrive'=>3000),
													'epci_relation'=>array('fichier_csv'=>'epci_relation.txt','onglet'=>'Composition communale des EPCI','colonnes'=>array('A','C'),'ligne_depart'=>2,'ligne_arrive'=>37000)),
									'relation'=>array('code_insee'	=>	array('fichier'=>'epci_relation','num_col'=>0,'lien_col_cog_epci'=>0,'lien_col_epci_relation'=>2)),
									'correspondance' => array('code'=>	0,'libelle'	=>	1,'nature' => 2)),
									
									//'fichier'=>array('epci.txt',array('epci_composition.txt',0,1))),
/*'zauers' 				=> array(	'nom'=>'ZAUER',
									'fichier'=>array('ZAUER.txt',array('zauer_composition.txt',0,1))),
'zauers_espace' 		=> array(	'nom'=>'Espaces Urbains des ZAUER',
									'fichier'=>'epci_nature.txt'),
'zauers_categories' 	=> array(	'nom'=>'Categories des ZAUER',
									'fichier'=>'epci_nature.txt'),
'zauers_communes_liens'	=> array(	'nom'=>'Relations ZAUER - commune',
									'fichier'=>'epci_nature.txt')*/
					);
if ($objet)
	return $tab_cog_fichier[$objet];			
return $tab_cog_fichier;
}

function cog_config_tab_table()
{
$tab_cog_fichier = array(
'cog_commune'				=>	array('nom'=>'Communes'),
'cog_canton'				=>	array('nom'=>'Cantons'),
'cog_arrondissement' 		=>	array('nom'=>'Arrondissements'),
'cog_departement' 			=>	array('nom'=>'Départements'),
'cog_region' 				=>	array('nom'=>'Régions'),
'cog_epci' 				=>	array('nom'=>'EPCI'),
/*'epcis_natures' 		=>	array('nom'=>'Nature des EPCI'),
'zauers' 				=>	array('nom'=>'ZAUER'),
'zauers_espace' 		=>	array('nom'=>'Espace Urbain des ZAUER'),
'zauers_categories' 	=>	array('nom'=>'Categorie des ZAUER'),
'communes_liens'		=>	array('nom'=>'table de relations avec les communes')*/
);
return $tab_cog_fichier;
}
?>
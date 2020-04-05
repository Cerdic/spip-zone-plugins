<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function cog_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_cog_communes',
									'spip_cog_communes_liens',
									'spip_cog_cantons',
									'spip_cog_arrondissements',
									'spip_cog_departements',
									'spip_cog_regions',
									'spip_cog_epcis',
									'spip_cog_epcis_natures',
									/*'spip_cog_zauers',
									'spip_cog_zauers_espace',
									'spip_cog_zauers_categories'*/)),
		array('cog_peupler_base')
	);
	$maj['1.1'] = array(
		 array('sql_alter',"TABLE spip_cog_cantons CHANGE `chef_lieu` `chef_lieu` MEDIUMINT( 6 )")
	);
	$maj['1.2'] = array(
		array('sql_alter',"TABLE spip_cog_regions RENAME spip_cog_regions_anciennes"),
		array('sql_alter',"TABLE spip_cog_regions_anciennes CHANGE `id_cog_region` `id_cog_region_ancienne` INT(10) auto_increment UNSIGNED NOT NULL COMMENT 'Identifiant du region' "),
		array('sql_alter',"TABLE spip_cog_regions_anciennes ADD `region2016` TINYINT ( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région 2016'"),
		array('maj_tables', array('spip_cog_regions'))
	);
	$maj['1.4'] = array(	array('maj_tables', array('spip_cog_departements')),
							array('cog_nouvelle_definition_regionale'));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function cog_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_cog_communes");
	sql_drop_table("spip_cog_communes_liens");
	sql_drop_table("spip_cog_cantons");
	sql_drop_table("spip_cog_arrondissements");
	sql_drop_table("spip_cog_departements");
	sql_drop_table("spip_cog_regions");
	sql_drop_table("spip_cog_regions_anciennes");
	sql_drop_table("spip_cog_epcis");
	sql_drop_table("spip_cog_epci_natures");
	/*sql_drop_table("spip_cog_zauers");
	sql_drop_table("spip_cog_zauer_espace");
	sql_drop_table("spip_cog_zauer_categories");*/

	effacer_meta($nom_meta_base_version);
}


function cog_nouvelle_definition_regionale(){

$tab_region=array();
include_spip('cog_config');
$conf_region = cog_config_tab_fichier('cog_regions_ancienne');


$fichier=realpath(_DIR_PLUGIN_COG.'/data/'.$conf_region['fichier']);

$pointeur_fichier = fopen($fichier,"r");

if($pointeur_fichier<>0){
fgets($pointeur_fichier, 4096);
while (!feof($pointeur_fichier)){
	$ligne= fgets($pointeur_fichier, 4096);
	$tab=explode("\t",$ligne);
		if(count($tab)>4) {
			if(trim($tab[0])!=trim($tab[5]))
				$tab_region[trim($tab[0]).'']=trim($tab[5]);
			sql_updateq('spip_cog_regions_anciennes',array('region2016'=>intval(trim($tab[5]))),'code = '.intval(trim($tab[0])));
			$data=array('region'=>intval(trim($tab[5])),'region_ancienne'=>intval(trim($tab[0])));
			sql_updateq('spip_cog_departements',$data,'region = '.intval(trim($tab[0])) );
		}
	}
}



$tab_table=array(
	'spip_cog_communes',
	'spip_cog_cantons',
	'spip_cog_arrondissements');

foreach($tab_table as $table){
	foreach($tab_region as $anc=>$nouvelle){
		$data=array('region'=>intval($nouvelle));
		sql_updateq($table,$data,'region = '.intval($anc));
		}
	}

}

function cog_peupler_base()
{
	include_spip('inc/config');
	ecrire_config('cog/chemin_donnee','cog_donnees/');
}

?>

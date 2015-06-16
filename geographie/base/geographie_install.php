<?php
include_spip('base/create');

function geographie_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if ($current_version==0.0){
			if (include_spip('base/geographie')){
				creer_base();
				echo "Geographie Install<br/>";
				$importer_geographie = charger_fonction('geographie','imports');
				$importer_geographie();
				if (include_spip('base/arrondissements')){
					echo "Création des arrondissements : <br/>", _DIR_PLUGIN_GEOGRAPHIE.'base/arrondissements.txt<br/>';
					$importer_arrondissements = charger_fonction('arrondissements','imports');
					$importer_arrondissements();
				}
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			else return;
		}
		else if ($current_version<0.2){
			if (include_spip('base/geographie')){
				sql_drop_table("spip_geo_pays");
				creer_base();
				echo "Mise &agrave; des pays<br/>";
				reimport_pays();
				ecrire_meta($nom_meta_base_version,$current_version=0.2,'non');
			}
			else return;
		}
		else if ($current_version<0.3){
			if (include_spip('base/arrondissements')){
				creer_base();
				echo "Création des arrondissements : <br/>", _DIR_PLUGIN_GEOGRAPHIE.'base/arrondissements.txt<br/>';
				$importer_arrondissements = charger_fonction('arrondissements','imports');
				$importer_arrondissements();
				ecrire_meta($nom_meta_base_version,$current_version=0.3,'non');
			}
			else return;
		}
		else if ($current_version<0.4){
			if (include_spip('base/geographie')){
				sql_drop_table("spip_geo_pays");
				creer_base();
				echo "Mise &agrave; des pays<br/>";
				reimport_pays();
				ecrire_meta($nom_meta_base_version,$current_version=0.4,'non');
			}
			else return;
		}
		ecrire_metas();
	}
}

function geographie_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_geo_pays");
	sql_drop_table("spip_geo_regions");
	sql_drop_table("spip_geo_departements");
	sql_drop_table("spip_geo_arrondissements");
	sql_drop_table("spip_geo_communes");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

function reimport_pays(){
	include_spip('imports/pays');
	sql_insertq_multi('spip_geo_pays',$GLOBALS['liste_pays']);
}


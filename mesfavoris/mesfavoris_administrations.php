<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2012 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function mesfavoris_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['favoris']='favoris';

	return $interface;
}

/**
 * Declaration des tables principales
 *
 * @param array $tables_principales Un array de description des tables
 * @return array $tables_principales L'Array de description complété
 */
function mesfavoris_declarer_tables_principales($tables_principales){
	$spip_favoris = array(
		"id_favori"	=> "bigint(21) NOT NULL",
		"id_auteur"	=> "bigint DEFAULT '0' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
	);

	$spip_favoris_key = array(
		"PRIMARY KEY"		=> "id_favori",
		"KEY auteur_objet"	=> "id_auteur,id_objet,objet",
		"KEY id_auteur"	=> "id_auteur",
		"KEY id_objet" => "id_objet",
		"KEY objet" => "objet"
	);

	$tables_principales['spip_favoris'] =
		array('field' => &$spip_favoris, 'key' => &$spip_favoris_key);

	return $tables_principales;
}

/**
 * Mise à jour des tables
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function mesfavoris_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = "0.0.0";
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'1.0.0','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('base/serial');
			creer_ou_upgrader_table("spip_favoris",$GLOBALS['tables_principales']['spip_favoris'],true);
			
			// recuperer l'ancienne base si possible (hum)
			$trouver_table = charger_fonction("trouver_table","base");
			$trouver_table(''); // vider le cache
			if ($desc = $trouver_table("spip_favtextes")){
				$res = sql_select("*","spip_favtextes");
				while ($row = sql_fetch($res)){
					sql_insertq("spip_favoris", array('id_auteur'=>$row['id_auth'],'id_objet'=>$row['id_texte'],'objet'=>'article'));
					sql_delete("spip_favtextes","id_favtxt=".$row['id_favtxt']);
				}
				sql_drop_table("spip_favtextes");
			}
			ecrire_meta($nom_meta_base_version,$current_version="1.0.0",'non');
		}
		if (version_compare($current_version,'1.1.0','<')){
			sql_alter("TABLE spip_favoris ADD INDEX objet (objet)");
			sql_alter("TABLE spip_favoris ADD INDEX id_objet (id_objet)");
			ecrire_meta($nom_meta_base_version,$current_version="1.1.0",'non');
		}
	}
}


/**
 * Desinstallation du plugin
 *
 * @param string $nom_meta_base_version
 */
function mesfavoris_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table("spip_favoris");
	effacer_meta($nom_meta_base_version);
}

?>
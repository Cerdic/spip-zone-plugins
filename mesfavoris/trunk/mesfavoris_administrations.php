<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Mesfavoris\Installation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation / Mise à jour des tables des favoris
 *
 * Crée les tables SQL du plugin (spip_favoris)
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 */
function mesfavoris_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = "0.0.0";
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
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
		if (version_compare($current_version,'1.2.0','<')){
			sql_alter("TABLE spip_favoris ADD COLUMN categorie VARCHAR(50) DEFAULT '' NOT NULL");
			sql_alter("TABLE spip_favoris ADD INDEX categorie (categorie)");
			ecrire_meta($nom_meta_base_version,$current_version="1.2.0",'non');
		}
	}
}


/**
 * Désinstallation du plugin
 *
 * Supprime les tables SQL du plugin (spip_favoris)
 * 
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 */
function mesfavoris_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table("spip_favoris");
	effacer_meta($nom_meta_base_version);
}

?>

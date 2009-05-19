<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Tables de jointures
 *
 * @param array $tables_auxiliaires
 * @return array
 */
function urls_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_urls = array(
		"url"			=> "VARCHAR(255) NOT NULL",
		// la table cible
		"type"			=> "varchar(15) DEFAULT 'article' NOT NULL",
		// l'id dans la table
		"id_objet"		=> "bigint(21) NOT NULL",
		// pour connaitre la plus recente.
		// ATTENTION, pas on update CURRENT_TIMESTAMP implicite
		// et pas le nom maj, surinterprete par inc/import_1_3
		"date"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

	$spip_urls_key = array(
		"PRIMARY KEY"		=> "url",
		"KEY type"		=> "type, id_objet");

	$tables_auxiliaires['spip_urls'] = array(
		'field' => &$spip_urls,
		'key' => &$spip_urls_key);

	return $tables_auxiliaires;
}


/**
 * Installation/maj des tables urls
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function urls_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if ($current_version==0.0){
			include_spip('base/create');
			// creer les tables
			creer_base();
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	}
}

/**
 * Desinstallation/suppression des tables urls
 *
 * @param string $nom_meta_base_version
 */
function urls_vider_tables($nom_meta_base_version) {
	// repasser dans les urls par defaut
	ecrire_meta('type_urls','page');
	sql_drop_table("spip_urls");
	effacer_meta($nom_meta_base_version);
}

?>
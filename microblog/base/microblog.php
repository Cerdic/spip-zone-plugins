<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Table principale
 * un champ microblog sur les articles
 *
 * @param array $tables_principales
 * @return array
 */
function microblog_declarer_tables_principales($tables_principales) {
	$tables_principales['spip_articles']['field']['mircroblog'] = "VARCHAR(140) DEFAULT '' NOT NULL";
	
	return $tables_principales;
}

/**
 * maj dede la table article
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function microblog_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if ($current_version==0.0){
			sql_alter("table spip_articles ADD microblog VARCHAR(140) DEFAULT '' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	}
}

/**
 * Desinstallation/suppression
 *
 * @param string $nom_meta_base_version
 */
function microblog_vider_tables($nom_meta_base_version) {
	sql_alter("table spip_articles DROP microblog");
	effacer_meta($nom_meta_base_version);
}


?>
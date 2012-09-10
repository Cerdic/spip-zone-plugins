<?php

/**
 * Fonctions utiles pour les squelettes de la fabrique
 *
 * @package SPIP\Fabrique\Fonctions
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


if (!function_exists("lister_tables_sql")) {
/**
 * Liste les tables sql de chaque base déclarée
 * Avec la connexion par defaut
 * - spip_articles
 * - spip_truc
 * Avec d'autres connexions
 * - autre:spip_articles
 * - autre:spip_truc 
 *
 * @return array
 *     Liste des tables.
**/
function lister_tables_sql() {
	static $tables = false;
	if ($tables !== false) {
		return $tables;
	}
	// retrouver toutes les bases
	include_spip('inc/install');
	$bases = bases_referencees(_FILE_CONNECT_TMP);
	$tables = array();
	foreach ($bases as $connect) {
		$base = ($connect == 'connect') ? '' : $connect . ':';
		$liste = sql_alltable('%', $connect);
		sort($liste);
		foreach ($liste as $table) {
			$cle = $base . $table;
			$tables[$cle] = $cle;
		}
	}
	return $tables;
}
}
?>

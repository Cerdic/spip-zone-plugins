<?php

/**
 * Transfert la bdd de l'ancien site dans le nouveau
 *
 * Du coup, tout le site sera planté ensuite.
 * On essaie de réduire au minimum la difficulté de transition.
 *
 * Donc, on :
 * - supprime toutes les tables de la nouvelle base
 * - on remet l'ancienne base
 * - on vide les caches
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/** Éviter les taches cron pendant ce temps */
function inc_genie() {
	return;
}
define('_DIRECT_CRON_FORCE', true);


/**
 * Transfert des données SQL sur la base de données destination
**/
function migrateur_mig_bdd_destination_put_dump() {

	// récupérer les plugins de migration
	$migrateurs = migrateur_obtenir_plugins_actifs('migrateur');

	// conserver la conf
	$conf = lire_config('migrateur');

	// vider toutes les tables avant insertion
	migrateur_supprimer_toutes_tables_sql();

	// copier l'ancienne bdd
	migrateur_copier_la_bdd();

	// remettre les plugins de migration
	migrateur_ajouter_plugins_actifs($migrateurs);

	// restaurer la conf
	ecrire_config('migrateur', $conf);

	// On vide le cache
	migrateur_vider_cache();

	// Il faut rediriger sur ecrire/ pour mettre à jour la bdd ensuite. C'est le plus simple
	migrateur_log(">> Redirection sur ecrire/ pour finaliser l'installation");
	include_spip('inc/headers');
	// cf ecrire/index.php pour contourner exec=demande_mise_a_jour automatique...
	$ou = generer_url_ecrire('upgrade','reinstall=non', false, _DIR_RESTREINT_ABS);
	redirige_par_entete(parametre_url(generer_url_public('login'),'url', $ou, '&'));

}


/**
 * Supprime toutes les table SQL de la base en cours !
**/
function migrateur_supprimer_toutes_tables_sql() {
	migrateur_log("Vider toutes les tables avant insertion");

	// vider toutes les tables avant insertion
	$tables = sql_alltable('%');
	foreach($tables as $table) {
		sql_drop_table($table);
	}

	// mieux vaut 2 fois qu'une
	$tables = sql_alltable('%');
	if ($tables) {
		migrateur_log("/!\ Tables encore là : " . implode(',', $tables));
		spip_log('/!\ tables non supprimées la première fois', 'migration_concurrences');
		spip_log($tables, 'migration_concurrences');
		arsort($tables);
		foreach($tables as $table) {
			sql_drop_table($table);
		}
		$tables = sql_alltable('%');
		if ($tables) {
			spip_log('/!\/!\ tables non supprimées la deuxième fois', 'migration_concurrences');
			spip_log($tables, 'migration_concurrences');
			migrateur_log("/!\/!\ Tables encore là : " . implode(',', $tables));
		}
	}
}


/**
 * Copier le contenu de l'ancienne bdd dans la nouvelle (qui doit être au préalable vidée)
**/
function migrateur_copier_la_bdd($source_sql = '') {

	$dest = migrateur_destination();
	$sauvegarde = $dest->dir . '/tmp/dump/' . ($source_sql ? $source_sql : 'migrateur.sql');

	migrateur_log("Copie de la BDD par MySQL");

	//$mysql_cmd = "mysql --user={$dest->sql->user} --password={$dest->sql->pass} --default_character_set=utf8 {$dest->sql->bdd} < $sauvegarde";
	$mysql_cmd = "mysql --user={$dest->sql->user} --password={$dest->sql->pass} {$dest->sql->bdd} < $sauvegarde";

	exec($mysql_cmd);
}



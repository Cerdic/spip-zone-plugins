<?php


/**
 * Autres fonctions d'exemples de migrations
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Installer les plugins
**/
function migrateur_exemple_installer_plugins() {
	migrateur_activer_plugin_prefixes(array(
		'agenda',
		'pages',
		'polyhierarchie',
		'yaml',
	));
}


/**
 * Configurer des metas
**/
function migration_exemple_configurer_site() {
	ecrire_meta('adresse_site', "http://www2.exemple.com");
	ecrire_meta('image_process', "gd2");
	ecrire_meta('type_urls', "arbo");
	ecrire_meta('charset_sql_connexion', "utf-8"); // utf-8
	ecrire_meta('articles_redirection', 'oui');
	ecrire_meta('activer_breves', 'non');
	ecrire_meta('activer_logos_survol', 'oui');
}

/**
 * Configurer des metas (exemple inc/config)
**/
function migration_exemple_configurer_site() {
	include_spip('inc/config');
	ecrire_config('adresse_site', "http://www2.exemple.com");
	ecrire_config('image_process', "gd2");
	ecrire_config('type_urls', "arbo");
	ecrire_config('plugin/nom', 'exemple');
}


/**
 * Définir les compositions
**/
function migrateur_exemple_definir_compositions() {

	if (!include_spip('inc/compositions')) {
		migrateur_log("Le plugin «compositions» n'est pas actif. Action impossible");
		return false;
	}

	migrateur_log("Appliquer la composition de galerie");
	sql_updateq('spip_rubriques', array(
		'composition'              => 'galerie', // nom de la compo
		'composition_lock'         => 0,        // 1 pour verrouillage webmestre
		'composition_branche_lock' => 0,        // 1 pour verrouillage webmestre sur la branche
		), array(
			sql_in('id_rubrique', array(550))     // liste des ids de rubriques
	));

	// autres compos
	// [...]

	// a la fin, recréer le cache
	compositions_cacher();
}


/**
 * Supprimer des tables
**/
function migration_exemple_nettoyage_sql() {
	sql_drop_table('spip_ortho_cache');
	sql_drop_table('spip_ortho_dico');
	sql_drop_table('spip_geo_pays');
	sql_alter('TABLE spip_auteurs DROP COLUMN id_user');
}

/**
 * Supprimer des tables (autre exemple)
**/
function migration_exemple_supprimer_tables_inutiles() {
	$tables = sql_alltable('%');
	sort($tables);
	foreach($tables as $table) {
		if (substr($table, 0, strlen('contacts_')) == 'contacts_') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('wa_')) == 'wa_') {
			$x = sql_drop_table($table);
		}
		if (in_array($table, array(
			'TABLE 143', 'temp_cle', 'test_md5',
			'spip_mots_articles_bk', 'spip_mots_bk',
			'reperes', 'spip_articles_bak', 'spip_articles_test'))) {
			$x = sql_drop_table($table);
		}
	}
}


/**
 * Migrer le contenu d'une table dans une autre
 * 
 * S'appuie sur migrateur_deplacer_table_complete()
**/
function migration_exemple_migrer_projets_cadres() {
	// migrer les projets cadres
	$ok = migrateur_deplacer_table_complete('spip_types_facturation', 'spip_projets_cadres', array(
		'id_type_facturation'   => 'id_projets_cadre',
		'nom_type_facturation'  => 'titre',
		'commentaires'          => 'descriptif',
	));
	if ($ok) {
		migrateur_log('  > Suppression de la table source (spip_types_facturation)');
		sql_drop_table('spip_types_facturation');
	}
}


/**
 * Migrer le contenu d'une table dans une autre (autre exemple avec champ inutiles et callback)
 *
 * Appelle migrateur_deplacer_table_complete(). Certains champs ne servent plus
 * (pas de correspondance). Pour chaque ligne traitée, un appel à une fonction de callback
 * est effectué afin éventuellement de modifier une ou des données insérées.
**/
function proj_migrer_factures() {
	$ok = migrateur_deplacer_table_complete('spip_factures_old', 'spip_factures', array(
		'id_facture'            => 'id_facture',
		'id_organisation'       => 'id_organisation',
		'id_type_document'      => '', // migré en composition
		'num_facture'           => 'num_facture',
		'num_devis'             => 'num_devis',
		'date_facture'          => 'date_facture',
		'date_payement'         => '', // sera dans la table règlements ?
		'libelle_facture'       => 'libelle_facture',
		'conditions'            => 'conditions',
		'reglement'             => 'reglement',
		'delais_validite'       => 'delais_validite',
		'fin_validite'          => 'fin_validite',
		'id_type_presta'        => '', // à migrer en mots clés
		'montant'               => 'montant',
		'charge_estimee'        => '', // correspond au projet
		'nb_heures_vendues'     => 'quantite',
		'nota_bene'             => 'nota_bene',
		'delais_realisation'    => '', // correspond au projet
	), $options = array(
		'callback_ligne' => 'proj_callback_factures_ligne',
	));

	if ($ok) {
		migrateur_log('  > Suppression de la table source temporaire (spip_factures_old)');
		sql_drop_table('spip_factures_old');
	}
}


/**
 * Callback sur les données d'une ligne insérée d'une facture
 * afin de modifier les compositions
 *
 * @param array $couples_inseres Données insérées : couples (cle => valeur)
 * @param array $couples_anciens Anciennes données
 * @return array Données insérées
**/
function proj_callback_factures_ligne($couples_inseres, $couples_anciens) {
	// tableau de correspondances des status
	static $compositions = array(
		NULL => '',
		''   => '',
		0    => '',

		1 => '',
		2 => 'devis',
		3 => 'avoir',
		4 => 'performa',
	);

	if (isset($compositions[$couples_anciens['id_type_document']])) {
		$couples_inseres['composition'] = $compositions[$couples_anciens['id_type_document']];
	}

	// orga émetrice : atelier cym
	$couples_inseres['id_organisation_emettrice'] = 9;

	return $couples_inseres;
}





/**
 * Suppression des vignettes de logo de SPIP 1.8.3
**/ 
function migration_exemple_supprimer_vignettes_logos() {
	$ori = MIGRATEUR_DESTINATION_DIR . 'IMG/';
	foreach (scandir($ori) as $filename) {
		if (preg_match('/.*-[0-9]+x[0-9]+\.(jpg|gif|png)/i', $filename)) {
			unlink($ori . $filename);
		}
	}
}


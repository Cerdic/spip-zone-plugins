<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/albums/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'titre_migrer_albums' => 'Migrer des Articles en Albums',
	'explication_migration_albums_article_1' => 'Si votre site contient des albums basé sur des articles,
vous pouvez utiliser cet outil pour les transformer automatiquement en albums.',
	'explication_migration_albums_article_2' => 'Dans la rubrique sélectionnée, un album sera créé à partir de chaque article, selon les réglages ci-dessous.',
	'explication_migration_albums_article_fin' => 'Seuls les articles publiés et n\'ayant pas déjà d\'album associé seront migrés.
	Aucune donnée ne sera supprimée sur les articles : si le résultat ne vous convient pas, il suffit de désinstaller le plugin albums pour retrouver votre rubrique comme avant la migration.',

	'label_rubrique_source' => 'Rubrique à migrer',
	'label_toute_la_branche_oui' => 'Migrer aussi toutes les sous-rubriques',
	'label_refuser_articles_oui' => 'Passer les articles en "refusé" après leur transformation en albums',
	'label_groupes_mots' => 'Associer les mots des groupes suivants',
	'bouton_migrer' => 'Prévisualiser la migration',
	'bouton_lancer_migration' => 'Lancer la migration',

	'erreur_choix_incorrect' => 'Ce choix n\'est pas permis',

	'info_migration_articles' => "Articles à migrer :",
	'info_migration_articles_reussi' => "Articles migrés :",

);

?>

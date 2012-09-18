<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/agenda/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'titre_migrer_agenda' => 'Migrer un Agenda d\'articles',
	'explication_migration_agenda_article_1' => 'Si votre site contient un agenda basé sur des articles,
vous pouvez utiliser cet outil pour le transformer automatiquement en événements.',
	'explication_migration_agenda_article_2' => 'Dans la rubrique agenda sélectionnée, un événement sera créé et renseigné pour dater chaque article, selon les réglages ci-dessous.',
	'explication_migration_agenda_article_fin' => 'Seuls les articles publiés et n\'ayant pas déjà d\'événement seront migrés.
	Aucune donnée ne sera supprimée sur les articles : si le résultat ne vous convient pas, il suffit de désinstaller le plugin Agenda pour retrouver votre rubrique comme avant la migration.',

	'label_rubrique_source' => 'Rubrique Agenda à migrer',
	'label_toute_la_branche_oui' => 'Migrer aussi toutes les sous-rubriques',
	'label_champ_date_debut' => 'Date de début',
	'label_champ_date_fin' => 'Date de fin',
	'label_champ_date' => 'Date de publication',
	'label_champ_date_redac' => 'Date de rédaction antérieure',
	'label_horaire' => 'Horaire',
	'label_horaire_oui' => 'Prendre en compte l\'heure',
	'label_horaire_non' => 'Pas d\'horaire (événements par journées)',
	'label_groupes_mots' => 'Associer les mots des groupes suivants',
	'bouton_migrer' => 'Prévisualiser la migration',
	'bouton_lancer_migration' => 'Lancer la migration',

	'erreur_choix_incorrect' => 'Ce choix n\'est pas permis',

	'info_migration_articles' => "Articles à migrer :",
	'info_migration_articles_reussi' => "Articles migrés :",

);

?>

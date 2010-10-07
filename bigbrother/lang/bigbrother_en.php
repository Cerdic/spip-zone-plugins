<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'action ajouter document' => '@qui@ has added the document @id@',
	'action_entree_objet' => '@qui@ entered on the page of the object "@type@" number @id@',
	'action_inserer_objet' => '@qui@ created the object "@type@" number @id@',
	'action_instituer_objet' => '@qui@ modified the status of the object "@type@" number @id@',
	'action_modifier_objet' => '@qui@ modified the object "@type@" number @id@',
	'action_sortie_objet' => '@qui@ went out from the page of the object "@type@" number @id@',

	// C
	'configuration_description' => 'It allows you to configure what you want to store or not.',
	'configuration_enregistrer_connexion_anonyme' => 'Save anonymous connections (users not registered)',
	'configuration_enregistrer_insertions' => 'Log insertions of objects',
	'configuration_enregistrer_institutions' => 'Log status changes of the objects',
	'configuration_enregistrer_ip' => 'Allways store the IP address',
	'configuration_enregistrer_modifications' => 'Log changes to objects',
	'configuration_enregistrer_visite' => 'Log all visits by people with accounts',
	'configuration_enregistrer_visite_article' => 'Save time spent on an article of persons having an account',
	'configuration_journalisation_base' => 'Actions to log in the database',
	'configuration_options' => 'Options',
	'configuration_titre' => 'Big Brother',

	// D
	'date_debut' => 'Entrance',
	'date_fin' => 'Exit',
	'detail' => 'See details',

	// E
	'erreur_statistiques' => '"id_article" or "id_auteur" missing',
	'explication_exclure_auteurs' => 'The selected users will be excluded from the visible results but will be still registered in the database in case we want to go back.',
	'explication_page_stats' => 'This page lists the events that took place on the site',

	// F
	'filtrer_par_action' => 'Only filter the action "@action@"',

	// I
	'info_aucun_filtre' => 'The results are not filtered',
	'info_aucun_resultat' => 'No results match your selection.',
	'info_donnees_actions' => '@nb@ different actions',
	'info_donnees_concernant' => 'These data affect',
	'info_donnees_dates' => '@nb@ different days',
	'info_donnees_users' => '@nb@ unique different users',
	'info_nombre_resultat' => '@nb@ item(s) in the log match the criteria.',
	'info_stats_id_objet_visite_auteurs_nb' => 'The object of type "@objet@" number @id_objet@ was consulted by @nb@ user(s) for a total number of @nb_visites@ visits.',
	'info_stats_visite_auteurs_nb' => 'All items on the site have been consulted by @nb@ user(s) for a total number of @nb_visites@ visits.',
	'info_stats_objet_visite_auteurs_nb' => 'The objects of type "@objet@" were consulted by @nb@ user(s) for a total number of @nb_visites@ visits.',

	// L
	'label_date_debut' => 'Start date',
	'label_date_fin' => 'End date',
	'label_exclure_auteurs' => 'Exclude some users',
	'label_nombre_pagination' => 'Paginate by :',
	'label_suppression_journal_criteres' => 'Deletion of the entries related to your criteria',

	// M
	'message_suppression_journal_definitives' => 'The deletion of these data will be definitive',

	// N
	'naviguer_journal_stats' => 'Events on the site',

	// P
	'pagination_tout_afficher' => 'Show all',
	// T
	'temps_median' => 'Median time',
	'temps_moyen' => 'Average time',
	'temps_passe' => 'Elapsed time',
	'temps_total' => 'Total time',
	'thead_action' => 'Action',
	'thead_date' => 'Date',
	'thead_date_entree' => 'Entrance',
	'thead_date_sortie' => 'Exit',
	'thead_duree' => 'Duration',
	'thead_id_auteur' => 'User',
	'thead_id_journal' => 'Journal entry',
	'thead_id_objet' => 'Id',
	'thead_nombre' => 'Amount',
	'thead_objet' => 'Object',
	'thead_temps_consultation' => 'Duration of consultation',
	'thead_titre' => 'Title',
	'title_afficher_uniquement_id_objet' => 'Only show results of the object "@objet@" number @id@',
	'title_afficher_uniquement_objet' => 'Only show results of "@objet@" objects',
	'title_afficher_uniquement_user' => 'Only show results of the user "@user@"',
	'title_supprimer_filtre' => 'Remove this filter',
	'titre_actions_nombre_date' => 'Actions by day',
	'titre_actions_nombre' => 'Amount of actions',
	'titre_actions_nombre_par_auteur' => 'Amount of actions by "@auteur@"',
	'titre_actions_nombre_par_auteur_date' => 'Amount of actions by "@auteur@" by date',
	'titre_compilation_resultats' => 'Compilation of results',
	'titre_filtrer_action' => 'Filter by action',
	'titre_filtres_utilises' => 'Current filters',
	'titre_limiter_temps' => 'Limit the time interval',
	'titre_liste_entrees_journal' => 'List of log entries',
	'titre_page_stats' => 'Events on the site',
	'titre_page_stats_id_auteur' => 'Events concerning the user @auteur@',
	'titre_statistiques_visites_id_objet' => 'Visits statitics of "@titre@" (@objet@ #@id_objet@)',
	'titre_statistiques_visites_id_objet_id_auteur' => 'Visits statitics of "@titre@" (@objet@ #@id_objet@) by the user @auteur@',
	'titre_statistiques_visites_objet' => 'Visits statitics of objects of type "@objet@"',
	'titre_statistiques_visites_objet_id_auteur' => 'Visits statitics of objects of type "@objet@" by the user @auteur@',
	'titre_visites_par_auteurs' => 'By users',
	'titre_visites_par_date' => 'By days',
	'titre_visites_par_id_objet' => 'By ID of objects',

	// V
	'visites_article_auteur' => 'Visits from @nom@ on the article <em>@titre@</em>',
	'voir_statistiques_article' => 'Visit statistics of this article',
	'voir_statistiques_auteur' => 'Visit statistics of this person'

);

?>

<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'action ajouter document' => '@qui@ a ajout&eacute; le document @id@',
	'action_entree_objet' => '@qui@ est entr&eacute; sur la page de l\'objet "@type@" num&eacute;ro @id@',
	'action_inserer_objet' => '@qui@ a cr&eacute;&eacute; l\'objet "@type@" num&eacute;ro @id@',
	'action_instituer_objet' => '@qui@ a modifi&eacute; le statut de l\'objet "@type@" num&eacute;ro @id@',
	'action_modifier_objet' => '@qui@ a modifi&eacute; l\'objet "@type@" num&eacute;ro @id@',
	'action_sortie_objet' => '@qui@ est sorti de la page de l\'objet "@type@" num&eacute;ro @id@',

	// C
	'configuration_description' => 'Permet de configurer ce que l\'on d&eacute;sire enregistrer ou pas.',
	'configuration_enregistrer_connexion_anonyme' => 'Enregistrer les connexions anonymes (utilisateurs non identifi&eacute;s)',
	'configuration_enregistrer_insertions' => 'Journaliser les insertions d\'objets',
	'configuration_enregistrer_institutions' => 'Journaliser les modifications de statuts des objets',
	'configuration_enregistrer_ip' => 'Toujours enregistrer l\'adresse ip',
	'configuration_enregistrer_modifications' => 'Journaliser les modifications des objets',
	'configuration_enregistrer_visite' => 'Enregistrer toutes les visites des personnes ayant un compte',
	'configuration_enregistrer_visite_article' => 'Enregistrer le temps passé sur un article des personnes ayant un compte',
	'configuration_journalisation_base' => 'Les actions &agrave; journaliser en base',
	'configuration_options' => 'Options',
	'configuration_titre' => 'Big Brother',

	// D
	'date_debut' => 'Entr&eacute;e',
	'date_fin' => 'Sortie',
	'detail' => 'Voir le d&eacute;tail',

	// E
	'erreur_statistiques' => '"id_article" ou "id_auteur" manquant',
	'explication_exclure_auteurs' => 'Les auteurs s&eacute;lectionn&eacute;s seront exclus des r&eacute;sultats visibles mais seront quand m&ecirc;me enregistr&eacute; en base pour les cas o&ugrave; l\'on souhaite revenir en arri&egrave;re.',
	'explication_page_stats' => 'Cette page liste les &eacute;v&egrave;nements qui ont eu lieu sur le site',

	// F
	'filtrer_par_action' => 'Filtrer uniquement l\'action &laquo;@action@&raquo;',

	// I
	'info_aucun_filtre' => 'Les r&eacute;sultats ne sont pas filtr&eacute;s',
	'info_aucun_resultat' => 'Aucun r&eacute;sultat ne correspond &agrave; votre s&eacute;lection.',
	'info_donnees_actions' => '@nb@ actions diff&eacute;rentes',
	'info_donnees_concernant' => 'Ces donn&eacute;es concernent',
	'info_donnees_dates' => '@nb@ jours diff&eacute;rents',
	'info_donnees_users' => '@nb@ utilisateurs uniques diff&eacute;rents',
	'info_nombre_resultat' => 'Il y a @nb@ entr&eacute;e(s) dans le journal correspondant aux crit&egrave;res.',
	'info_stats_id_objet_visite_auteurs_nb' => 'L\'objet de type &laquo; @objet@ &raquo; num&eacute;ro @id_objet@ a &eacute;t&eacute; consult&eacute; par @nb@ utilisateur(s) pour un nombre de visites total de @nb_visites@.',
	'info_stats_visite_auteurs_nb' => 'L\'ensemble des objets du site ont &eacute;t&eacute; consult&eacute;s par @nb@ utilisateur (s) pour un nombre de visites total de @nb_visites@.',
	'info_stats_objet_visite_auteurs_nb' => 'Les objets de type &laquo; @objet@ &raquo; ont &eacute;t&eacute; consult&eacute;s par @nb@ utilisateur (s) pour un nombre de visites total de @nb_visites@.',

	// L
	'label_date_debut' => 'Date de d&eacute;but',
	'label_date_fin' => 'Date de fin',
	'label_exclure_auteurs' => 'Exclure certains auteurs',
	'label_nombre_pagination' => 'Paginer par :',
	'label_suppression_journal_criteres' => 'Suppression des entr&eacute;es li&eacute;es &agrave; vos crit&egrave;res',

	// M
	'message_suppression_journal_definitives' => 'La suppression de ces donn&eacute;es sera d&eacute;finitive',

	// N
	'naviguer_journal_stats' => '&Eacute;v&egrave;nements du site',

	// P
	'pagination_tout_afficher' => 'Tout afficher',
	// T
	'temps_median' => 'Temps m&eacute;dian',
	'temps_moyen' => 'Temps moyen',
	'temps_passe' => 'Temps pass&eacute;',
	'temps_total' => 'Temps total',
	'thead_action' => 'Action',
	'thead_date' => 'Date',
	'thead_date_entree' => 'Entr&eacute;e',
	'thead_date_sortie' => 'Sortie',
	'thead_duree' => 'Dur&eacute;e',
	'thead_id_auteur' => 'Utilisateur',
	'thead_id_journal' => 'Entr&eacute;e du journal',
	'thead_id_objet' => 'Id',
	'thead_nombre' => 'Nombre',
	'thead_objet' => 'Objet',
	'thead_temps_consultation' => 'Dur&eacute;e de consultation',
	'thead_titre' => 'Titre',
	'title_afficher_uniquement_id_objet' => 'N\'afficher que les r&eacute;sultats concernant l\'objet &laquo;@objet@&raquo; num&eacute;ro @id@',
	'title_afficher_uniquement_objet' => 'N\'afficher que les r&eacute;sultats concernant les objets &laquo;@objet@&raquo;',
	'title_afficher_uniquement_user' => 'N\'afficher que les r&eacute;sultats concernant l\'utilisateur @user@',
	'title_supprimer_filtre' => 'Supprimer ce filtre',
	'titre_actions_nombre_date' => 'Actions par jour',
	'titre_actions_nombre' => 'Nombre d\'actions',
	'titre_actions_nombre_par_auteur' => 'Nombre d\'actions effectu&eacute;es par &laquo; @auteur@ &raquo;',
	'titre_actions_nombre_par_auteur_date' => 'Nombre d\'actions effectu&eacute;es par &laquo; @auteur@ &raquo; par date',
	'titre_compilation_resultats' => 'Compilation des r&eacute;sultats',
	'titre_filtrer_action' => 'Filtrer par action',
	'titre_filtres_utilises' => 'Filtres actuels',
	'titre_limiter_temps' => 'Limiter l\'intervalle de temps',
	'titre_liste_entrees_journal' => 'Liste des entr&eacute;es du journal',
	'titre_page_stats' => '&Eacute;v&egrave;nements sur le site',
	'titre_page_stats_id_auteur' => '&Eacute;v&egrave;nements concernant l\'utilisateur @auteur@',
	'titre_statistiques_visites_id_objet' => 'Statistiques de visites de &laquo; @titre@ &raquo; (@objet@ #@id_objet@)',
	'titre_statistiques_visites_id_objet_id_auteur' => 'Statistiques de visites de &laquo; @titre@ &raquo; (@objet@ #@id_objet@) de l\'utilisateur @auteur@',
	'titre_statistiques_visites_objet' => 'Statistiques de visites des objets de type &laquo; @objet@ &raquo;',
	'titre_statistiques_visites_objet_id_auteur' => 'Statistiques de visites des objets de type &laquo; @objet@ &raquo; de l\'utilisateur @auteur@',
	'titre_visites_par_auteurs' => 'Par utilisateurs',
	'titre_visites_par_date' => 'Par journ&eacute;es',
	'titre_visites_par_id_objet' => 'Par ID num&eacute;riques des objets',

	// V
	'visites_article_auteur' => 'Visites de @nom@ sur l\'article <em>@titre@</em>',
	'voir_statistiques_article' => 'Statistiques de visite de l\'article',
	'voir_statistiques_auteur' => 'Statistiques de visite de cette personne'

);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/mes_fichiers/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_configurer' => 'Mes fichiers',
	'bouton_mes_fichiers' => 'Sauvegarder mes fichiers',
	'bouton_sauver' => 'Sauvegarder',
	'bouton_tout_cocher' => 'Tout cocher',
	'bouton_tout_decocher' => 'Tout décocher',
	'bouton_voir' => 'Voir',
	'bulle_bouton_voir' => 'Voir le contenu de l’archive',

	// C
	'colonne_nom' => 'Nom',

	// E
	'erreur_aucun_fichier_sauver' => 'Il n’y a aucun fichier à sauvegarder',
	'erreur_repertoire_trop_grand' => 'Ce répertoire dépasse la limite de @taille_max@ MB et ne peut être sauvegardé.',
	'explication_cfg_duree_sauvegarde' => 'Saisir la durée de conservation des sauvegardes en jours',
	'explication_cfg_frequence' => 'Saisir la fréquence des sauvegardes en jours',
	'explication_cfg_notif_mail' => 'Saisir les adresses en les séparant par des virgules ",". Ces adresses s’ajoutent à celle du webmestre du site.',
	'explication_cfg_prefixe' => 'Saisir le préfixe accolé à chaque archive',
	'explication_cfg_taille_max_rep' => 'Saisir la taille maximale en MB des répertoires à sauvegarder',

	// I
	'info_liste_a_sauver' => 'Liste des fichiers et dossiers pouvant être sauvegardés :',
	'info_sauver_1' => 'Cette option fabrique un fichier d’archive contenant les données de personnalisation du site comme le dernier dump de sauvegarde de la base, les dossiers des squelettes nommés, le dossier images...',
	'info_sauver_2' => 'Le fichier d’archive est créé dans <em>tmp/mes_fichiers/</em> et se nomme <em>@prefixe@_aaaammjj_hhmmss.zip</em>.',
	'info_sauver_3' => 'La sauvegarde automatique est activée (fréquence en jours : @frequence@).',

	// L
	'label_cfg_duree_sauvegarde' => 'Conservation des archives',
	'label_cfg_frequence' => 'Fréquence d’archivage',
	'label_cfg_nettoyage_journalier' => 'Activer le nettoyage journalier des archives',
	'label_cfg_notif_active' => 'Activer les notifications',
	'label_cfg_notif_mail' => 'Adresses email à notifier',
	'label_cfg_prefixe' => 'Préfixe',
	'label_cfg_sauvegarde_reguliere' => 'Activer la sauvegarde régulière',
	'label_cfg_taille_max_rep' => 'Taille maximale des dossiers',
	'legende_cfg_generale' => 'Paramètres d’archivage généraux',
	'legende_cfg_notification' => 'Notifications',
	'legende_cfg_sauvegarde_reguliere' => 'Traitements automatiques',

	// M
	'message_cleaner_sujet' => 'Nettoyage des sauvegardes',
	'message_notif_cleaner_intro' => 'La suppression automatique des sauvegardes obsolètes (dont la date est antérieure à @duree@ jours) a été effectuée avec succès. Les archives suivantes ont été supprimées : ',
	'message_notif_sauver_intro' => 'Une nouvelle sauvegarde de vos fichiers est disponible. Elle a été réalisée par @auteur@.',
	'message_rien_a_sauver' => 'Aucun fichier ni dossier à sauvegarder.',
	'message_rien_a_telecharger' => 'Aucune sauvegarde disponible au téléchargement.',
	'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde. Le fichier d’archives n’a pas été créé.',
	'message_sauvegarde_ok' => 'Le fichier d’archives a bien été créé.',
	'message_sauver_sujet' => 'Sauvegarde',
	'message_telechargement_nok' => 'Erreur lors du téléchargement.',
	'message_zip_auteur_indetermine' => 'Non déterminé',
	'message_zip_propriete_nok' => 'Aucune propriété n’est disponible sur cette archive.',
	'message_zip_sans_contenu' => 'Aucune information n’est disponible sur le contenu de cette archive.',

	// R
	'resume_zip_auteur' => 'Créé par',
	'resume_zip_compteur' => 'Fichiers / dossiers archivés',
	'resume_zip_contenu' => 'Contenu résumé',
	'resume_zip_statut' => 'Statut',

	// T
	'titre_boite_sauver' => 'Créer une archive',
	'titre_boite_telecharger' => 'Liste des archives disponibles au téléchargement',
	'titre_page_configurer' => 'Configuration du plugin Mes fichiers',
	'titre_page_mes_fichiers' => 'Sauvegarder mes fichiers de personnalisation'
);

?>

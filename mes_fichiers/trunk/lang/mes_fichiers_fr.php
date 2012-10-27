<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// B
'bouton_mes_fichiers' => 'Sauvegarder mes fichiers',
'bouton_effacer' => 'Effacer',
'bouton_sauver' => 'Sauvegarder',
'bouton_telecharger' => 'Télécharger',
'bouton_tout_cocher' => 'Tout cocher',
'bouton_tout_decocher' => 'Tout décocher',
'bouton_voir' => 'Voir',
'bulle_bouton_voir' => 'Voir le contenu de l\'archive',

// C
'colonne_date' => 'Date',
'colonne_nom' => 'Nom',
'colonne_taille_octets' => 'Taille',
'colonne_actions' => '&nbsp;',

// E
'erreur_aucun_fichier_sauver' => 'Il n\'y a aucun fichier à sauvegarder',
'erreur_droits_insuffisants' => 'Vous ne disposez pas de droits suffisants pour réaliser une sauvegarde.',
'erreur_repertoire_trop_grand' => 'Ce répertoire dépasse la limite de @taille_max@ MB et ne peut &ecirc;tre sauvegardé.',
'explication_cfg_notif_mail' => 'Ces emails s\'ajoutent à celui du webmestre du site. Ils doivent &ecirc;tre séparés par des virgules ",".',

// I
'info_sauver' => 'Cette option fabrique un fichier d\'archives contenant les données de personnalisation du site comme le dernier dump de sauvegarde de la base, le(s) dossier(s) des squelettes nommés, le dossier images...
	Le fichier d\'archives est créé dans <i>tmp/mes_fichiers/</i> et se nomme <i>@prefixe@_aaaammjj_hhmmss.zip</i>.
	La liste exhaustive des fichiers et dossiers pouvant &ecirc;tre sauvegardés est affichée ci-dessous :',
'info_telecharger' => 'Cette option permet de télécharger un des fichiers d\'archives disponibles parmi la liste affichée ci-dessous :',

// J
'jours' => 'jours',

// L
'label_cfg_duree_sauvegarde' => 'Durée de conservation des sauvegardes',
'label_cfg_frequence' => 'Fréquence de sauvegarde',
'label_cfg_taille_max_rep' => 'Taille maximale des répertoires que l\'on peut sauvegarder (en MB)',
'label_cfg_notif_active' => 'Activer les notifications',
'label_cfg_notif_mail' => 'Adresses email à notifier',
'label_cfg_prefixe' => 'Préfixe utilisé pour le nom de fichier',
'label_cfg_sauvegarde_reguliere' => 'Faire une sauvegarde régulière',
'legende_cfg_notification' => 'Notifications',
'legende_cfg_sauvegarde_reguliere' => 'Sauvegarder régulièrement',


// M
'message_cleaner_sujet' => 'Suppression des sauvegardes de fichiers',
'message_notif_cleaner_intro' => 'L\'action de suppression automatique des sauvegardes de fichiers obsolètes (dont la date est postérieure à @frequence@ jours) a été effectuée avec succès. Les fichiers suivants ont été supprimés : ',
'message_rien_a_sauver' => 'Aucun fichier ni dossier à sauvegarder.',
'message_rien_a_telecharger' => 'Aucune sauvegarde disponible au téléchargement.',
'message_rien_a_restaurer' => 'Aucune sauvegarde disponible à la restauration.',
'message_sauvegarde_ok' => 'Le fichier d\'archives a bien été créé.',
'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde. Le fichier d\'archives n\'a pas été créé.',
'message_notif_sauver_intro' => 'Une nouvelle sauvegarde de vos fichiers est disponible pour votre site. Elle a été réalisée par @auteur@.',
'message_sauver_sujet' => 'Sauvegarde des fichiers',
'message_telechargement_ok' => 'Le fichier d\'archives a bien été téléchargé.',
'message_telechargement_nok' => 'Erreur lors du téléchargement.',
'message_zip_propriete_nok' => 'Aucune propriété n\'est disponible sur cette archive.',
'message_zip_sans_contenu' => 'Aucune information n\'est disponible sur le contenu de cette archive.',
'message_zip_auteur_indetermine' => 'Non déterminé',
'message_zip_auteur_cron' => 'Tache automatique',

// R
'resume_table_telecharger' => 'Liste des archives disponibles au téléchargement',
'resume_zip_auteur' => 'Créé par',
'resume_zip_statut' => 'Statut',
'resume_zip_compteur' => 'Fichiers / dossiers archivés',
'resume_zip_contenu' => 'Contenu résumé',


// T
'titre_page_navigateur' => 'Mes fichiers',
'titre_page' => 'Sauvegarder mes fichiers de personnalisation',
'titre_boite_sauver' => 'Sauvegarder mes fichiers',
'titre_boite_telecharger' => 'Télécharger une sauvegarde',

);


?>

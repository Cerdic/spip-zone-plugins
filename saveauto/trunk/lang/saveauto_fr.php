<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saveauto/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_sauvegarder' => 'Sauvegarder la base',

	// C
	'colonne_auteur' => 'Créé par',
	'colonne_nom' => 'Nom',

	// E
	'erreur_impossible_creer_verifier' => 'Impossible de créer le fichier @fichier@, vérifiez les droits d’écriture du répertoire @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible de lister les tables de la base.',
	'erreur_probleme_donnees_corruption' => 'Probleme avec les donnees de @table@, corruption possible !',
	'erreur_repertoire_inaccessible' => 'Le répertoire @rep@ est inaccessible en écriture.',

	// H
	'help_cfg_generale' => 'Ces paramètres de configuration s’appliquent à toutes les sauvegardes, manuelles ou automatiques.',
	'help_contenu' => 'Choisissez les paramètres de contenu de votre fichier de sauvegarde.',
	'help_contenu_auto' => 'Choisir le contenu des sauvegardes automatiques.',
	'help_frequence' => 'Saisir la fréquence des sauvegardes automatiques en jours.',
	'help_liste_tables' => 'Par défaut, toutes les tables sont exportées à l’exception des tables @noexport@. Si vous souhaitez choisir précisément les tables à sauvegarder ouvrez la liste en décochant la case ci-dessous.',
	'help_mail_max_size' => 'Saisir la taille maximale en Mo du fichier de sauvegarde au-delà de laquelle le mail ne sera pas envoyé (valeur à vérifier auprès de votre fournisseur de mail).',
	'help_max_zip' => 'Le fichier de sauvegarde est automatiquement zippé si sa taille est inférieure à un seuil. Saisir ce seuil en Mo. (Ce seuil est nécessaire pour ne pas planter le serveur par la confection d’un trop gros zip)',
	'help_notif_active' => 'Si vous souhaitez être prévenus des traitements automatiques activez les notifications. Pour la sauvegarde automatique vous recevrez le fichier généré par mail si celui-ci n’est pas trop volumineux et que le plugin Facteur est activé.',
	'help_notif_mail' => 'Saisir les adresses en les séparant par des virgules ",". Ces adresses s’ajoutent à celle du webmestre du site.',
	'help_obsolete' => 'Saisir la durée de conservation des sauvegardes en jours',
	'help_prefixe' => 'Saisir le préfixe accolé au nom de chaque fichier de sauvegarde',
	'help_restauration' => '<strong>Attention !!!</strong> ces sauvegardes ne sont <strong>pas au format de celles de SPIP</strong> et ne peuvent pas être utilisées avec l’outil de restauration de la base de SPIP.<br /><br />
							Pour toute restauration il faut donc utiliser l’interface <strong>phpmyadmin</strong> de votre
							serveur de base de données.<br /><br />
							Ces sauvegardes contiennent les commandes permettant d’<strong>effacer</strong> les tables de votre base SPIP et de les <strong>remplacer</strong> par les
							données archivées. Les données <strong>plus récentes</strong> que celles de la sauvegarde seront donc <strong>PERDUES</strong> !',
	'help_sauvegarde_1' => 'Cette option vous permet de sauvegarder la structure et le contenu de la base dans un fichier au format MySQL qui sera stocké dans le répertoire tmp/dump/. La fichier se nomme <em>@prefixe@_aaaammjj_hhmmss.</em>. Le préfixe des tables est conservé.',
	'help_sauvegarde_2' => 'La sauvegarde automatique est activée (fréquence en jours : @frequence@).',

	// I
	'info_sql_auteur' => 'Auteur : ',
	'info_sql_base' => 'Base : ',
	'info_sql_compatible_phpmyadmin' => 'Fichier SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Date : ',
	'info_sql_debut_fichier' => 'Debut du fichier',
	'info_sql_donnees_table' => 'Donnees de la table @table@',
	'info_sql_fichier_genere' => 'Ce fichier est genere par le plugin Saveauto',
	'info_sql_fin_fichier' => 'Fin du fichier',
	'info_sql_ipclient' => 'IP Client : ',
	'info_sql_mysqlversion' => 'Version MySQL : ',
	'info_sql_os' => 'OS Serveur : ',
	'info_sql_phpversion' => 'Version PHP : ',
	'info_sql_plugins_utilises' => '@nb@ plugins utilises :',
	'info_sql_serveur' => 'Serveur : ',
	'info_sql_spip_version' => 'Version de SPIP : ',
	'info_sql_structure_table' => 'Structure de la table @table@',

	// L
	'label_donnees' => 'Données des tables',
	'label_frequence' => 'Fréquence des sauvegardes',
	'label_mail_max_size' => 'Seuil d’envoi du mail',
	'label_max_zip' => 'Seuil des zips',
	'label_nettoyage_journalier' => 'Activer le nettoyage journalier des archives',
	'label_notif_active' => 'Activer les notifications',
	'label_notif_mail' => 'Adresses email à notifier',
	'label_obsolete_jours' => 'Conservation des sauvegardes',
	'label_prefixe_sauvegardes' => 'Préfixe',
	'label_sauvegarde_reguliere' => 'Activer la sauvegarde régulière',
	'label_structure' => 'Structure des tables',
	'label_toutes_tables' => 'Sauvegarder toutes les tables',
	'legend_cfg_generale' => 'Paramètres généraux des sauvegardes',
	'legend_cfg_notification' => 'Notifications',
	'legend_cfg_sauvegarde_reguliere' => 'Traitements automatiques',

	// M
	'message_aucune_sauvegarde' => 'Aucune sauvegarde disponible au téléchargement.',
	'message_cleaner_sujet' => 'Nettoyage des sauvegardes',
	'message_notif_cleaner_intro' => 'La suppression automatique des sauvegardes obsolètes (dont la date est antérieure à @duree@ jours) a été effectuée avec succès. Les fichiers suivants ont été supprimés : ',
	'message_notif_sauver_intro' => 'La sauvegarde de la base @base@ a été effectuée avec succès par l’auteur @auteur@.',
	'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde de la base.',
	'message_sauvegarde_ok' => 'La sauvegarde de la base a été faite avec succès.',
	'message_sauver_sujet' => 'Sauvegarde de la base @base@',
	'message_telechargement_nok' => 'Erreur lors du téléchargement.',

	// T
	'titre_boite_historique' => 'Sauvegardes MySQL disponibles au téléchargement',
	'titre_boite_sauver' => 'Créer une sauvegarde MySQL',
	'titre_page_configurer' => 'Configuration du plugin Sauvegarde automatique',
	'titre_page_saveauto' => 'Sauvegarder la base au format MySQL',
	'titre_saveauto' => 'Sauvegarde automatique'
);

?>

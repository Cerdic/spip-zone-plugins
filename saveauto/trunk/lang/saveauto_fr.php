<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saveauto/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Attention :',

	// B
	'bouton_sauvegarder' => 'Sauvegarder la base',

	// C
	'colonne_auteur' => 'Créé par',
	'colonne_nom' => 'Nom',

	// E
	'envoi_mail' => 'Envoi par mail',
	'erreur_config_inadaptee_mail' => 'Configuration non-adaptée, votre serveur n\'assure pas les fonctions d\'envoi de mail !',
	'erreur_impossible_creer_verifier' => 'Impossible de créer le fichier @fichier@, vérifiez les droits d\'écriture du répertoire @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible de lister les tables de la base.',
	'erreur_mail_fichier_lourd' => 'Le fichier de sauvegarde est trop lourd pour être envoyé par mail. Vous pouvez le récupérer depuis votre interface d\'administration ou par FTP en suivant le chemin : @fichier@',
	'erreur_mail_sujet' => 'Erreur de sauvegarde SQL',
	'erreur_probleme_donnees_corruption' => 'Probleme avec les donnees de @table@, corruption possible !',
	'erreur_repertoire_inaccessible' => 'Le répertoire @rep@ est inaccessible en écriture.',
	'erreur_repertoire_inexistant' => 'Le répertoire @rep@ est inexistant. Veuillez vérifier votre configuration.',
	'erreur_sauvegarde_intro' => 'Le message d\'erreur est le suivant :',
	'erreurs_config' => 'Erreur(s) dans la configuration',

	// H
	'help_accepter' => 'Optionnel : ne sauver que les tables ayant la chaîne spécifiée dans leur nom, ex : annuaire_, important, machin.
	 								 		Ne mettez rien pour accepter toutes les tables. Séparez les différents noms par le symbole point-virgule (;)',
	'help_cfg_generale' => 'Ces paramètres de configuration s\'appliquent à toutes les sauvegardes, manuelles ou automatiques.',
	'help_contenu' => 'Choisissez les paramètres de contenu de votre fichier de sauvegarde.',
	'help_contenu_auto' => 'Choisir le contenu des sauvegardes automatiques.',
	'help_envoi' => 'Saisir une adresse mail pour activer l\'envoi de la sauvegarde',
	'help_eviter' => 'Optionnel : si la table contient dans son nom la chaîne spécifiée : les données sont ignorées (pas la structure). Séparez les différents noms par le symbole point-virgule (;)',
	'help_frequence' => 'Saisir la fréquence des sauvegardes automatiques en jours.',
	'help_gz' => 'Sinon les sauvegardes seront au format .sql',
	'help_liste_tables' => 'Par défaut, toutes les tables sont exportées à l\'exception des tables @noexport@. Si vous souhaitez choisir précisément les tables à sauvegarder ouvrez la liste en décochant la case ci-dessous.',
	'help_mail_max_size' => 'Saisir la taille maximale en Mo du fichier de sauvegarde au-delà de laquelle le mail ne sera pas envoyé (valeur à vérifier auprès de votre fournisseur de mail).',
	'help_max_zip' => 'Le fichier de sauvegarde est automatiquement zippé si sa taille est inférieure à un seuil. Saisir ce seuil en Mo.',
	'help_msg' => 'Afficher un message dans l\'interface privée en cas de succès de la sauvegarde',
	'help_notif_mail' => 'Saisir les adresses en les séparant par des virgules ",". Ces adresses s\'ajoutent à celle du webmestre du site.',
	'help_obsolete' => 'Saisir la durée de conservation des sauvegardes en jours',
	'help_prefixe' => 'Saisir le préfixe accolé au nom de chaque fichier de sauvegarde',
	'help_rep' => 'Répertoire où stocker les fichiers (chemin à partir de la <strong>racine</strong> du SPIP, tmp/data/ par ex). <strong>DOIT</strong> se terminer par un /.',
	'help_restauration' => '<strong>Attention !!!</strong> ces sauvegardes ne sont <strong>pas au format de celles de SPIP</strong> et ne peuvent pas être utiliser avec l\'outil de restauration de la base de SPIP.<br /><br />
							Pour toute restauration il faut donc utiliser l\'interface <strong>phpmyadmin</strong> de votre
							serveur de base de données.<br /><br />
							Ces sauvegardes contiennent les commandes permettant d\'<strong>effacer</strong> les tables de votre base SPIP et de les <strong>remplacer</strong> par les
							données archivées. Les données <strong>plus récentes</strong> que celles de la sauvegarde seront donc <strong>PERDUES</strong>!',
	'help_sauvegarde_1' => 'Cette option vous permet de sauvegarder la structure et le contenu de la base dans un fichier au format SQL qui sera stocké dans le répertoire tmp/dump/. La fichier se nomme <em>@prefixe@_aaaammjj_hhmmss.</em>',
	'help_sauvegarde_2' => 'La sauvegarde automatique est activée (fréquence en jours : @frequence@).',
	'help_titre' => 'Cette page vous permet de configurer les options de sauvegarde automatique de la base.',

	// I
	'info_mail_message_mime' => 'Ceci est un message au format MIME.',
	'info_sauvegardes_obsolete' => 'Une sauvegarde de la base est conservée @nb@ jours à partir du jour de sa réalisation.',
	'info_sql_auteur' => 'Auteur : ',
	'info_sql_base' => 'Base : ',
	'info_sql_compatible_phpmyadmin' => 'Fichier SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Date : ',
	'info_sql_debut_fichier' => 'Debut du fichier',
	'info_sql_donnees_table' => 'Donnees de @table@',
	'info_sql_fichier_genere' => 'Ce fichier est genere par le plugin saveauto',
	'info_sql_fin_fichier' => 'Fin du fichier',
	'info_sql_ipclient' => 'IP Client : ',
	'info_sql_mysqlversion' => 'Version mySQL : ',
	'info_sql_os' => 'OS Serveur : ',
	'info_sql_phpversion' => 'Version PHP : ',
	'info_sql_plugins_utilises' => '@nb@ plugins utilises :',
	'info_sql_serveur' => 'Serveur : ',
	'info_sql_spip_version' => 'Version de SPIP : ',
	'info_sql_structure_table' => 'Structure de la table @table@',
	'info_telecharger_sauvegardes' => 'Le tableau ci-dessous liste l\'ensemble des sauvegardes réalisées pour votre site que vous pouvez télécharger.',

	// L
	'label_adresse' => 'À l\'adresse : ',
	'label_compression_gz' => 'Zipper le fichier de sauvegarde : ',
	'label_donnees' => 'Données des tables',
	'label_donnees_ignorees' => 'Données ignorées : ',
	'label_frequence' => 'Fréquence des sauvegardes',
	'label_mail_max_size' => 'Seuil d\'envoi du mail',
	'label_max_zip' => 'Seuil des zips',
	'label_message_succes' => 'Alerte',
	'label_nettoyage_journalier' => 'Activer le nettoyage journalier des archives',
	'label_nom_base' => 'Nom de la base SPIP : ',
	'label_notif_active' => 'Activer les notifications',
	'label_notif_mail' => 'Adresses email à notifier',
	'label_obsolete_jours' => 'Conservation des sauvegardes',
	'label_prefixe_sauvegardes' => 'Préfixe',
	'label_repertoire_stockage' => 'Répertoire de stockage : ',
	'label_restauration' => 'Restauration d\'une sauvegarde :',
	'label_sauvegarde_reguliere' => 'Activer la sauvegarde régulière',
	'label_structure' => 'Structure des tables',
	'label_tables_acceptes' => 'Tables acceptées : ',
	'label_toutes_tables' => 'Sauvegarder toutes les tables',
	'legend_cfg_generale' => 'Paramètres généraux des sauvegardes',
	'legend_cfg_notification' => 'Notifications',
	'legend_cfg_sauvegarde_reguliere' => 'Traitements automatiques',
	'legend_structure_donnees' => 'Eléments à sauvegarder : ',

	// M
	'message_aucune_sauvegarde' => 'Aucune sauvegarde disponible au téléchargement.',
	'message_cleaner_sujet' => 'Nettoyage des sauvegardes',
	'message_notif_cleaner_intro' => 'La suppression automatique des sauvegardes obsolètes (dont la date est antérieure à @duree@ jours) a été effectuée avec succès. Les fichiers suivants ont été supprimés : ',
	'message_pas_envoi' => 'Les sauvegardes ne seront pas envoyées !',
	'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde SQL de la base.',
	'message_sauvegarde_ok' => 'La sauvegarde SQL de la base a été faite avec succès.',
	'message_telechargement_nok' => 'Erreur lors du téléchargement.',

	// S
	'saveauto_titre' => 'Sauvegarde SQL',

	// T
	'titre_boite_historique' => 'Sauvegardes SQL disponibles au téléchargement',
	'titre_boite_sauver' => 'Créer une sauvegarde SQL',
	'titre_page_configurer' => 'Configuration du plugin Sauvegarde automatique',
	'titre_page_saveauto' => 'Sauvegarder la base au format SQL',
	'titre_saveauto' => 'Sauvegarde automatique',

	// V
	'valeur_jours' => ' jours'
);

?>

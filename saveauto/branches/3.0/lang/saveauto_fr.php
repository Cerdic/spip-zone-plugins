<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saveauto/2.1/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Attention :',

	// C
	'colonne_date' => 'Date',
	'colonne_nom' => 'Nom',
	'colonne_taille_octets' => 'Taille',

	// E
	'envoi_mail' => 'Sauvegardes envoyées ',
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
	'help_envoi' => 'Optionnel : envoi de la sauvegarde par mail si vous mettez une adresse de destinataire',
	'help_eviter' => 'Optionnel : si la table contient dans son nom la chaîne spécifiée : les données sont ignorées (pas la structure). Séparez les différents noms par le symbole point-virgule (;)',
	'help_gz' => 'Sinon les sauvegardes seront au format .sql',
	'help_mail_max_size' => 'Certaines bases de données peuvent dépasser la taille maximale des fichiers joints pour un mail. Vérifiez auprès de votre fournisseur de mail pour connaître la taille maximale qu\'il autorise. La limite par défaut est de 2Mo.',
	'help_msg' => 'Affiche un message de succès dans l\'interface',
	'help_obsolete' => 'Détermine à partir de combien de jours une archive est considérée comme obsolète et automatiquement supprimée du serveur.
	 								 		Mettez -1 pour désactiver cette fonctionnalité',
	'help_prefixe' => 'Optionnel : mettre un préfixe au nom du fichier de sauvegarde',
	'help_rep' => 'Répertoire où stocker les fichiers (chemin à partir de la <strong>racine</strong> du SPIP, tmp/data/ par ex). <strong>DOIT</strong> se terminer par un /.',
	'help_restauration' => '<strong>Attention !!!</strong> les sauvegardes réalisées ne sont <strong>pas au format de celles de SPIP</strong> :
   										 		Inutile d\'essayer de les utiliser avec l\'outil d\'administration de Spip.<br /><br />
													Pour toute restauration il faut utiliser l\'interface <strong>phpmyadmin</strong> de votre
													serveur de base de données : dans l\'onglet <strong>"SQL"</strong> utiliser le bouton
													<strong>"Emplacement du fichier texte"</strong> pour sélectionner le fichier de sauvegarde
													(cocher l\'option "gzippé" si nécessaire) puis valider.<br /><br />
													Les sauvegardes <strong>xxxx.gz</strong> ou <strong>xxx.sql</strong> contiennent un fichier au format SQL avec les commandes
													permettant d\'<strong>effacer</strong> les tables existantes du SPIP et de les <strong>remplacer</strong> par les
													données archivées. Les données <strong>plus récentes</strong> que celles de la sauvegarde seront donc <strong>PERDUES</strong>!',
	'help_titre' => 'Cette page vous permet de configurer les options de sauvegarde automatique de la base.',

	// I
	'info_mail_message_mime' => 'Ceci est un message au format MIME.',
	'info_sauvegardes_obsolete' => 'Une sauvegarde de la base est conservée @nb@ jours à partir du jour de sa réalisation.',
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
	'label_donnees' => 'Données des tables : ',
	'label_donnees_ignorees' => 'Données ignorées : ',
	'label_frequence' => 'Fréquence de la sauvegarde : tous les ',
	'label_mail_max_size' => 'Taille maximale des fichiers à attacher aux mails (en Mo) :',
	'label_message_succes' => 'Affiche un message de succès si sauvegarde OK : ',
	'label_nom_base' => 'Nom de la base SPIP : ',
	'label_obsolete_jours' => 'Sauvegardes considérées obsolètes après : ',
	'label_prefixe_sauvegardes' => 'Préfixe pour les sauvegardes : ',
	'label_repertoire_stockage' => 'Répertoire de stockage : ',
	'label_restauration' => 'Restauration d\'une sauvegarde :',
	'label_structure' => 'Structure des tables : ',
	'label_tables_acceptes' => 'Tables acceptées : ',
	'legend_structure_donnees' => 'Eléments à sauvegarder : ',

	// M
	'message_aucune_sauvegarde' => 'Il n\'y a aucune sauvegarde.',
	'message_pas_envoi' => 'Les sauvegardes ne seront pas envoyées !',

	// S
	'sauvegarde_erreur_mail' => 'Le plugin "saveauto" a rencontré une erreur lors de la sauvegarde de la base de donnée.',
	'sauvegarde_ok_mail' => 'Sauvegarde de la base et envoi par mail effectués avec succès !',
	'saveauto_titre' => 'Sauvegarde SQL',

	// T
	'titre_boite_historique' => 'Historique des sauvegardes',
	'titre_boite_sauver' => 'Plugin Saveauto: sauvegarde SQL de la base de donnée',
	'titre_page_saveauto' => 'Sauvegarde de base de donnée',
	'titre_saveauto' => 'Sauvegarde automatique',

	// V
	'valeur_jours' => ' jours'
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saveauto/2.1/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Attention :',

	// C
	'colonne_date' => 'Date',
	'colonne_nom' => 'Nom',
	'colonne_taille_octets' => 'Taille',

	// E
	'envoi_mail' => 'Sauvegardes envoy&eacute;es ',
	'erreur_config_inadaptee_mail' => 'Configuration non-adapt&eacute;e, votre serveur n\'assure pas les fonctions d\'envoi de mail !',
	'erreur_impossible_creer_verifier' => 'Impossible de cr&eacute;er le fichier @fichier@, v&eacute;rifiez les droits d\'&eacute;criture du r&eacute;pertoire @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible de lister les tables de la base.',
	'erreur_mail_fichier_lourd' => 'Le fichier de sauvegarde est trop lourd pour &ecirc;tre envoy&eacute; par mail. Vous pouvez le r&eacute;cup&eacute;rer depuis votre interface d\'administration ou par FTP en suivant le chemin : @fichier@',
	'erreur_mail_sujet' => 'Erreur de sauvegarde SQL',
	'erreur_probleme_donnees_corruption' => 'Probleme avec les donnees de @table@, corruption possible !',
	'erreur_repertoire_inaccessible' => 'Le r&eacute;pertoire @rep@ est inaccessible en &eacute;criture.',
	'erreur_repertoire_inexistant' => 'Le r&eacute;pertoire @rep@ est inexistant. Veuillez v&eacute;rifier votre configuration.',
	'erreur_sauvegarde_intro' => 'Le message d\'erreur est le suivant :',
	'erreurs_config' => 'Erreur(s) dans la configuration',

	// H
	'help_accepter' => 'Optionnel : ne sauver que les tables ayant la cha&icirc;ne sp&eacute;cifi&eacute;e dans leur nom, ex : annuaire_, important, machin.
	 								 		Ne mettez rien pour accepter toutes les tables. S&eacute;parez les diff&eacute;rents noms par le symbole point-virgule (;)',
	'help_envoi' => 'Optionnel : envoi de la sauvegarde par mail si vous mettez une adresse de destinataire',
	'help_eviter' => 'Optionnel : si la table contient dans son nom la cha&icirc;ne sp&eacute;cifi&eacute;e : les donn&eacute;es sont ignor&eacute;es (pas la structure). S&eacute;parez les diff&eacute;rents noms par le symbole point-virgule (;)',
	'help_gz' => 'Sinon les sauvegardes seront au format .sql',
	'help_mail_max_size' => 'Certaines bases de donn&eacute;es peuvent d&eacute;passer la taille maximale des fichiers joints pour un mail. V&eacute;rifiez aupr&egrave;s de votre fournisseur de mail pour conna&icirc;tre la taille maximale qu\'il autorise. La limite par d&eacute;faut est de 2Mo.',
	'help_msg' => 'Affiche un message de succ&egrave;s dans l\'interface',
	'help_obsolete' => 'D&eacute;termine &agrave; partir de combien de jours une archive est consid&eacute;r&eacute;e comme obsol&egrave;te et automatiquement supprim&eacute;e du serveur.
	 								 		Mettez -1 pour d&eacute;sactiver cette fonctionnalit&eacute;',
	'help_prefixe' => 'Optionnel : mettre un pr&eacute;fixe au nom du fichier de sauvegarde',
	'help_rep' => 'R&eacute;pertoire o&ugrave; stocker les fichiers (chemin &agrave; partir de la <strong>racine</strong> du SPIP, tmp/data/ par ex). <strong>DOIT</strong> se terminer par un /.',
	'help_restauration' => '<strong>Attention !!!</strong> les sauvegardes r&eacute;alis&eacute;es ne sont <strong>pas au format de celles de SPIP</strong> :
   										 		Inutile d\'essayer de les utiliser avec l\'outil d\'administration de Spip.<br /><br />
													Pour toute restauration il faut utiliser l\'interface <strong>phpmyadmin</strong> de votre
													serveur de base de donn&eacute;es : dans l\'onglet <strong>"SQL"</strong> utiliser le bouton
													<strong>"Emplacement du fichier texte"</strong> pour s&eacute;lectionner le fichier de sauvegarde
													(cocher l\'option "gzipp&eacute;" si n&eacute;cessaire) puis valider.<br /><br />
													Les sauvegardes <strong>xxxx.gz</strong> ou <strong>xxx.sql</strong> contiennent un fichier au format SQL avec les commandes
													permettant d\'<strong>effacer</strong> les tables existantes du SPIP et de les <strong>remplacer</strong> par les
													donn&eacute;es archiv&eacute;es. Les donn&eacute;es <strong>plus r&eacute;centes</strong> que celles de la sauvegarde seront donc <strong>PERDUES</strong>!',
	'help_titre' => 'Cette page vous permet de configurer les options de sauvegarde automatique de la base.',

	// I
	'info_mail_message_mime' => 'Ceci est un message au format MIME.',
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

	// L
	'label_adresse' => '&Agrave; l\'adresse : ',
	'label_compression_gz' => 'Zipper le fichier de sauvegarde : ',
	'label_donnees' => 'Donn&eacute;es des tables : ',
	'label_donnees_ignorees' => 'Donn&eacute;es ignor&eacute;es : ',
	'label_frequence' => 'Fr&eacute;quence de la sauvegarde : tous les ',
	'label_mail_max_size' => 'Taille maximale des fichiers &agrave; attacher aux mails (en Mo) :',
	'label_message_succes' => 'Affiche un message de succ&egrave;s si sauvegarde OK : ',
	'label_nom_base' => 'Nom de la base SPIP : ',
	'label_obsolete_jours' => 'Sauvegardes consid&eacute;r&eacute;es obsol&egrave;tes apr&egrave;s : ',
	'label_prefixe_sauvegardes' => 'Pr&eacute;fixe pour les sauvegardes : ',
	'label_repertoire_stockage' => 'R&eacute;pertoire de stockage : ',
	'label_restauration' => 'Restauration d\'une sauvegarde :',
	'label_structure' => 'Structure des tables : ',
	'label_tables_acceptes' => 'Tables accept&eacute;es : ',
	'legend_structure_donnees' => 'El&eacute;ments &agrave; sauvegarder : ',

	// M
	'message_aucune_sauvegarde' => 'Il n\'y a aucune sauvegarde.',
	'message_pas_envoi' => 'Les sauvegardes ne seront pas envoy&eacute;es !',

	// S
	'sauvegarde_erreur_mail' => 'Le plugin "saveauto" a rencontr&eacute; une erreur lors de la sauvegarde de la base de donn&eacute;e.',
	'sauvegarde_ok_mail' => 'Sauvegarde de la base et envoi par mail effectu&eacute;s avec succ&egrave;s !',
	'saveauto_titre' => 'Sauvegarde SQL',

	// T
	'titre_boite_historique' => 'Historique des sauvegardes',
	'titre_boite_sauver' => 'Plugin Saveauto: sauvegarde SQL de la base de donn&eacute;e',
	'titre_page_saveauto' => 'Sauvegarde de base de donn&eacute;e',
	'titre_saveauto' => 'Sauvegarde automatique',

	// V
	'valeur_jours' => ' jours'
);

?>

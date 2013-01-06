<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saveauto?lang_cible=ca
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_sauvegarder' => 'Sauvegarder la base', # NEW

	// C
	'colonne_auteur' => 'Créé par', # NEW
	'colonne_nom' => 'Cognom',

	// E
	'erreur_impossible_creer_verifier' => 'Impossible crear el fitxer @fichier@, verifiqueu els drets d\'escriptura del directori @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible llistar les taules de la base de dades.',
	'erreur_probleme_donnees_corruption' => 'Problema amb les dades de @table@, és possible que estiguin corruptes!',
	'erreur_repertoire_inaccessible' => 'El directori @rep@ és inaccessible a l\'escriptura.',

	// H
	'help_cfg_generale' => 'Ces paramètres de configuration s\'appliquent à toutes les sauvegardes, manuelles ou automatiques.', # NEW
	'help_contenu' => 'Choisissez les paramètres de contenu de votre fichier de sauvegarde.', # NEW
	'help_contenu_auto' => 'Choisir le contenu des sauvegardes automatiques.', # NEW
	'help_frequence' => 'Saisir la fréquence des sauvegardes automatiques en jours.', # NEW
	'help_liste_tables' => 'Par défaut, toutes les tables sont exportées à l\'exception des tables @noexport@. Si vous souhaitez choisir précisément les tables à sauvegarder ouvrez la liste en décochant la case ci-dessous.', # NEW
	'help_mail_max_size' => 'Algunes bases de dades poden sobrepassar la mida màxima permesa en els fitxers adjunts per un correu electrònic. Consulteu al vostre proveïdor de correu electrònic per tal de saber la mida màxima autoritzada. El límit predeterminat és de 2MB.', # MODIF
	'help_max_zip' => 'Le fichier de sauvegarde est automatiquement zippé si sa taille est inférieure à un seuil. Saisir ce seuil en Mo.', # NEW
	'help_notif_active' => 'Activer l\'envoi des sauvegardes par mail', # NEW
	'help_notif_mail' => 'Saisir les adresses en les séparant par des virgules ",". Ces adresses s\'ajoutent à celle du webmestre du site.', # NEW
	'help_obsolete' => 'Determina a partir de quants dies un arxiu es considera obsolet i automàticament l\'elimina del servidor.
             Poseu-hi -1 per desactivar aquesta funció', # MODIF
	'help_prefixe' => 'Opcional: posar un prefix al nom del fitxer de la còpia de seguretat', # MODIF
	'help_restauration' => '<strong>Advertència!!!</strong> Les còpies de seguretat realitzades no estan <strong>en el format de les d\'SPIP</strong>:
                Inútil provar utilitzar-les amb l\'eina d\'administració d\'Spip.<br /><br />
             Per qualsevol restauració cal utilitzar la interfície <strong>phpmyadmin</strong> del vostre
             servidor de base de dades: a la pestanya <strong>"SQL"</strong> utilitzeu el botó
             <strong>"Ubicació del fitxer de text"</strong> per seleccionar el fitxer de la còpia de seguretat
             (marcar la opció "gzip" si és necessari) i valideu tot seguit.<br /><br />
             Les còpies de seguretat <strong>xxxx.gz</strong> o <strong>xxx.sql</strong> contenen un fitxer en format SQL amb les comandes
             que permeten <strong>esborrar</strong> les taules existents d\'SPIP i <strong>substituir-les</strong> per les
             dades arxivades. Per tant, les dades <strong>més recents</strong> que les de la còpia de seguretat es <strong>PERDRAN</strong>!', # MODIF
	'help_sauvegarde_1' => 'Cette option vous permet de sauvegarder la structure et le contenu de la base dans un fichier au format SQL qui sera stocké dans le répertoire tmp/dump/. La fichier se nomme <em>@prefixe@_aaaammjj_hhmmss.</em>', # NEW
	'help_sauvegarde_2' => 'La sauvegarde automatique est activée (fréquence en jours : @frequence@).', # NEW

	// I
	'info_sql_auteur' => 'Auteur : ', # NEW
	'info_sql_base' => 'Base de dades: ',
	'info_sql_compatible_phpmyadmin' => 'Fitxer SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Data: ',
	'info_sql_debut_fichier' => 'Inici de l\'arxiu',
	'info_sql_donnees_table' => 'Dades de @table@', # MODIF
	'info_sql_fichier_genere' => 'Aquest fitxer està generat pel connector "saveauto"', # MODIF
	'info_sql_fin_fichier' => 'Final de l\'arxiu',
	'info_sql_ipclient' => 'IP Client: ',
	'info_sql_mysqlversion' => 'Versió mySQL : ',
	'info_sql_os' => 'Sistema Operatiu del Servidor: ',
	'info_sql_phpversion' => 'Versió PHP: ',
	'info_sql_plugins_utilises' => '@nb@ connectors utilitzats:',
	'info_sql_serveur' => 'Servidor: ',
	'info_sql_spip_version' => 'Versió d\'SPIP: ',
	'info_sql_structure_table' => 'Estructura de la taula @table@',

	// L
	'label_donnees' => 'Dades de les taules: ', # MODIF
	'label_frequence' => 'Freqüència de la còpia de seguretat: tots els  ', # MODIF
	'label_mail_max_size' => 'La mida màxima dels fitxers adjunts als correus electrònics (en MB):', # MODIF
	'label_max_zip' => 'Seuil des zips', # NEW
	'label_nettoyage_journalier' => 'Activer le nettoyage journalier des archives', # NEW
	'label_notif_active' => 'Activer les notifications', # NEW
	'label_notif_mail' => 'Adresses email à notifier', # NEW
	'label_obsolete_jours' => 'Còpies de seguretat considerades obsoletes després: ', # MODIF
	'label_prefixe_sauvegardes' => 'Prefix per les còpies de seguretat: ', # MODIF
	'label_sauvegarde_reguliere' => 'Activer la sauvegarde régulière', # NEW
	'label_structure' => 'Estructura de les taules: ', # MODIF
	'label_toutes_tables' => 'Sauvegarder toutes les tables', # NEW
	'legend_cfg_generale' => 'Paramètres généraux des sauvegardes', # NEW
	'legend_cfg_notification' => 'Notifications', # NEW
	'legend_cfg_sauvegarde_reguliere' => 'Traitements automatiques', # NEW

	// M
	'message_aucune_sauvegarde' => 'No hi ha cap còpia de seguretat.', # MODIF
	'message_cleaner_sujet' => 'Nettoyage des sauvegardes', # NEW
	'message_notif_cleaner_intro' => 'La suppression automatique des sauvegardes obsolètes (dont la date est antérieure à @duree@ jours) a été effectuée avec succès. Les fichiers suivants ont été supprimés : ', # NEW
	'message_notif_sauver_intro' => 'La sauvegarde de la base @base@ a été effectuée avec succès par l\'auteur @auteur@.', # NEW
	'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde SQL de la base.', # NEW
	'message_sauvegarde_ok' => 'La sauvegarde SQL de la base a été faite avec succès.', # NEW
	'message_sauver_sujet' => 'Sauvegarde de la base @base@', # NEW
	'message_telechargement_nok' => 'Erreur lors du téléchargement.', # NEW

	// T
	'titre_boite_historique' => 'Històric de les còpies de seguretat', # MODIF
	'titre_boite_sauver' => 'Connector Saveauto: còpia de seguretat SQL de la base de dades', # MODIF
	'titre_page_configurer' => 'Configuration du plugin Sauvegarde automatique', # NEW
	'titre_page_saveauto' => 'Còpia de seguretat de la base de dades', # MODIF
	'titre_saveauto' => 'Còpia de seguretat automàtica'
);

?>

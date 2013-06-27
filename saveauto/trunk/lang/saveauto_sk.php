<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saveauto?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_sauvegarder' => 'Zálohovať databázu',

	// C
	'colonne_auteur' => 'Vytvoril(i)',
	'colonne_nom' => 'Názov',

	// E
	'erreur_impossible_creer_verifier' => 'Nedá sa vytvoriť súbor @fichier@: skontrolujte právo zapisovať v priečinku @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Tabuľky z databázy sa nedajú vypísať.',
	'erreur_probleme_donnees_corruption' => 'Problém s údajmi v tabuľke @table@ table, môže byť poškodená!',
	'erreur_repertoire_inaccessible' => 'Do priečinka @rep@ sa nedá zapisovať.',

	// H
	'help_cfg_generale' => 'Ces paramètres de configuration s\'appliquent à toutes les sauvegardes, manuelles ou automatiques.', # NEW
	'help_contenu' => 'Choisissez les paramètres de contenu de votre fichier de sauvegarde.', # NEW
	'help_contenu_auto' => 'Choisir le contenu des sauvegardes automatiques.', # NEW
	'help_frequence' => 'Saisir la fréquence des sauvegardes automatiques en jours.', # NEW
	'help_liste_tables' => 'Par défaut, toutes les tables sont exportées à l\'exception des tables @noexport@. Si vous souhaitez choisir précisément les tables à sauvegarder ouvrez la liste en décochant la case ci-dessous.', # NEW
	'help_mail_max_size' => 'Niektoré databázy môžu prevyšovať maximálnu veľkosť určenú pre dokumenty pripojené k e-mailom. Zistite u poskytovateľa hostingu, aká maximálna veľkosť je povolená. Predvolený limit je 2 MB.', # MODIF
	'help_max_zip' => 'Le fichier de sauvegarde est automatiquement zippé si sa taille est inférieure à un seuil. Saisir ce seuil en Mo.', # NEW
	'help_notif_active' => 'Activer l\'envoi des sauvegardes par mail', # NEW
	'help_notif_mail' => 'Saisir les adresses en les séparant par des virgules ",". Ces adresses s\'ajoutent à celle du webmestre du site.', # NEW
	'help_obsolete' => 'Podľa počtu dní od vytvorenia určí, či sa archív považuje za neaktuálny a ak je neaktuálny, odstráni ho zo servera.
             Na deaktivovanie tejto funkcie zadajte -1', # MODIF
	'help_prefixe' => 'Nepovinné: zadajte predponu pre názvy súborov zálohy', # MODIF
	'help_restauration' => '<strong>Pozor!!!</strong> vytvorené zálohy <strong>nie sú vo formáte SPIPu:</strong>
                Je zbytočné snažiť sa ich použiť pomocou nástroja na riadenie SPIPu.<br /><br />
             Na každú obnovu databázy musíte použiť rozhranie programu <strong>phpmyadmin</strong> svojho
             databázového servera: na karte <strong>"SQL"</strong> použite tlačidlo s názvom
             <strong>"Umiestnenie textového súboru"</strong> na výber súboru zálohy
             (ak treba, zaškrtnite možnosť "gzipped" (= vo formáte gzip)) potom kliknite na OK.<br /><br />
             Zálohy <strong>xxxx.gz</strong> alebo <strong>xxx.sql</strong> obsahujú súbor vo formáte SQL s príkami,
             ktoré sa používajú na <strong>odstránenie</strong> existujúcich tabuliek SPIPu a na ich <strong>nahradenie</strong>
             údajmi v archívoch. Všetky  <strong>novšie</strong> údaje ako tie v zálohe sa preto <strong>STRATIA!</strong>', # MODIF
	'help_sauvegarde_1' => 'Cette option vous permet de sauvegarder la structure et le contenu de la base dans un fichier au format SQL qui sera stocké dans le répertoire tmp/dump/. La fichier se nomme <em>@prefixe@_aaaammjj_hhmmss.</em>', # NEW
	'help_sauvegarde_2' => 'La sauvegarde automatique est activée (fréquence en jours : @frequence@).', # NEW

	// I
	'info_sql_auteur' => 'Autor: ',
	'info_sql_base' => 'Databáza:',
	'info_sql_compatible_phpmyadmin' => 'Súbor SQL 100%-ne kompatibilný s programom PHPMyadmin',
	'info_sql_date' => 'Dátum:',
	'info_sql_debut_fichier' => 'Začiatok súboru',
	'info_sql_donnees_table' => 'Údaje z tabuľky @table@',
	'info_sql_fichier_genere' => 'Tento súbor vytvára zásuvný modul Saveauto',
	'info_sql_fin_fichier' => 'Koniec súboru',
	'info_sql_ipclient' => 'IP klienta:',
	'info_sql_mysqlversion' => 'Verzia MySQL:',
	'info_sql_os' => 'Operačný systém servera:',
	'info_sql_phpversion' => 'Verzia PHP:',
	'info_sql_plugins_utilises' => 'používa sa @nb@ zásuvných modulov:',
	'info_sql_serveur' => 'Server:',
	'info_sql_spip_version' => 'Verzia SPIPu:',
	'info_sql_structure_table' => 'Štruktúra tabuľky @table@',

	// L
	'label_donnees' => 'Údaje z tabuliek',
	'label_frequence' => 'Frekvencia zálohovania',
	'label_mail_max_size' => 'Maximálna veľkosť súborov pripájaných k e-mailom (v MB):', # MODIF
	'label_max_zip' => 'Seuil des zips', # NEW
	'label_nettoyage_journalier' => 'Activer le nettoyage journalier des archives', # NEW
	'label_notif_active' => 'Aktivovať oznamy',
	'label_notif_mail' => 'Adresses email à notifier', # NEW
	'label_obsolete_jours' => 'Zálohy sa pokladajú za neaktuálne po uplynutí:', # MODIF
	'label_prefixe_sauvegardes' => 'Predpona zálohy:', # MODIF
	'label_sauvegarde_reguliere' => 'Aktivovať pravidelné zálohovanie',
	'label_structure' => 'Štruktúra tabuliek:', # MODIF
	'label_toutes_tables' => 'Zálohovať všetky tabuľky',
	'legend_cfg_generale' => 'Paramètres généraux des sauvegardes', # NEW
	'legend_cfg_notification' => 'Oznamy',
	'legend_cfg_sauvegarde_reguliere' => 'Traitements automatiques', # NEW

	// M
	'message_aucune_sauvegarde' => 'Žiadne zálohy neexistujú.', # MODIF
	'message_cleaner_sujet' => 'Nettoyage des sauvegardes', # NEW
	'message_notif_cleaner_intro' => 'La suppression automatique des sauvegardes obsolètes (dont la date est antérieure à @duree@ jours) a été effectuée avec succès. Les fichiers suivants ont été supprimés : ', # NEW
	'message_notif_sauver_intro' => 'La sauvegarde de la base @base@ a été effectuée avec succès par l\'auteur @auteur@.', # NEW
	'message_sauvegarde_nok' => 'Erreur lors de la sauvegarde SQL de la base.', # NEW
	'message_sauvegarde_ok' => 'La sauvegarde SQL de la base a été faite avec succès.', # NEW
	'message_sauver_sujet' => 'Záloha databázy @base@',
	'message_telechargement_nok' => 'Chyba pri sťahovaní.',

	// T
	'titre_boite_historique' => 'História záloh', # MODIF
	'titre_boite_sauver' => 'Vytvoriť zálohu MySQL',
	'titre_page_configurer' => 'Nastavenia modulu Automatická záloha',
	'titre_page_saveauto' => 'Zálohovať databázu vo formáte MySQL',
	'titre_saveauto' => 'Automatická záloha'
);

?>

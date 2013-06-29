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
	'help_cfg_generale' => 'Tieto nastavenia sa použijú na všetky zálohy – manuálne aj automatické',
	'help_contenu' => 'Zvoľte si vlastnosti obsahu svojho súboru zálohy.',
	'help_contenu_auto' => 'Zvoľte si obsah automatických záloh.',
	'help_frequence' => 'Zadajte frekvenciu automatického zálohovania v dňoch',
	'help_liste_tables' => 'Podľa predvolených nastavení sa exportujú všetky tabuľky okrem tabuliek @noexport@. Ak chcete presne určiť, ktoré tabuľky sa majú zálohovať, odznačte toto políčko, čím otvoríte ich zoznam.',
	'help_mail_max_size' => 'Zadajte maximálnu veľkosť súboru databázy v MB, po ktorej prekročení nebude odoslaný e-mail (hodnotu je dobré si overiť u poskytovateľa e-mailu).',
	'help_max_zip' => 'Súbor zálohy bude automaticky "zazipovaný", ak jeho veľkosť bude menšia ako limit. Zadajte limit v megabytoch (Limit je potrebný na to, aby sa zabránilo zrúteniu servera preto, že bol vytvorený príliš veľký súbor zip)',
	'help_notif_active' => 'Ak chcete mať prehľad o každom automatickom spracovaní, aktivujte si zasielanie oznamov. Pri automatickom zálohovaní dostanete na e-mail vygenerovaný súbor, či nie je príliš veľký a oznam o tom, že zásuvný modul Poštár je aktivovaný.',
	'help_notif_mail' => 'Zadajte adresy oddelené čiarkami ",". Budú pridané k adrese webmastera stránky.',
	'help_obsolete' => 'Zadajte dĺžku trvania databáz v dňoch',
	'help_prefixe' => 'Zadajte predponu, ktorá bude pripojená k názvu každého súboru databázy',
	'help_restauration' => '<strong>Pozor!!!</strong> vytvorené zálohy <strong>nie sú vo formáte záloh SPIPu</strong> a nemôže sa na ne použiť nástroj SPIPu na obnovenie údajov.<br /><br />
            

                                    Na obnovenie databázy treba použiť rozhranie  <strong>phpmyadmin</strong> vášho
             databázového servera.
Tieto databázy obsahujú príkazy na <strong>vymazanie</strong> tabuliek vašej databázy v SPIPe a ich <strong>nahradenie</ strong> údajmi z archívu. <strong>Novšie údaje </strong> ako tie v databáze by sa preto mohli <strong>STRATIŤ!</ strong>',
	'help_sauvegarde_1' => 'Táto možnosť vám umožňuje zálohovať štruktúru obsahu databázy do súboru vo formáte SQL, ktorý bude uložený v priečinku tmp/dump/. Súbor sa bude volať <em>@prefixe@_aaaammjj_hhmmss.</em>. Predpona tabuliek bude zachovaná.',
	'help_sauvegarde_2' => 'Automatické zálohovanie je aktivované(frekvencia v dňoch: @frequence@).',

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
	'label_mail_max_size' => 'Limit na posielanie e-mailom',
	'label_max_zip' => 'Maximum pre zipy',
	'label_nettoyage_journalier' => 'Aktivovať denné čistenie archívov',
	'label_notif_active' => 'Aktivovať oznamy',
	'label_notif_mail' => 'E-mailová adresa na oznamy',
	'label_obsolete_jours' => 'Zachovávanie záloh',
	'label_prefixe_sauvegardes' => 'Predpona',
	'label_sauvegarde_reguliere' => 'Aktivovať pravidelné zálohovanie',
	'label_structure' => 'Štruktúra tabuliek',
	'label_toutes_tables' => 'Zálohovať všetky tabuľky',
	'legend_cfg_generale' => 'Všeobecné vlastnosti databáz',
	'legend_cfg_notification' => 'Oznamy',
	'legend_cfg_sauvegarde_reguliere' => 'Automatické spracúvanie',

	// M
	'message_aucune_sauvegarde' => 'Na stiahnutie nie je pripravená žiadna záloha.',
	'message_cleaner_sujet' => 'Vyčistenie databáz',
	'message_notif_cleaner_intro' => 'Automatické vymazanie zastaralých databáz  (tých, ktoré majú @duree@ dní) bolo úspešne dokončené. Vymazané boli tieto súbory: ',
	'message_notif_sauver_intro' => 'Zálohu databázy @base@ úspešne vytvoril(a) @auteur@.',
	'message_sauvegarde_nok' => 'Chyba pri vytváraní zálohy databázy.',
	'message_sauvegarde_ok' => 'Záloha databázy bola úspešne vytvorená.',
	'message_sauver_sujet' => 'Záloha databázy @base@',
	'message_telechargement_nok' => 'Chyba pri sťahovaní.',

	// T
	'titre_boite_historique' => 'Zálohy MySQL dostupné na stiahnutie',
	'titre_boite_sauver' => 'Vytvoriť zálohu MySQL',
	'titre_page_configurer' => 'Nastavenia modulu Automatická záloha',
	'titre_page_saveauto' => 'Zálohovať databázu vo formáte MySQL',
	'titre_saveauto' => 'Automatická záloha'
);

?>

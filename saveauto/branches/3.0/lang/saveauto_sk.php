<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/saveauto?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Pozor:',

	// C
	'colonne_date' => 'Dátum',
	'colonne_nom' => 'Názov',
	'colonne_taille_octets' => 'Veľkosť',

	// E
	'envoi_mail' => 'Zálohy odoslané',
	'erreur_config_inadaptee_mail' => 'Nesprávne nastavenia – váš server neponúka funkciu odosielania e-mailov!',
	'erreur_impossible_creer_verifier' => 'Nedá sa vytvoriť súbor @fichier@: skontrolujte právo zapisovať v priečinku @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Tabuľky z databázy sa nedajú vypísať.',
	'erreur_mail_fichier_lourd' => 'Záložný súbor je priveľký na to, aby bol odoslaný e-mailom. Jeho kópiu môžte získať pomocou administračného rozhrania stránky alebo prostredníctvom FTP v umiestnení: @fichier@',
	'erreur_mail_sujet' => 'Chyba zálohy SQL',
	'erreur_probleme_donnees_corruption' => 'Problém s údajmi v tabuľke @table@ table, môže byť poškodená!',
	'erreur_repertoire_inaccessible' => 'Do priečinka @rep@ sa nedá zapisovať.',
	'erreur_repertoire_inexistant' => 'Priečinok @rep@ neexistuje. Prosím, skontrolujte nastavenie štruktúry svojej stránky.',
	'erreur_sauvegarde_intro' => 'Chybová správa znie takto:',
	'erreurs_config' => 'V nastaveniach je chyba alebo viac chýb',

	// H
	'help_accepter' => 'Nepovinné: uložiť len tabuľky s určitým reťazcom v ich názve, napr. directory_, dolezity, vec.
             Ak chcete akceptovať všetky tabuľky v databáze, nezadajte nič. Rozličné názvy od seba oddeľte bodkočiarkou (;)',
	'help_envoi' => 'Nepovinné: ak zadáte e-mailovú adresu príjemcu, pošle zálohu e-mailom',
	'help_eviter' => 'Nepovinné: ak je v názve tabuľky určitý reťazec: údaje sú ignorované (ale nie štruktúra). Rozličné názvy od seba oddeľte bodkočiarkou (;).',
	'help_gz' => 'V opačnom prípade budú zálohy vo formáte .sql',
	'help_mail_max_size' => 'Niektoré databázy môžu prevyšovať maximálnu veľkosť určenú pre dokumenty pripojené k e-mailom. Zistite u poskytovateľa hostingu, aká maximálna veľkosť je povolená. Predvolený limit je 2 MB.',
	'help_msg' => 'Na obrazovke zobrazí správu o úspešnom dokončení',
	'help_obsolete' => 'Podľa počtu dní od vytvorenia určí, či sa archív považuje za neaktuálny a ak je neaktuálny, odstráni ho zo servera.
             Na deaktivovanie tejto funkcie zadajte -1',
	'help_prefixe' => 'Nepovinné: zadajte predponu pre názvy súborov zálohy',
	'help_rep' => 'Priečinok na ukladanie súborov (adresa, ktorá sa začína <strong>koreňovým</strong> adresárom SPIPu, napr. tmp/data/). <strong>MUSÍ</strong> sa končiť /.',
	'help_restauration' => '<strong>Pozor!!!</strong> vytvorené zálohy <strong>nie sú vo formáte SPIPu:</strong>
                Je zbytočné snažiť sa ich použiť pomocou nástroja na riadenie SPIPu.<br /><br />
             Na každú obnovu databázy musíte použiť rozhranie programu <strong>phpmyadmin</strong> svojho
             databázového servera: na karte <strong>"SQL"</strong> použite tlačidlo s názvom
             <strong>"Umiestnenie textového súboru"</strong> na výber súboru zálohy
             (ak treba, zaškrtnite možnosť "gzipped" (= vo formáte gzip)) potom kliknite na OK.<br /><br />
             Zálohy <strong>xxxx.gz</strong> alebo <strong>xxx.sql</strong> obsahujú súbor vo formáte SQL s príkami,
             ktoré sa používajú na <strong>odstránenie</strong> existujúcich tabuliek SPIPu a na ich <strong>nahradenie</strong>
             údajmi v archívoch. Všetky  <strong>novšie</strong> údaje ako tie v zálohe sa preto <strong>STRATIA!</strong>',
	'help_titre' => 'Táto stránka sa používa na nastavenie možností automatickej zálohy databázy.',

	// I
	'info_mail_message_mime' => 'Toto je správa formátovaná v MIME.',
	'info_sauvegardes_obsolete' => 'Záloha databázy sa ukladá na @nb@ dní od dátumu jej vytvorenia.',
	'info_sql_base' => 'Databáza:',
	'info_sql_compatible_phpmyadmin' => 'Súbor SQL 100%-ne kompatibilný s programom PHPMyadmin',
	'info_sql_date' => 'Dátum:',
	'info_sql_debut_fichier' => 'Začiatok súboru',
	'info_sql_donnees_table' => 'Údaje z tabuľky @table@',
	'info_sql_fichier_genere' => 'Tento súbor vytvára zásuvný modul saveauto',
	'info_sql_fin_fichier' => 'Koniec súboru',
	'info_sql_ipclient' => 'IP klienta:',
	'info_sql_mysqlversion' => 'Verzia MySQL:',
	'info_sql_os' => 'Operačný systém servera:',
	'info_sql_phpversion' => 'Verzia PHP:',
	'info_sql_plugins_utilises' => 'používa sa @nb@ zásuvných modulov:',
	'info_sql_serveur' => 'Server:',
	'info_sql_spip_version' => 'Verzia SPIPu:',
	'info_sql_structure_table' => 'Štruktúra tabuľky @table@',
	'info_telecharger_sauvegardes' => 'V tabuľke nižšie sú uvedené všetky zálohy vytvorené pre vašu stránku, ktoré možno stiahnuť.',

	// L
	'label_adresse' => 'Na adresu:',
	'label_compression_gz' => 'Zazipovať súbor zálohy:',
	'label_donnees' => 'Údaje z tabuliek:',
	'label_donnees_ignorees' => 'Ignorované údaje:',
	'label_frequence' => 'Frekvencia zálohy: každých',
	'label_mail_max_size' => 'Maximálna veľkosť súborov pripájaných k e-mailom (v MB):',
	'label_message_succes' => 'Zobraziť správu o úspešnom vytvorení, ak bude záloha v poriadku:',
	'label_nom_base' => 'Názov databázy SPIPu:',
	'label_obsolete_jours' => 'Zálohy sa pokladajú za neaktuálne po uplynutí:',
	'label_prefixe_sauvegardes' => 'Predpona zálohy:',
	'label_repertoire_stockage' => 'Priečinok na ukladanie:',
	'label_restauration' => 'Obnoviť zálohu:',
	'label_structure' => 'Štruktúra tabuliek:',
	'label_tables_acceptes' => 'Akceptované tabuľky:',
	'legend_structure_donnees' => 'Položky, ktoré treba zálohovať:',

	// M
	'message_aucune_sauvegarde' => 'Žiadne zálohy neexistujú.',
	'message_pas_envoi' => 'Záloha nebude odoslaná!',

	// S
	'sauvegarde_erreur_mail' => 'Počas vytvárania zálohy databázy sa vyskytla chyba v zásuvnom module "saveauto".',
	'sauvegarde_ok_mail' => 'Záloha databázy a jej odoslanie e-mailom vykonané úspešne!',
	'saveauto_titre' => 'Záloha SQL',

	// T
	'titre_boite_historique' => 'História záloh',
	'titre_boite_sauver' => 'Zásuvný modul Saveauto: zálohy databázy SQL',
	'titre_page_saveauto' => 'Zálohy databázy',
	'titre_saveauto' => 'Automatické zálohy',

	// V
	'valeur_jours' => ' dní'
);

?>

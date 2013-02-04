<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/notation?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'Prístupnosť',
	'afficher_tables' => 'Zobraziť hodnotenia',
	'aide' => 'Pomocník',
	'articles' => 'Články',
	'auteur' => 'Autor',

	// B
	'bouton_radio_fermee' => 'Zatvorené',
	'bouton_radio_ouvert' => 'Otvorené',
	'bouton_voter' => 'Hodnotiteľ',

	// C
	'change_note_label' => 'Povoliť hodnotiteľom meniť svoju známku',
	'configuration_notation' => 'Nastaviť hodnotenia',
	'creation' => 'Vytvorenie tabuliek',
	'creation_des_tables_mysql' => 'Vytvorenie tabuliek',
	'cree' => 'Tabuľky vytvorené',
	'creer_tables' => 'Vytvoriť tabuľky',

	// D
	'date' => 'dátum',
	'derniers_votes' => 'Najnovšie hodnotenia',
	'destruction' => 'Zničenie tabuliek',
	'detruire' => '<strong style="color:red">Pozor, tento príkaz zničí tabuľky zásuvného modulu!</strong><br />Mali by ste ho používať, iba ak chcete zásuvný modul odinštalovať.',
	'detruit' => 'Tabuľky zničené.',

	// E
	'effacer_tables' => 'Vymazať tabuľky',
	'err_balise' => '[NOTATION_ERR: tag mimo cyklu článku]',
	'err_db_notation' => '[NOTATION ERREUR: iba jedno hodnotenie na článok]',
	'exemple' => 'Rozdelenie známok (známka = 5, váha = @ponderation@): ',
	'explication_accepter_note' => 'Ak je "zatvorené", hodnotenie sa dá aktivovať samostatne na objekty, ktoré majú túto funkciu.',

	// I
	'info_acces' => 'Otvoriť hodnotenie: ',
	'info_etoiles' => 'Tento parameter vám umožňuje zmeniť maximálnu hodnotu hodnotenia (počet hviezdičiek od 1 do 10, a 5 podľa predvolených nastavení).<br />
                    <strong style="color:red">/!\\ Pozor:</strong> toto nastavenie nesmiete meniť po uložení hodnotenia, lebo hodnotenia sa neprepočítajú, čo môže spôsobiť nepresnosť vo vyhodnotení.<br />
                    Tieto parametre musia byť pri vytváraní hodnotení rovnaké.',
	'info_fonctionnement_note' => 'Ako hodnotenie funguje',
	'info_ip' => 'Na čo najľahšie používanie sa hodnotenie priraďuje IP adrese hodnotiteľa, čo s určitými nevýhodami bráni následným duplicitným hodnoteniam. Najmä ak riadite hlasovanie autorov.<br />
                V tomto prípade sa hodnotenie priraďuje k prihlasovaciemu údaju používateľa (samozrejme, keď sa zaregistroval).<br />
                Ak chcete zaistiť jedinečnosť hodnotenia, vyhraďte hodnotenie <b>iba</b> pre zaregistrovaných používateľov (vyššie).',
	'info_modifications' => 'Zmeny známok',
	'info_ponderation' => 'Váha umožňuje prirátať hodnotu článkom, ktoré dostali dosť hlasov. <br /> Zadajte počet hlasov, nad ktorý je podľa vás hodnotenie spoľahlivé.',
	'ip' => 'IP',
	'item_adm' => 'administrátorom ',
	'item_all' => 'všetkým',
	'item_aut' => 'autorom',
	'item_id' => 'jedno hodnotenie na používateľa',
	'item_ide' => 'zaregistrovaným ľuďom',
	'item_ip' => 'jeden hlas/IP',

	// J
	'jaidonnemonavis' => 'Svoj názor som povedal(a)!',
	'jaime' => 'Páči sa mi',
	'jaimepas' => 'Nepáči sa mi',
	'jaimeplus' => 'Už sa mi viac nepáči',
	'jechangedavis' => 'Sťahujem svoj názor',

	// L
	'label_accepter_note' => 'Stav hodnotenia všetkých objektov',

	// M
	'moyenne' => 'Priemer',
	'moyennep' => 'Vážený priemer',

	// N
	'nb_etoiles' => 'Hodnota známok',
	'nbobjets_note' => 'Počet objektov so známkou: ',
	'nbvotes' => 'Počet hodnotení',
	'nbvotes_moyen' => 'Priemerný počet hodnotení podľa objektu: ',
	'nbvotes_total' => 'Celkový počet hodnotení na stránke:',
	'notation' => 'Hodnotenia',
	'note' => 'Známka: ',
	'note_1' => 'Známka: 1',
	'note_10' => 'Známka: 10',
	'note_2' => 'Známka: 2',
	'note_3' => 'Známka: 3',
	'note_4' => 'Známka: 4',
	'note_5' => 'Známka: 5',
	'note_6' => 'Známka: 6',
	'note_7' => 'Známka: 7',
	'note_8' => 'Známka: 8',
	'note_9' => 'Známka: 9',
	'note_pond' => 'Vážené známky',
	'notes' => 'Známky',

	// O
	'objets' => 'Objekty',

	// P
	'param' => 'Nastavenie',
	'ponderation' => 'Váha známky',

	// T
	'titre_ip' => 'Spôsob fungovania:',
	'topnb' => '10 najlepšie ohodnotených objektov',
	'topten' => '10 najlepších známok',
	'toptenp' => '10 najlepších (vážených) známok ',
	'totaux' => 'Spolu',

	// V
	'valeur_nb_etoiles' => 'Hodnotenie od 1 do ',
	'valeur_ponderation' => 'Váha',
	'vos_notes' => 'Vašich 5 najlepších známok',
	'vote' => 'hodnotenie',
	'voter' => 'Hodnotiť: ',
	'votes' => 'hodnotení',
	'votre_note' => 'Vaša známka'
);

?>

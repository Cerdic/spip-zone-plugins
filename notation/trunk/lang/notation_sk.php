<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'Prístup',
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
	'info_etoiles' => 'Ce paramètre vous permet de modifier la valeure maximale de la note (le nombre d\'étoiles, entre 1 et 10, et 5 par défaut).<br />
                    <strong style="color:red">/!\\ Attention</strong> : vous ne devez pas toucher à ce paramètre une fois la notation engagée car les notes ne seront pas recalculées et cela peut provoquer des incohérences dans la notation...<br />
                    Ce paramètres doit être fixé une fois pour toute à la création des notes.', # NEW
	'info_fonctionnement_note' => 'Ako hodnotenie funguje',
	'info_ip' => 'Pour être le plus facile possible d\'utilisation, la note est fixée sur l\'adresse IP du votant, ce qui évite deux votes successifs dans la base, avec quelques inconvénients... en particulier si vous gérez des votes d\'auteurs.<br />
                Dans ce cas, on fixe la note sur l\'identifiant de l\'utilisateur (quand celui-ci est enregistré, bien sûr).<br />
                Si vous voulez garantir l\'unicité de la note, limitez le vote aux <b>seules</b> personnes enregistrées (ci-dessus).', # NEW
	'info_modifications' => 'Zmeny známok',
	'info_ponderation' => 'Le facteur de pondération permet d\'accorder plus de valeur aux articles ayant reçu suffisament de votes. <br /> Entrez ci-dessous la nombre de votes au delà duquel vous pensez que la note est fiable.', # NEW
	'ip' => 'IP',
	'item_adm' => 'administrátorom ',
	'item_all' => 'všetkým',
	'item_aut' => 'autorom',
	'item_id' => 'jedno hodnotenie na používateľa',
	'item_ide' => 'zaregistrovaným ľuďom',
	'item_ip' => 'jedno hodnotenie na IP',

	// J
	'jaidonnemonavis' => 'J\'ai donné mon avis !', # NEW
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
	'nbvotes' => 'Počet známok',
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

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Aktivovať riadenie podľa kľúčových slov',
	'admin_complets' => 'Plnoprávni administrátori',
	'admin_restreints' => 'Administrátori s obmedzeniami?',
	'admin_tous' => 'Všetci administrátori (vrátane tých s obmedzeniami)',
	'administrateur' => 'administrátor',
	'admins' => 'Administrátori',
	'admins_redacs' => 'Administrátori a redaktori',
	'admins_rubriques' => 'administrátori prepojení s rubrikami majú:',
	'attention_crayons' => '<small><strong>Pozor.</strong> Tieto nastavenia môžu fungovať iba ak použijete zásuvný modul, ktorý ponúka rozhranie na úpravu (ako napríklad <a href="http://www.spip-contrib.net/Les-Crayons">Farbičky.</a>)</small>',
	'attention_version' => 'Majte na pamäti, že tieto nastavenia nemusia vo vašej verzii SPIPu fungovať:',
	'auteur_message_advitam' => 'Autor správy natrvalo',
	'auteur_message_heure' => 'Autor správy na hodinu',
	'auteur_modifie_article' => '<strong>Článok upravuje autor:</strong> každý redaktor môže upravovať publikované články, ktoré napísal.
	<br />
	<i>Pozn.: táto možnosť sa bude vzťahovať aj pri zaregistrovaných návštevníkoch, ak budú uvedení ako autori a ak sa bude využívať špeciálne rozhranie.</i>',
	'auteur_modifie_email' => '<strong>E-mailovú adresu upravuje redaktor:</strong> v zázname o svojich osobných údajoch si každý redaktor môže zmeniť e-mailovú adresu.',
	'auteur_modifie_forum' => '<strong>Diskusné fórum moderuje autor:</strong> každý redaktor môže moderovať diskusné fórum k článkom, ktoré napísal.',
	'auteur_modifie_petition' => '<strong>Petíciu moderuje autor:</strong> každý redaktor môže moderovať petíciu, ktorú sám vytvoril.',

	// C
	'config_auteurs' => 'Nastavenia autorov',
	'config_auteurs_rubriques' => 'Aký typ autorov môže byť <b>prepojený s rubrikami?</b>',
	'config_auteurs_statut' => 'Aká je <b>predvolená funkcia</b> pri zápise autora?',
	'config_plugin_qui' => 'Kto môže <strong>meniť nastavenia</strong>  zásuvných modulov (aktivácia, atď.)?',
	'config_site' => 'Nastavenia stránky',
	'config_site_qui' => 'Kto môže<strong>meniť nastavenia</strong> stránky?',
	'crayons' => 'Crayons',

	// D
	'deja_defini' => 'Inde už boli definované tieto povolenia:',
	'deja_defini_suite' => 'Zásuvný modul "Autorita" nedokáže zmeniť niektoré z týchto nastavení, a preto nebudú fungovať.
	<br />Ak chcete vyriešiť tento problém, mali by ste skontrolovať, či sú v súbore  <tt>mes_options.php</tt> (alebo v inom aktívnom zásuvnom module) definované tieto funkcie.',
	'descriptif_1' => 'Táto stránka s nastaveniami je vyhradená pre webmasterov stránky:',
	'descriptif_2' => '<p>Ak chcete upraviť tento zoznam, upravte, prosím, súbor <tt>config/mes_options.php</tt> (ak treba, vytvorte ho) a zadajte zoznam prihlasovacích údajov webmasterov v tejto podobe:</p>
<pre>&lt;?php
  define(
    \'_ID_WEBMESTRES\',
    \'1:5:8\');
?&gt;</pre>
<p>Počnúc verziou 2.1 sa práva webmastera dajú administrátorovi prideliť na stránke na úpravu údajov o autorovi.</p>
<p>Poznámka: webmasteri definovaní týmto spôsobom už viac nemusia overovať svoju totožnosť cez FTP pri vykonávaní zásadných operácií (napríklad pri aktualizácii databázy na novú verziu).</p>

<a href=\'http://www.spip-contrib.net/-Autorite-\' class=\'spip_out\'>Prečítajte si dokumentáciu</a>
', # MODIF
	'details_option_auteur' => '<small><br />Nateraz možnosť "autor" funguje iba pre zaregistrovaných autorov (napríklad diskusné fóra, na ktoré sa treba prihlásiť). A ak je táto možnosť aktivovaná, možnosť upravovať diskusné fóra majú aj administrátori stránky.
	</small>',
	'droits_des_auteurs' => 'Práva autorov',
	'droits_des_redacteurs' => 'Práva redaktorov',
	'droits_idem_admins' => 'rovnaké práva pre všetkých administrátorov',
	'droits_limites' => 'obmedzené práva na tieto rubriky',

	// E
	'effacer_base_option' => '<small><br />Odporúčaná možnosť je "človek", štandardná možnosť v SPIPe je "administrátori" (ale vždy s kontrolou cez FTP).</small>',
	'effacer_base_qui' => 'Kto môže <strong>mazať</strong> databázu stránky?',
	'espace_publieur' => 'Otvoriť zónu publikovania',
	'espace_publieur_detail' => 'Nižšie vyberte oblasť, z ktorej má vzniknúť otvorená zóna na publikovanie pre redaktorov a zaregistrovaných návštevníkov (pod podmienkou, že máte rozhranie ako napr. Farbičky a formulár na odoslanie článku):',
	'espace_publieur_qui' => 'Chcete otvoriť publikovanie — za administrátormi:',
	'espace_wiki' => 'Zóna wiki',
	'espace_wiki_detail' => 'Nižšie vyberte oblasť, ktorá bude slúžiť ako wiki, čiže budú ju môcť upravovať všetci z verejne prístupnej stránky (pod podmeinkou, že máte rozhranie, ako napr. Farbičky):',
	'espace_wiki_mots_cles' => 'Zóna wiki podľa kľúčových slov',
	'espace_wiki_mots_cles_detail' => 'Vyberte si kľúčové slová, ktoré aktivujú režim wiki, teda taký, ktorý budú môcť upravovať všetci z verejne prístupnej stránky (pod podmienkou, že máte rozhranie, ako napr. Farbičky)',
	'espace_wiki_mots_cles_qui' => 'Chcete otvoriť túto stránku wiki za administrátormi:',
	'espace_wiki_qui' => 'Chcete otvoriť túto stránku wiki — za administrátormi:',

	// F
	'forums_qui' => '<strong>Diskusné fóra:</strong> kto môže meniť obsah diskusných fór:',

	// I
	'icone_menu_config' => 'Autorita',
	'infos_selection' => '(viac možností môžete vybrať klávesom Shift)',
	'interdire_admin' => 'Zaškrtnite polia, ak chcete zakázať administrátorom vytváranie',

	// M
	'mots_cles_qui' => '<strong>Kľúčové slová:</strong> kto môže vytvárať a upravovať kľúčové slová:',

	// N
	'non_webmestres' => 'Toto nastavenie neplatí pre webmasterov.',
	'note_rubriques' => '(Majte na pamäti, že  vytvárať rubriky môžu len administrátori a administrátori s obmedzeniami to môžu robiť len vo svojich rubrikách.)',
	'nouvelles_rubriques' => 'z nových rubrík do koreňového adresára stránky',
	'nouvelles_sous_rubriques' => 'z nových podrubrík v stromovej štruktúre.',

	// O
	'ouvrir_redacs' => 'Otvoriť redaktorom stránky:',
	'ouvrir_visiteurs_enregistres' => 'Otvoriť zaregistrovaným návštevníkom:',
	'ouvrir_visiteurs_tous' => 'Otvoriť všetkým návštevníkom stránky:',

	// P
	'pas_acces_espace_prive' => '<strong>Bez prístupu do súkromnej zóny:</strong> redaktori nemajú prístup do súkromnej zóny.',
	'personne' => 'Hocikto',
	'petitions_qui' => '<strong>Podpisy:</strong> kto môže meniť podpisy pod petíciami:',
	'publication' => 'Publikovanie',
	'publication_qui' => 'Kto môže publikovať na stránke:',

	// R
	'redac_tous' => 'Všetci redaktori',
	'redacs' => 'redaktorom stránky',
	'redacteur' => 'redaktor',
	'redacteur_lire_stats' => '<strong>Redaktori môžu vidieť štatistiky:</strong> redaktori môžu vidieť štatistiky.',
	'redacteur_modifie_article' => '<strong>Redactor upravuje odoslané články:</strong> každý redaktor môže upravovať články odoslaný na publikovanie aj ak je bez autora.',
	'refus_1' => '<p>Iba webmasteri stránky',
	'refus_2' => 'majú povolené meniť tieto parametre.</p>
<p>Ak chcete zistiť viac informácií, prečítajte si <a href="http://www.spip-contrib.net/-Autorite-">dokumentáciu.</a></p>',
	'reglage_autorisations' => 'Nastavenie povolení',

	// S
	'sauvegarde_qui' => 'Kto môže vytvárať <strong>zálohy?</strong>',

	// T
	'tous' => 'Všetko',
	'tout_deselectionner' => ' odznačiť všetko',

	// V
	'valeur_defaut' => '(predvolená hodnota)',
	'visiteur' => 'návštevník',
	'visiteurs_anonymes' => 'anonymní návštevníci môžu vytvárať nové stránky.',
	'visiteurs_enregistres' => 'prihláseným návštevníkom',
	'visiteurs_tous' => 'všetkým návštevníkom stránky.',

	// W
	'webmestre' => 'Webmaster',
	'webmestres' => 'Webmasteri'
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/faq-manuelsite?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'Diskusné fóra sú podľa predvolených nastavení aktivované v článkoch  @complement@; dajú sa deaktivovať jedno po druhom. Návštevníci môžu reagovať na vaše články. Ak niekto pošle príspevok k vášmu článku, dostanete o tom oznam cez e-mail.  Malé negatívum: spamy nie je vždy ľahké nájsť, a tak ich budete musieť vytriediť manuálne. Ak chcete spracovať príspevok v diskusnom fóre (vymazať ho, ak sa vám nepáči, alebo ho označiť ako spam, ak to je spam):
-* Ak ste prihlásený (-á), na verejne prístupnej stránke na stránke s článkom sú zobrazené dve tlačidlá "Odstrániť príspevok" alebo "SPAM"
-* V súkromnej zóne prejdite do menu Aktivita / Riadiť diskusné fóra',
	'forum_q' => 'Ako riadiť diskusné fóra?',

	// I
	'img' => 'Neexistuje žiadna "správna" veľkosť na zobrazenie obrázka v článku. V každom prípade netreba posielať obrázok široký 3000 pixelov, lebo ho žiadna obrazovka nezobrazí naraz! Výnimkou je prípad, keď je dokument určený na tlač.
-* Ak bude obrázok zaradený do textu článku, všetko závisí od jeho vlastností: ak je orientovaný na výšku, výška 200 px by mala stačiť, v opačnom prípade treba dať pozor na nedokonalosti v zobrazení; ak je orientovaný na šírku, môže mať maximálnu šírku až {{@largeur_max@}} pixelov.
-* Ak bude obrázok súčasťou portfólia k článku, neodporúča sa prekročiť šírku 1000 pixelov a výšku 600 pixelov.

{Pozor, maximálna veľkosť nesmie prekročiť 150 MB, inak sa obrázok nebude dať stiahnuť.}',
	'img_nombre' => 'Do článku sa dá poslať viac fotiek jedným klikom:
-* Vybrané fotky skopírujte do priečinka na svojom pevnom disku
-* Upravte ich veľkosť
-* Vytvorte z nich súbor zip
-* Tento súbor zip pripojte k článku. Na konci sťahovania sa vás systém spýta, čo chcete so súborom urobiť, napríklad môžete všetky fotky vložiť do portfólia.',
	'img_nombre_q' => 'Ako ľahko napĺňať portfólio?',
	'img_ou_doc' => 'Na vkladanie obrázkov do textu článku používa najmä tag <code><imgXX|center>.</code> Ak však chcete zobraziť pod obrázkom jeho nadpis alebo opis, použite tag <code><docXX|center>.</code>',
	'img_ou_doc_q' => '<code><imgXX> alebo <docXX>?</code>',
	'img_q' => 'Akú veľkosť by mala mať moja fotka?',

	// S
	'son' => 'Uložte si zvukový záznam vo formáte mp3 v režime mono s frekvenciou 11 alebo 22 kHz a bitovou rýchlosťou (rýchlosť pri kompresii) 64 kb/s (alebo viac, ak chcete vyššiu kvalitu).
	
Súbor mp3 zaraďte do článku ako obrázok a dajte mu nadpis, prípadne pridajte opis a poďakovanie (spolu)autorom. Nakoniec ho vložte do článku na miesto, na ktoré chcete  <code><docXX|center|player></code>. Na verejne prístupnej stránke sa zobrazí ikona USB kľúča, aby návštevníci mohli zvukový záznam spustiť.
_ {Pozor, maximálna veľkosť súboru je 150 MB alebo dĺžka približne 225 minút}',
	'son_audacity' => 'Na prácu s audio súborom môžete využiť program Audacity (Mac, Windows, Linux), ktorý sa dá stiahnuť odtiaľto [->http://audacity.sourceforge.net/]. Niekoľko tipov:
-* Po inštalácii programu budete potrebovať aj knižnicu na kódovanie lame mp3 [->http://audacity.sourceforge.net/help/faq?s=install&item=lame-mp3].
-* Ak chcete súbor previesť do režimu mono: Menu {Stopy/Stereo stopy na mono}
-* Ak chcete vytvoriť súbor mp3: Menu {Súbor/Exportovať}
-* Ak chcete nastaviť bitovú rýchlosť: Menu {Súbor/Exportovať/Možnosti/Kvalita}',
	'son_audacity_q' => 'Ako pripraviť súbor so zvukom?',
	'son_q' => 'Ako pridať k článku súbor so zvukom?',

	// T
	'thumbsites' => 'V rubrike {{@rubrique@}} kliknite na tlačidlo "Odkázať na stránku". Zadajte adresu stránky a potvrďte stránku; systém sa pokúsi získať názov, opis a obrázok stránky online.  Ak to bude potrebné, upravte názov a opis. Ak sa obrázok nevytvorí automaticky, urobte snímku obrazovky s rozmermi 120 x 90 a vložte ju ako logo stránky.',
	'thumbsites_q' => 'Ako vytvoriť odkaz na (inú) stránku na stránke s odkazmi?',
	'trier' => 'Čísla pred názvami článkov/rubrík/ dokumentov umožňujúce manipulovať s poradím, v akom sa zobrazí. Syntax tvorí číslo,  za ktorým nasleduje bodka a medzera',
	'trier_q' => 'Ako nastaviť správne poradie zobrazenia článkov, rubrík alebo pripojených súborov?',

	// V
	'video_320x240' => 'Uložte svoje video vo formáte flv (streaming flash) s rozmermi 320 x 240 pixelov s bitovou rýchlosťou (rýchlosť pri kompresii) 400 kb/s a v režime mono 64 kb/s. Ak chcete súbor videa kovertovať,  môžete využiť program avidemux (Mac, Windows, Linux), ktorý si môžete stiahnuť odtiaľto [->http://www.avidemux.org/]. 

Vytvorený súbor zaraďte do článku ako pripojený dokument, dajte mu nadpis, prípadne pridajte podpis a poďakovanie (spolu)autorom aj veľkosť (šírka 320, výška 240). Nakoniec ho pridajte do článku tam, kde chcete <code><docXX|center|video></code>. Na verejne prístupnej stránke sa zobrazí ikona USB kľúča, aby si mohli návštevníci video prehrať. 
_ {Pozor, maximálna veľkosť súboru je 150 MB alebo dĺžka približne 37,5 minúty}',
	'video_320x240_q' => 'Ako pridať video k článku?',
	'video_dist' => 'Ak sa vaše video nachádza na stránke DailyMotion, YouTube alebo Viméo  a máte ho na novej karte vášho prehliadača, prejdite na stránku, kde sa video nachádza a skopírujte internetovú adresu videa. Na stránke úprav svojho článku kliknite na tlačidlo "Pridať video" a prilepte jeho internetovú adresu. Potom vložte do textu článku <code><videoXX|center></code>',
	'video_dist_q' => 'Ako k článku pridať video z dailymotion (youtube, atď.)?'
);

?>

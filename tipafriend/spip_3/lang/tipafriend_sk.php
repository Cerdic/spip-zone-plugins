<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/tipafriend?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_activer' => 'Aktivovať',
	'bouton_annuler' => 'Zrušiť',
	'bouton_desactiver' => 'Deaktivovať',
	'bouton_envoyer' => 'Poslať',
	'bouton_fermer' => 'Zatvoriť',
	'bouton_reessayer' => 'Začať znova',
	'bouton_reset' => 'Pôvodné',
	'bouton_send_by_mail' => 'Poslať e-mailom',
	'bouton_send_by_mail_ttl' => 'Poslať túto stránku e-mailom',

	// C
	'cfg_legend_balise' => 'O tagu "#TIPAFRIEND"',
	'cfg_legend_patron' => 'O typoch e-mailových adries',
	'cfg_legend_squelette' => 'O odosielacom formulári',
	'cfg_texte_descr' => 'Tento zásuvný modul SPIPu pridáva modul na poslanie stránky (<i>jej obsahu, adresy a správy</i>) jednému príjemcovi alebo viacerým.',
	'cfg_titre_descr' => 'Nastavenie zásuvného modulu <i>Odporučiť priateľom</i>',
	'cfgform_comment_close_button' => 'Ak je podľa predvolených nastavení aktivovaná, táto možnosť vám umožňuje rozhodnúť, či sa v spodnej časti okna zobrazí tlačidlo "Zatvoriť"; <strong>táto možnosť je automaticky deaktivovaná ak sú deaktivované hlavičky, ktoré sú vymenované vyššie.</strong>',
	'cfgform_comment_contenu' => 'Tu si vyberte typ objektu SPIPu (<i>článok, novinka, autor, a i.</i>) ktorý bude pripojený do e-mailu.',
	'cfgform_comment_header' => 'Táto možnosť vám umožňuje rozhodnúť sa, či stránka musí mať tag  &lt;head&gt; ("hlavička") alebo nie (<i>môže byť užitočné deaktivovať túto možnosť, ak používate javascriptové okno typu "thickbox" alebo ich chcete zobraziť iným spôsobom v rovnakom kontexte s rámom</i>).',
	'cfgform_comment_javascript' => 'Funkciu otvárania vyskakovacieho okna môžete deaktivovať (<i>napríklad v prípade použitia javascriptových okien typu "thickbox" alebo "fancybox"</i>).',
	'cfgform_comment_options' => 'musíte zadať úplné parametre, napríklad: "class=’thickbox’", budú automaticky pridané k odkazom na vaše šablóny; <b>používajte len jednoduché úvodzovky (’...’).</b>',
	'cfgform_comment_options_url' => 'Tu môžete zadať parametre, napríklad: "arg=valeur&arg2=nouvelle_valeur"; budú automaticky pridané k internetovej adrese, ktorú vytvorí tag.',
	'cfgform_comment_patron' => 'predvolený e-mail v klasickej verzii (<i>neformátovaný text</i>).',
	'cfgform_comment_patron_html' => 'Ak si vyberiete túto možnosť, odoslaný e-mail bude mať úplne rovnakú prvú šablónu v textovej verzii; ak chcete túto možnosť zrušiť, nechajte toto pole prázdne.',
	'cfgform_comment_reset' => 'Tu môžete definovať, čo sa stane vo formulári po stlačení tlačidla "Zrušiť" (<i>túto akciu môžete napríklad zmeniť tak, aby vám mohla pomôcť zatvoriť okno "thickbox" namiesto bežného okna</i>).',
	'cfgform_comment_squelette' => 'Ak ste vytvorili vlastnú šablónu pre dialógové okno zásuvného modulu  (<i>vytvorenú podľa súboru "tip_a_friend.html"</i>) zadajte ju tu; vaša šablóna bude musieť mať vyplnený formulár <b>"tipafriend_form".</b>',
	'cfgform_comment_taf_css' => '







Zásuvný modul definuje štýly CSS na modely štýlov distribúcie SPIPu; tieto štýly sa podľa predvolených nastavení vkladajú do formulára, ale môžete sa rozhodnúť, že ich tam nevložíte.',
	'cfgform_info_balise' => 'Tag vypíše odkaz na otvorenie stránky s formulárom na odoslanie. Zobrazený obrázok môžete zmeniť priamo úpravou šablóny  <strong>"modeles/tipafriend.html"</strong> zásuvného modulu.',
	'cfgform_info_patron_html' => '<strong>Ak je na vašej stránke nainštalovaný a aktivovaný zásuvný modul <a href="http://contrib.spip.net/?article3371"><strong>Faktor,</strong></a> dá sa vytvoriť HTML verzia odoslaného e-mailu.',
	'cfgform_info_patrons' => 'Vaše vlastné vzory sa umiestňujú do podpriečinka  <strong>patrons/</strong> vášho priečinka so šablónami.',
	'cfgform_info_squelettes' => 'Vaše vlastné šablóny sa priamo umiestňujú do priečinka so šablónami.',
	'cfgform_option_contenu_introduction' => 'Názov a úvod',
	'cfgform_option_contenu_rien' => 'Nič',
	'cfgform_option_contenu_tout' => 'Celý predmet',
	'cfgform_titre_close_button' => 'Pridať tlačidlo "Zatvoriť"',
	'cfgform_titre_contenu' => 'Obsah objektov SPIPu pridaných k e-mailu',
	'cfgform_titre_header' => 'Pripojiť hlavičky HTML',
	'cfgform_titre_javascript' => 'Štandardná funkcia javascriptu (otvorenie vyskakovacieho okna)',
	'cfgform_titre_options' => 'Pridaný (-é) atribút(y) k vytvorenému odkazu podľa tagu',
	'cfgform_titre_options_url' => 'Parameter (-tre) pridaný (-é) k URL odkazu, ktorý vytvorí tag',
	'cfgform_titre_patron' => 'Šablóna odoslaného e-mailu',
	'cfgform_titre_patron_html' => 'Šablóna e-mailu vo formáte HTML',
	'cfgform_titre_reset' => 'Akcia tlačidla na zrušenie',
	'cfgform_titre_squelette' => 'Šablóna, ktorá sa použije na formulár zásuvného modulu Odporučiť priateľom',
	'cfgform_titre_taf_css' => 'Predvoliť pridávanie definícií CSS',

	// D
	'doc_chapo' => 'Zásuvný modul "Odporučiť priateľom" ponúka kompletný formulár na odoslanie ({hocijakej}) stránky v SPIPe na viacero e-mailových adries.',
	'doc_en_ligne' => 'Dokumentácia',
	'doc_titre_court' => 'Dokumentácia zásuvného modulu Odporučiť priateľom',
	'doc_titre_page' => 'Dokumentácia zásuvného modulu "Odporučiť priateľom"',
	'docskel_sep' => '----',
	'documentation' => 'Táto stránka vám umožňuje otestovať, či sa zásuvný modul hodí na vašu stránku, vašu konfiguráciu a vaše prispôsobenia. Rôzne odkazy, ktoré SPIP ponúka, pridajú do jadra stránky objekt SPIPu alebo šablónu. Tieto doplnky môžete zmeniť úpravou príslušného parametra internetovej adresy.

{{{Tag TIPAFRIEND}}}

{{Využitie}}

Zásuvný modul ponúka tag na vytvorenie odkazu, ktorý otvorí stránku na odoslanie e-mailu s informáciami o fungovaní daného objektu SPIPu. K tomuto tagu môžete pridať jedinečný parameter, prípadne môžete definovať aj:
-* buď sa {{na vytvorenie tohto odkazu využije šablóna,}} potom treba zadať názov tejto šablóny ({bez prípony ".html"}) ; šablóna musí byť nahratá vo vašom priečinku šablón,
-* alebo je to {{typ uvedeného odkazu;}} ak zadáte parameter "{{mini}}", tag nakreslí iba obrázok odkazu bez textu "Odoslať túto správu".

{{Príklad}}

<cadre class="spip">
// tu je len tag
#TIPAFRIEND
// aby sa zobrazil iba obrázok
#TIPAFRIEND{mini}
// alebo s vlastnou šablónou
#TIPAFRIEND{mon_modele}
</cadre>

{{Testy}}

Tieto odkazy pridajú objekt SPIPu na aktuálnu stránku so zmenami v zobrazení tagu TIPAFRIEND.
- [Pridať článok 1,->@url_article@] <small>(id_article=...)</small>
- [Pridať novinku 2,->@url_breve@] <small>(id_breve=...)</small>
- [Obnoviť stránku,->@url_recalcul@]
- [Späť na čistú stránku.->@url_vierge@]

Ak chcete zmeniť parameter tagu na tejto testovacej stránke, k aktuálnej adrese stránky pridajte parameter "{{arg=...}}" ({ak napríklad chcete použiť parameter "mini", kliknite na panel s adresou vášho prehliadača a na koniec aktuálnej adresy pridajte "&arg=mini"}).

{{{Šablóny}}}

Tieto odkazy vám umožňujú otestovať šablóny využívané na vašej webovej stránke ({s fiktívnymi hodnotami}) alebo ich pridať na aktuálnu stránku.
- [Vložiť šablónu "tipafriend_mail_default.html"->@url_model@] <small>(model=...)</small>
- [Zobraziť šablónu ako nenaformátovaný text s fiktívnymi hodnotami->@url_model_brut@]
- [Zobraziť šablónu ako HTML s fiktívnymi hodnotami->@url_model_html@] <small>(vyžaduje si zásuvný modul {{[Facteur->http://contrib.spip.net/?article3371]}})</small>

{{{Parametre CFG pre TIPAFRIEND}}}

Ak je zásuvný modul {{[CFG: nástroj na nastavenie->http://contrib.spip.net/?rubrique575]}} aktivovaný na vašej stránky, po kliknutí na tento odkaz sa zobrazia hodnoty na nastavení uložené pre zásuvný modul "Odporučiť priateľom".

@cfg_param@',

	// E
	'error_dest' => 'Neuviedli ste žiadneho príjemcu',
	'error_exp' => 'Neuviedli ste vašu e-mailovú adresu',
	'error_exp_nom' => 'Musíte zadať svoje meno',
	'error_not_mail' => 'Zdá sa, že adresa, ktorú ste zadali, nie je e-mail',
	'error_one_is_not_mail' => 'Zdá sa, že aspoň jedna zo zadaných adries nie je e-mail',

	// F
	'form_dest_label' => 'E-mailové adresy príjemcov',
	'form_exp_label' => 'Vaša e-mailová adresa',
	'form_exp_nom_label' => 'Vaše meno',
	'form_exp_send_label' => '<em>Pripojíte kópiu e-mailu (pole "Cc")</em>',
	'form_intro' => 'Ak chcete poslať adresu tejto stránky, zadajte e-mailové adresy svojich kontaktov, svoju vlastnú e-mailovú adresu a hodnotenie. Ak chcete, môžete prípadne aj pridať komentár, ktorý bude pridaný do hlavného textu správy. <br /><small>{{*}} {Žiaden z týchto údajov nebude uložený.}</small>',
	'form_message_label' => 'Môžete pridať text',
	'form_separe_virgule' => '<em>Môžete uviesť viac adries oddelených bodkočiarkou.</em>',
	'form_title' => 'Poslať stránku e-mailom',

	// I
	'info_doc' => 'Ak máte problémy so zobrazením tejto stránky, [kliknite sem.->@link@]',
	'info_doc_titre' => 'Poznámka o zobrazení tejto stránky',
	'info_skel_doc' => 'Táto stránka dokumentácie je vytvorená ako šablóna SPIPu, ktorá funguje s bežnou distribúciou  ({súbory v priečinku "squelettes-dist/"}). Ak sa vám stránka nezobrazuje správne alebo ak vaša stránka využíva vlastné šablóny, odkazy uvedené nižšie vám pomôžu upraviť jej zobrazenie:

-* [Režim "nenáformátovaný text"->@mode_brut@] ({jednoduché html + tag INSERT_HEAD})
-* [Režim "šablóna Zpip"->@mode_zpip@] ({kompatibilné so šablónou Z})
-* [Režim "šablóna SPIP"->@mode_spip@] ({kompatibilné s distribúciou})',

	// L
	'licence' => 'Copyright © 2009 [Piero Wbmstr->http://contrib.spip.net/PieroWbmstr] distribuovaný s licenciou [GNU GPL v3.->http://www.opensource.org/licenses/gpl-3.0.html]',

	// M
	'mail_body_01' => '@nom_exped@ (kontakt: @mail_exped@) vás pozýva, aby ste si pozreli tento dokument zo stránky @nom_site@, možno vás bude zaujímať.',
	'mail_body_01_html' => '<strong>@nom_exped@</strong> (kontakt: <a href="mailto:@mail_exped@">@mail_exped@</a>) vás pozýva, aby ste si pozreli tento dokument zo stránky <strong>@nom_site@</strong>; možno vás bude zaujímať.',
	'mail_body_02' => '@nom_exped@ vložili ste túto správu:',
	'mail_body_02_html' => '@nom_exped@ vložili ste túto správu:',
	'mail_body_03' => 'Názov dokumentu: "@titre_document@"',
	'mail_body_03_html' => 'Názov dokumentu: "@titre_document@"',
	'mail_body_04' => 'Adresa tejto stránky na internete: @url_document@',
	'mail_body_04_html' => 'Adresa tejto stránky na internete: <a href="@url_document@">@url_document@</a>',
	'mail_body_05' => 'Obsah stránky (ako nenaformátovaný text):',
	'mail_body_05_html' => 'Obsah stránky:',
	'mail_body_extrait' => '(úryvok) ',
	'mail_titre_default' => 'Údaje o stránke @nom_site@',
	'message_envoye' => 'OK – Vaša správa bola odoslaná',
	'message_pas_envoye' => '!! – Vašu správu sa z neznámeho dôvodu nepodarilo odoslať. Prijmite naše ospravedlnenie a <a href="@self@" title="Recharger la page">skúste to znova.</a>',

	// N
	'new_window' => 'Nové okno',

	// P
	'page_test' => 'Testovacia stránka (lokálne)',
	'page_test_balise' => 'Zápis tagu TIPAFRIEND',
	'page_test_cfg_pas_installe' => 'Zdá sa, že zásuvný modul [CFG->http://contrib.spip.net/?rubrique575] nie je nainštalovaný.',
	'page_test_fin_simulation' => '— Koniec pridávania na simuláciu',
	'page_test_in_new_window' => 'Testovacia stránka v novom okne',
	'page_test_menu_inclure' => 'Vložiť šablónu "tipafriend_mail_default.html"',
	'page_test_models_comment' => 'Tieto odkazy vám umožňujú vyskúšať si modely, ktoré sa používajú na webovej stránke (<i>s fiktívnymi hodnotami</i>).',
	'page_test_test_model_brut' => 'Zobraziť šablónu ako nenaformátovaný text s fiktívnymi hodnotami',
	'page_test_test_model_html' => 'Zobraziť model HTML s fiktívnymi hodnotami',
	'page_test_title' => 'Test zásuvného modulu "Odporučiť priateľom"',
	'page_test_titre_inclusion_model' => '— Vloženie šablóny "@model@" (<i>fiktívne hodnoty</i>)',
	'page_test_titre_inclusion_objet' => '— Simulácia stránky @objet@ č. @id_objet@ (<i>nadpis + úvod</i>)',
	'popup_name' => 'Poslať informáciu e-mailom',

	// T
	'taftest_arguments_balise_dyn' => 'Parametre prijaté v dymanickom tagu',
	'taftest_arguments_balise_stat' => 'Argumenty prijaté v statickom tagu ',
	'taftest_chargement_patron' => 'nahrávanie šablóny "@patron@"',
	'taftest_content' => '<b><u>Podrobnosti odoslaného e-mailu</u></b>',
	'taftest_contexte_modele' => 'Kontext odoslaný do šablóny',
	'taftest_creation_objet_champs' => 'Vytvorenie objektu "Polia" pre objekt ID',
	'taftest_creation_objet_texte' => 'Vytvorenie objektu "Text" pre názov objektu',
	'taftest_from' => '<b><i>Odosielateľ</i></b>',
	'taftest_mail_content' => '<b><i>Jadro e-mailu</i></b>',
	'taftest_mail_content_html' => '<b><i>Jadro e-mailu v HTML</i></b>',
	'taftest_mail_headers' => '<b><i>Hlavičky</i></b>',
	'taftest_mail_retour' => '<b><i>Späť na funkciu mail()</i></b>',
	'taftest_mail_title' => '<b><i>Názov pošty</i></b>',
	'taftest_modele_demande' => 'Formát požadovaný od používateľa',
	'taftest_param_form' => 'Parametre, ktoré prešli do formulára',
	'taftest_patron_pas_trouve' => 'Šablóna "@patron@" sa nenašla!<br />Nahráva sa predvolená šablóna.',
	'taftest_skel_pas_trouve' => 'Šablóna ’@skel@’ sa nenašla.<br />Použije sa predvolená šablóna.',
	'taftest_title' => 'OdporučiťPriateľovi LADENIE',
	'taftest_to' => '<b><i>Príjemcovia</i></b>',
	'tipafriend' => 'Odporučiť priateľovi'
);

?>

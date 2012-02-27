<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/tipafriend?lang_cible=sk
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
	'cfg_titre_descr' => 'Nastavenie zásuvného modulu <i>Odporučiť priateľovi</i>',
	'cfgform_comment_close_button' => 'active par défaut, cette option vous permet de choisir de montrer ou non le bouton \'Fermer\' en bas de la fenêtre ; <strong>cette option est automatiquement désactivée si les en-têtes sont eux-mêmes désactivés ci-dessus</strong>.', # NEW
	'cfgform_comment_contenu' => 'Tu si vyberte typ objektu SPIPu (<i>článok, novinka, autor, a i.</i>) ktorý bude pripojený do e-mailu.',
	'cfgform_comment_header' => 'cette option vous permet de choisir si les informations de la balise &lt;head&gt; de la page doivent être présentes ou non (<i>il peut être utile de les désactiver si vous utilisez une fenêtre javascript type \'thickbox\', ou au contraire de forcer leur affichage dans le même contexte avec un contenu en frame</i>).', # NEW
	'cfgform_comment_javascript' => 'vous pouvez désactiver la fonction d\'ouverture de la popup (<i>dans le cas de l\'utilisation de fenêtres javascript type \'thickbox\' ou \'fancybox\' par exemple</i>).', # NEW
	'cfgform_comment_options' => 'vous devez indiquer des attributs complets, par exemple : "class=\'thickbox\'", ils seront automatiquement ajoutés au lien inclus dans vos squelettes ; <b>utilisez seulement des guillemets simples</b>.', # NEW
	'cfgform_comment_options_url' => 'vous pouvez ici indiquer une liste d\'arguments, par exemple : "arg=valeur&arg2=nouvelle_valeur", ils seront automatiquement ajoutés à l\'URL générée par la balise.', # NEW
	'cfgform_comment_patron' => 'predvolený e-mail vlastníka v klasickej verzii (<i>neformátovaný text</i>).',
	'cfgform_comment_patron_html' => 'si vous utilisez cette option, le mail envoyé comportera tout de même le premier squelette en version texte brut ; laissez le champ vide pour annuler cette option.', # NEW
	'cfgform_comment_reset' => 'vous pouvez ici définir l\'action du bouton "Annuler" du formulaire (<i>redéfinir cette action peut vous permettre de fermer la thickbox plutôt que la fenêtre par exemple</i>).', # NEW
	'cfgform_comment_squelette' => 'si vous avez créé un squelette personnel pour la boîte de dialogue du plugin (<i>sur le modèle du fichier "tip_a_friend.html"</i>) indiquez-le ici ; votre squelette devra obligatoirement inclure le formulaire "<b>tipafriend_form</b>".', # NEW
	'cfgform_comment_taf_css' => 'le plugin définit des styles CSS sur le modèle des styles de la distribution de SPIP ; ces styles sont inclus au formulaire par défaut mais vous pouvez ici choisir de ne pas les inclure.', # NEW
	'cfgform_info_balise' => 'La balise renvoie le lien ouvrant la page du formulaire d\'envoi. Vous pouvez changer l\'image affichée en éditant directement le squelette "<strong>modeles/tipafriend.html</strong>" du plugin.', # NEW
	'cfgform_info_patron_html' => '<strong>Si le plugin <a href="http://www.spip-contrib.net/?article3371"><strong>Facteur</strong></a> est installé et actif sur votre site</strong>, il est possible de construire une version HTML du mail envoyé.', # NEW
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
	'cfgform_titre_options_url' => 'Pridaný (-é) argument(y) k \'URL vytvoreného odkazu podľa tagu',
	'cfgform_titre_patron' => 'Šablóna odoslaného e-mailu',
	'cfgform_titre_patron_html' => 'Šablóna e-mailu vo formáte HTML',
	'cfgform_titre_reset' => 'Akcia tlačidla na zrušenie',
	'cfgform_titre_squelette' => 'Šablóna, ktorá sa použije na formulár tipafriendu',
	'cfgform_titre_taf_css' => 'Predvoliť pridávanie definícií CSS',

	// D
	'doc_chapo' => 'Zásuvný modul "Tip A Friend" ponúka kompletný formulár na odoslanie ({hocijakej}) stránky v SPIPe na viacero e-mailových adries.',
	'doc_en_ligne' => 'Dokumentácia zásuvného modulu na Spip-Contribe',
	'doc_titre_court' => 'Dokumentácia TipAFriendu',
	'doc_titre_page' => 'Dokumentácia zásuvného modulu "Tip A Friend"',
	'docskel_sep' => '----',
	'documentation' => '
Cette page vous permet de tester l\'utilisation du plugin en fonction de votre site, de votre configuration et de vos personnalisations. Les différents liens proposés ajoutent un objet SPIP ou incluent un modèle dans le corps de la page. Vous pouvez modifier ces inclusions en éditant le paramètre correspondant de l\'URL courante.

{{{La balise TIPAFRIEND}}}

{{Utilisation}}

Le plugin propose une balise qui construit un lien ouvrant la page d\'envoi du mail d\'information en fonction de l\'objet SPIP courant. Cette balise accepte un unique argument, optionnel, permettant de définir :
-* soit {{le squelette utilisé pour générer ce lien}}, il faut alors indiquer le nom du squelette en question ({sans l\'extension ".html"}) ; le squelette doit être présent dans votre répertoire de modèles ;
-* soit {{le type de lien présenté}} ; si vous indiquez l\'argument "{{mini}}", la balise renverra uniquement l\'image du lien, sans le texte "Envoyer cette page ...".

{{Exemple}}

<cadre class="spip">
// balise seule
#TIPAFRIEND
// pour ne voir que l\'image
#TIPAFRIEND{mini}
// ou avec un modele personnel
#TIPAFRIEND{mon_modele}
</cadre>

{{Tests}}

Les liens ci-dessous ajoutent un objet SPIP à la page courante, laissant apparaître le rendu de la balise TIPAFRIEND.
- [Ajouter l\'article 1->@url_article@] <small>(id_article=...)</small>
- [Ajouter la brève 2->@url_breve@] <small>(id_breve=...)</small>
- [Recalculer la page->@url_recalcul@]
- [Retour à la page vierge->@url_vierge@]

Pour modifier l\'argument de la balise dans cette page de tests, ajoutez l\'argument "{{arg=...}}" à l\'URL courante ({par exemple pour utiliser l\'argument "mini", cliquez dans la barre d\'adresse de votre navigateur et ajoutez à la fin de l\'adresse courante "&arg=mini"}).

{{{Les modèles}}}

Les liens ci-dessous vous permettent de tester les modèles utilisés en page web ({avec des valeurs fictives}) ou de les inclure à la page courante.
- [Inclure le modèle \'tipafriend_mail_default.html\'->@url_model@] <small>(model=...)</small>
- [Voir le modèle brut avec des données fictives->@url_model_brut@]
- [Voir le modèle HTML avec des données fictives->@url_model_html@] <small>(nécessite le plugin {{[Facteur->http://www.spip-contrib.net/?article3371]}})</small>

{{{Paramètres de CFG pour TIPAFRIEND}}}

Si le plugin {{[CFG : moteur de configuration->http://www.spip-contrib.net/?rubrique575]}} est actif sur votre site, le lien ci-dessous vous présente les valeurs de configuration enregistrées pour le plugin "Tip A Friend".

@cfg_param@', # NEW

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
	'form_intro' => 'Pour transmettre l\'adresse de cette page, indiquez les adresses e-mail de vos contacts, votre propre adresse e-mail ainsi que vote nom. Vous pouvez également si vous le souhaitez ajouter un commentaire qui sera inclus dans le corps du message.<br /><small>{{*}} {Aucune de ces informations ne sera conservée.}</small>', # NEW
	'form_message_label' => 'Môžete pridať text',
	'form_separe_virgule' => '<em>Môžete uviesť viac adries oddelených bodkočiarkou.</em>',
	'form_title' => 'Poslať stránku e-mailom',

	// I
	'info_doc' => 'Ak máte problémy so zobrazením tejto stránky, [kliknite sem.->@link@]',
	'info_doc_titre' => 'Poznámka o zobrazení tejto stránky',
	'info_skel_doc' => 'Cette page de documentation est conçue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du répertoire "squelettes-dist/"}). Si vous ne parvenez pas à visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de gérer son affichage :

-* [Mode "texte simple"->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode "squelette Zpip"->@mode_zpip@] ({squelette Z compatible})
-* [Mode "squelette SPIP"->@mode_spip@] ({compatible distribution})', # NEW

	// L
	'licence' => 'Copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] distribuovaný pod licenciou [GNU GPL v3.->http://www.opensource.org/licenses/gpl-3.0.html]',

	// M
	'mail_body_01' => '@nom_exped@ (contact : @mail_exped@) vous invite à consulter le document ci-dessous, tiré du site @nom_site@, susceptible de vous intéresser.', # NEW
	'mail_body_01_html' => '<strong>@nom_exped@</strong> (contact : <a href="mailto:@mail_exped@">@mail_exped@</a>) vous invite à consulter le document ci-dessous, tiré du site <strong>@nom_site@</strong>, susceptible de vous intéresser.', # NEW
	'mail_body_02' => '@nom_exped@ vložili ste túto správu:',
	'mail_body_02_html' => '@nom_exped@ vložili ste túto správu:',
	'mail_body_03' => 'Názov dokumentu: "@titre_document@"',
	'mail_body_03_html' => 'Názov dokumentu: "@titre_document@"',
	'mail_body_04' => 'Adresa tejto stránky na internete: @url_document@',
	'mail_body_04_html' => 'Adresa tejto stránky na internete: <a href="@url_document@">@url_document@</a>',
	'mail_body_05' => 'Obsah stránky (ako text):',
	'mail_body_05_html' => 'Obsah stránky:',
	'mail_body_extrait' => '(úryvok) ',
	'mail_titre_default' => 'Údaje o stránke @nom_site@',
	'message_envoye' => 'OK – Vaša správa bola odoslaná',
	'message_pas_envoye' => '!! - Votre message n\'a pas pu être envoyé pour une raison inconnue ... Veuillez nous en excuser et <a href="@self@" title="Recharger la page">réessayer</a>.', # NEW

	// N
	'new_window' => 'Nové okno',

	// P
	'page_test' => 'Testovacia stránka (lokálne)',
	'page_test_balise' => 'Zápis tagu TIPAFRIEND',
	'page_test_cfg_pas_installe' => 'Zdá sa, že zásuvný modul [CFG->http://www.spip-contrib.net/?rubrique575] nie je nainštalovaný.',
	'page_test_fin_simulation' => '-- Koniec pridávania na simuláciu',
	'page_test_in_new_window' => 'Testovacia stránka v novom okne',
	'page_test_menu_inclure' => 'Vložiť model "tipafriend_mail_default.html"',
	'page_test_models_comment' => 'Tieto odkazy vám umožňujú vyskúšať si modely, ktoré sa používajú na webovej stránke (<i>s fiktívnymi hodnotami</i>).',
	'page_test_test_model_brut' => 'Zobraziť textový model s fiktívnymi dátami',
	'page_test_test_model_html' => 'Zobraziť model HTML s fiktívnymi dátami',
	'page_test_title' => 'Test zásuvného modulu Tip A Friend',
	'page_test_titre_inclusion_model' => '-- Vloženie modelu "@model@" (<i>fiktívne hodnoty</i>)',
	'page_test_titre_inclusion_objet' => '-- Simulácia stránky @objet@ č. @id_objet@ (<i>nadpis + úvod</i>)',
	'popup_name' => 'Poslať informáciu e-mailom',

	// T
	'taftest_arguments_balise_dyn' => 'Parametre prijaté v dymanickom tagu',
	'taftest_arguments_balise_stat' => 'Argumenty získané v statickom tagu ',
	'taftest_chargement_patron' => 'nahrávanie vzoru "@patron@"',
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
	'taftest_patron_pas_trouve' => 'Vzor "@patron@" sa nenašiel!<br />Nahráva sa predvolený vzor.',
	'taftest_skel_pas_trouve' => 'Šablóna \'@skel@\' sa nenašla.<br />Použije sa predvolená šablóna.',
	'taftest_title' => 'OdporučiťPriateľovi LADENIE',
	'taftest_to' => '<b><i>Príjemcovia</i></b>',
	'tipafriend' => 'Odporučiť priateľovi'
);

?>

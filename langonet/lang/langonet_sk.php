<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/langonet?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_corriger' => 'Získať opravy',
	'bouton_generer' => 'Vytvoriť',
	'bouton_langonet' => 'LangOnet',
	'bouton_lister' => 'Zobraziť',
	'bouton_rechercher' => 'Vyhľadať',
	'bouton_verifier' => 'Potvrdiť',
	'bulle_afficher_fichier_lang' => 'Zobraziť jazykový súbor vytvorený @date@',
	'bulle_afficher_fichier_log' => 'Zobraziť protokol @date@',
	'bulle_corriger' => 'Stiahnuť opravený jazykový súbor',
	'bulle_telecharger_fichier_lang' => 'Stiahnuť jazykový súbor vytvorený @date@',
	'bulle_telecharger_fichier_log' => 'Stiahnuť protokol @date@',

	// E
	'entete_log_avertissement_nonmais' => 'UPOZORNENIE: položky nepatria tomuto modulu',
	'entete_log_avertissement_peutetre_definition' => 'UPOZORNENIE: položky zrejme nie sú definované',
	'entete_log_avertissement_peutetre_utilisation' => 'UPOZORNENIE: položky sa zrejme už nepoužívajú',
	'entete_log_date_creation' => 'Súbor vytvorený @log_date_jour@ o @log_date_heure@.',
	'entete_log_erreur_definition' => 'CHYBA: položky modulu nie sú definované',
	'entete_log_erreur_definition_nonmais' => 'CHYBA: položky iných modulov nie sú definované',
	'entete_log_erreur_fonction_l' => 'CHYBA: ak používate funkciu _L()',
	'entete_log_erreur_utilisation' => 'CHYBA: položky sa nepoužívajú',

	// I
	'info_arborescence_scannee' => 'Vyberte si priečinok, do ktorého sa stromová štruktúra naskenuje',
	'info_bloc_langues_generees' => 'Kliknite na odkaz nižšie a stiahnite si vytvorený jazykový súbor',
	'info_bloc_logs_definition' => 'Kliknite a stiahnite si najnovší súbor protokolu s overením chýbajúcich definícií jazykového súboru.',
	'info_bloc_logs_fonction_l' => 'Kliknite na odkaz a stiahnite si najnovší súbor protokolu s overením použití _L() v danej stromovej štruktúre.',
	'info_bloc_logs_utilisation' => 'Kliknite na odkaz a stiahnite si najnovší súbor protokolu s overením zastaraných definícií jazykového súboru.',
	'info_chemin_langue' => 'Priečinok, kam ste nainštalovali jazykový súbor (príklad: <em>plugins/rainette/lang/</em> alebo <em>ecrire/lang/</em>)',
	'info_fichier_liste' => 'Vyberte si jazykový súbor, ktorého položky chcete zobraziť, s pomedzi tých súborov, ktoré sa nachádzajú na stránke.',
	'info_fichier_verifie' => 'Vyberte si jazykový súbor na schválenie z tých, ktoré sú na stránke.',
	'info_generer' => 'Táto možnosť vám umožňuje vytvoriť z jazyka originálu jazykový súbor daného modulu pre cieľový jazyk. Ak už cieľový jazyk existuje, jeho obsah sa pri vytváraní nového súboru použije znova.',
	'info_langue' => 'Skratka jazyka (príklad: <em>fr</em>, <em>en</em>, <em>es</em>, <em>sk</em> atď.)',
	'info_lister' => 'Táto možnosť vám umožní zobraziť položky jazykového súboru v abecednom poradí ',
	'info_mode' => 'Zhoduje sa s reťazcom, ktorý bude vložený pri vytváraní novej položky cieľového jazyka.',
	'info_module' => 'Correspond au préfixe du fichier de langue hors abréviation de la langue (exemple : <em>rainette</em> pour le plugin de même nom, ou <em>ecrire</em> pour SPIP)', # NEW
	'info_pattern_item_cherche' => 'Zadajte reťazec, ktorý sa týka celej skratky položky jazyka alebo jej časti. Pri vyhľadávaní sa nikdy nezohľadňujú VEĽKÉ a malé písmená.',
	'info_pattern_texte_cherche' => 'Zadajte reťazec, ktorý sa týka celého prekladu francúzskeho reťazca alebo jeho časti. Pri vyhľadávaní sa nikdy nezohľadňujú VEĽKÉ a malé písmená.',
	'info_rechercher_item' => 'Cette option vous permet de chercher des items de langue dans tous les fichiers de langue présents sur le site. Par souci de performance, seuls les fichiers de langue française sont scannés.', # NEW
	'info_rechercher_texte' => 'Cette option vous permet de chercher des items de langue via leur traduction française dans les fichiers de langue de SPIP <em>ecrire_fr</em>, <em>public_fr</em> et <em>spip_fr</em>. Le but de cette recherche est de vérifier si un texte n\'existe pas déjà dans SPIP avant de le créer.', # NEW
	'info_table' => 'Vous pouvez consulter ci-dessous la liste alphabétique des items de langue du fichier «<em>@langue@</em>» (@total@). Chaque bloc affiche les items ayant la même initiale, le raccourci en gras et le texte affiché en regard. Survolez une initiale pour faire apparaître la liste correspondante.', # NEW
	'info_verifier' => 'Cette option vous permet, d\'une part,  de vérifier les fichiers de langue d\'un module donné sous deux angles complémentaires. Il est possible, soit de vérifier si des items de langue utilisés dans un groupe de fichiers (un plugin, par exemple) ne sont pas définis dans le fichier de langue idoine, soit que certains items de langue définis ne sont plus utilisés. <br />D\'autre part, il est possible de lister et de corriger toutes les utilisations de la fonction _L() dans les fichiers PHP d\'une arborescence donnée.', # NEW

	// L
	'label_arborescence_scannee' => 'Stromová štruktúra, ktorú treba naskenovať',
	'label_avertissement' => 'Oznamy',
	'label_chemin_langue' => 'Umiestnenie jazykového súboru',
	'label_correspondance' => 'Typ zhody',
	'label_correspondance_commence' => 'Začať od',
	'label_correspondance_contient' => 'Obsahuje',
	'label_correspondance_egal' => 'Je rovný',
	'label_erreur' => 'Chyby',
	'label_fichier_liste' => 'Jazykový súbor',
	'label_fichier_verifie' => 'Jazyk na schválenie',
	'label_langue_cible' => 'Cieľový jazyk',
	'label_langue_source' => 'Zdrojový jazyk',
	'label_mode' => 'Spôsob vytvárania nových položiek',
	'label_module' => 'Modul',
	'label_pattern' => 'Reťazec, ktorý treba vyhľadať',
	'label_verification' => 'Typ scvaľovania',
	'label_verification_definition' => 'Zistenie chýbajúcich definícií',
	'label_verification_fonction_l' => 'Zisťovanie, či sa používa funkcia _L()',
	'label_verification_utilisation' => 'Zistenie zastaraných definícií',
	'legende_resultats' => 'Výsledky schvaľovania',
	'legende_table' => 'Zoznam položiek vybraného jazykového súboru',
	'legende_trouves' => 'Zoznam nájdených položiek (@total@)',

	// M
	'message_nok_aucun_fichier_log' => 'Žiaden súbor protokolu nie je dostupný na stiahnutie',
	'message_nok_aucune_langue_generee' => 'Žiaden vytvorený jazykový súbor nie je dostupný na stiahnutie',
	'message_nok_champ_obligatoire' => 'Toto pole je povinné',
	'message_nok_ecriture_fichier' => 'Jazykový súbor "<em>@langue@</em>" modulu "<em>@module@</em>" sa nepodarilo vytvoriť, pretože pri jeho zápise nastala chyba!',
	'message_nok_fichier_langue' => 'La génération a échoué car le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» est introuvable dans le répertoire «<em>@dossier@</em>» !', # NEW
	'message_nok_fichier_log' => 'Súbor protokolu s výsledkami overovania sa nedá vytvoriť!',
	'message_nok_fichier_script' => 'Súbor skriptu s príkazmi  na nahradenie funkcií _L za _T sa nepodarilo vytvoriť!',
	'message_nok_item_trouve' => 'Podmienkam vyhľadávania nevyhovuje žiadna jazyková položka!',
	'message_ok_definis_incertains_0' => 'Žiadna položka jazyka sa nepoužíva v celom kontexte, napríklad _T(\'@module@:item_\'.$variable).',
	'message_ok_definis_incertains_1' => 'L\'item de langue ci-dessous est utilisé dans un contexte complexe et pourrait être non défini dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à le vérifier :', # NEW
	'message_ok_definis_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans un contexte complexe et pourraient être non définis dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_fichier_genere' => 'Jazykový súbor "<em>@langue@</em>" modulu "<em>@module@</em>" bol vytvorený správne.<br />Súbor môžete získať "<em>@fichier@</em>".',
	'message_ok_fichier_log' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats.', # NEW
	'message_ok_fichier_log_script' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats ainsi que le fichier des commandes de remplacement _L en _T, «<em>@script@</em>».', # NEW
	'message_ok_fonction_l_0' => 'V priečinku <em>@ou_fichier@</em> nebol zistený žiaden prípad použitia funkcie _L() v súboroch PHP.',
	'message_ok_fonction_l_1' => 'Keď sa v súboroch PHP priečinka <em>"@ou_fichier@"</em> zistí jedno použitie funkcie _L():',
	'message_ok_fonction_l_n' => 'V súboroch PHP v priečinku <em>@ou_fichier@</em> bol nájdený @nberr@ prípad použitia funkcie _L():',
	'message_ok_item_trouve' => 'Vyhľadávanie reťazca @pattern@ bolo úspešné.',
	'message_ok_item_trouve_commence_1' => 'Jazyková položka sa začína hľadaným reťazcom:',
	'message_ok_item_trouve_commence_n' => 'Na hľadaný reťazec sa začína @sous_total@ položiek:',
	'message_ok_item_trouve_contient_1' => 'Jazyková položka obsahuje hľadaný reťazec:',
	'message_ok_item_trouve_contient_n' => 'Les @sous_total@ položiek obsahuje celý hľadaný reťazec:',
	'message_ok_item_trouve_egal_1' => 'Jazyková položka presne zodpovedá vyhľadávanému reťazcu:',
	'message_ok_item_trouve_egal_n' => 'Hľadanému reťazcu zodpovedá @sous_total@ položiek:',
	'message_ok_non_definis_0' => 'Tous les items de langue du module «<em>@module@</em>» utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» sont bien définis dans le fichier de langue «<em>@langue@</em>».', # NEW
	'message_ok_non_definis_1' => 'L\'item de langue du module «<em>@module@</em>» affiché ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais n\'est pas défini dans le fichier de langue «<em>@langue@</em>» :', # NEW
	'message_ok_non_definis_n' => 'Les @nberr@ items de langue du module «<em>@module@</em>» affichés ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais ne sont pas définis dans le fichier de langue «<em>@langue@</em>» :', # NEW
	'message_ok_non_utilises_0' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>».', # NEW
	'message_ok_non_utilises_1' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_n' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_nonmais_definis_0' => 'Súbory priečinka "<em>@ou_fichier@</em>" nepoužívajú žiadnu jazykovú položku, ktorá je správne definovaná v inom module ako "<em>@module@</em>".',
	'message_ok_nonmais_definis_1' => 'L\'item de langue ci-dessous est utilisé correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais est défini dans un autre module que «<em>@module@</em>». Nous vous invitons à le vérifier :', # NEW
	'message_ok_nonmais_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais sont définis dans un autre module que «<em>@module@</em>». Nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_nonmaisnok_definis_0' => 'Súbory priečinka "<em>@ou_fichier@</em>" nepoužívajú nesprávne žiadnu jazykovú položku definované v inom module ako "<em>@module@</em>".',
	'message_ok_nonmaisnok_definis_1' => 'L\'item de langue ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme un item du module «<em>@module@</em>». Etant donné qu\'il n\'est pas défini dans son module de rattachement, nous vous invitons à le vérifier :', # NEW
	'message_ok_nonmaisnok_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme des items du module «<em>@module@</em>». Etant donné qu\'ils ne sont pas définis dans leur module de rattachement, nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_table_creee' => 'Tabuľka položiek jazykového súboru @langue@ bola úspešne vytvorená.',
	'message_ok_utilises_incertains_0' => 'V celom kontexte sa nepoužíva žiadna jazyková položka (napríklad:  _T(\'@module@:item_\'.$variable)).',
	'message_ok_utilises_incertains_1' => 'Jazyková položka sa možno používa v celom kontexte. Pozývame vás, aby ste to skontrolovali:',
	'message_ok_utilises_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont peut-être utilisés dans un contexte complexe. Nous vous invitons à les vérifier un par un :', # NEW

	// O
	'onglet_generer' => 'Vytvoriť jazyk',
	'onglet_lister' => 'Zobraziť jazyk',
	'onglet_rechercher' => 'Vyhľadať položku',
	'onglet_verifier' => 'Potvrdiť jazyk',
	'option_aucun_dossier' => 'nevybrali ste žiadnu stromovú štruktúru',
	'option_aucun_fichier' => 'nevybrali ste žiaden jazyk',
	'option_mode_index' => 'Položka zdrojového jazyka',
	'option_mode_new' => 'Iba tag &lt;NEW&gt;',
	'option_mode_new_index' => 'Pred položkou cieľového jazyka sa nachádza &lt;NEW&gt;',
	'option_mode_new_valeur' => 'Pred reťazcom v zdrojovom jazyku sa nachádza &lt;NEW&gt;',
	'option_mode_pas_item' => 'Nevytvoriť položku',
	'option_mode_valeur' => 'Reťazec v zdrojovom jazyku',
	'option_mode_vide' => 'Prázdny reťazec',

	// T
	'test' => 'TEST : Táto položka jazyka slúži na vyhľadávanie skratky a test zhodnosti.',
	'test_item_1_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_2_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_non_utilise_1' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().', # NEW
	'test_item_non_utilise_2' => 'TEST: Táto jazyková položka je dobre definovaná v jazykovom súbore (), ale nepoužíva sa v súboroch naskenovaného priečinka ().',
	'texte_item_defini_ou' => '<em>definované v:</em>',
	'texte_item_mal_defini' => '<em>ale nie je definovaná v správnom module:</em>',
	'texte_item_non_defini' => '<em>ale nikde nie je definovaná!</em>',
	'texte_item_utilise_ou' => '<em>používa sa v:</em>',
	'titre_bloc_langues_generees' => 'Jazykové súbory',
	'titre_bloc_logs_definition' => 'Chýbajúce definície',
	'titre_bloc_logs_fonction_l' => 'Použitia _L()',
	'titre_bloc_logs_utilisation' => 'Zastaralé definície',
	'titre_form_generer' => 'Vytvorenie jazykového súboru',
	'titre_form_lister' => 'Zobrazenie jazykových súborov',
	'titre_form_rechercher_item' => 'Vyhľadávanie skratiek v jazykových súboroch',
	'titre_form_rechercher_texte' => 'Vyhľadávanie v textoch jazykových súborov SPIPu',
	'titre_form_verifier' => 'Kontrola jazykových súborov',
	'titre_page' => 'LangOnet',
	'titre_page_navigateur' => 'LangOnet',

	// Z
	'z_test' => 'TEST: Táto položka jazyka slúži na vyhľadávanie skratiek a test obsahu.'
);

?>

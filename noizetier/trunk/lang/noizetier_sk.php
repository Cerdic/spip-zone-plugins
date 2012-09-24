<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/noizetier?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'apercu' => 'Aperçu', # NEW

	// B
	'bloc_sans_noisette' => 'Ce bloc ne contient pas de noisette.', # NEW

	// C
	'choisir_noisette' => 'Vyberte si oriešok, ktorý chcete pridať:',
	'compositions_non_installe' => '<b>Zásuvný modul Rozmiestnenia:</b> tento zásuvný modul nie je na vašej stránke nainštalovaný. Na prevádzku noiZetiera nie je potrebný. Keď je však aktivovaný, môžete definovať rozmiestnenia priamo do noiZetiera.',

	// D
	'description_bloc_contenu' => 'Hlavný text stránky.',
	'description_bloc_extra' => 'Ďalšie kontextové informácie pre ďalšiu stránku.',
	'description_bloc_navigation' => 'Informácie o navigácii na každej stránke.',
	'description_bloctexte' => 'Názov je nepovinný.Pri písaní textu môžete používať klávesové skratky SPIPu.',

	// E
	'editer_composition' => 'Upraviť toto rozloženie',
	'editer_composition_heritages' => 'Definovať dedičnosť',
	'editer_configurer_page' => 'Nastaviť oriešky tejto stránky',
	'editer_exporter_configuration' => 'Exportovať konfiguráciu',
	'editer_importer_configuration' => 'Nahrať konfig.',
	'editer_noizetier_explication' => 'Nastavte oriešky, aby ste ich mohli pridať na stránky svojho webu.', # MODIF
	'editer_noizetier_titre' => 'Riadiť oriešky',
	'editer_nouvelle_page' => 'Créer une nouvelle page / composition', # NEW
	'erreur_aucune_noisette_selectionnee' => 'Vous devez sélectionner une noisette !', # NEW
	'erreur_doit_choisir_noisette' => 'Musíte si vybrať oriešok.',
	'erreur_mise_a_jour' => 'Pri aktualizovaní databázy sa vyskytla chyba.',
	'explication_glisser_deposer' => 'Vous pouvez ajouter une noisette ou les réordonner par simple glisser/déposer.', # NEW
	'explication_heritages_composition' => 'Tu môžete nastaviť rozmiestnenia, ktoré budú používať objekty danej vetvy.',
	'explication_noizetier_css' => 'Oriešku môžete pridať hocijaké ďalšie triedy CSS',
	'explication_raccourcis_typo' => 'Môžete používať klávesové skratky SPIPu.',

	// F
	'formulaire_ajouter_noisette' => 'Pridať oriešok',
	'formulaire_composition' => 'Identifikátor rozloženia',
	'formulaire_composition_explication' => 'Zadajte jedinečné kľúčové slovo (malými písmenami, bez medzier, bez pomlčiek (-) a diakritiky), ktoré umožní jednoznačne označiť toto rozmiestnenie.<br />Napríklad: <i>mojeroz.</i>', # MODIF
	'formulaire_composition_mise_a_jour' => 'Rozmiestnenie aktualizované',
	'formulaire_configurer_bloc' => 'Nastaviť blok:',
	'formulaire_configurer_page' => 'Nastaviť stránku:',
	'formulaire_deplacer_bas' => 'Posunúť nadol',
	'formulaire_deplacer_haut' => 'Posunúť nahor',
	'formulaire_description' => 'Popis',
	'formulaire_description_explication' => 'Môžete využívať zvyčajné skrtky SPIPu, najmä tag &lt;multi&gt;.',
	'formulaire_erreur_format_identifiant' => 'V identifikátore môžu byť len malé písmená bez diakritiky, čísla a znak _ (podčiarkovník).',
	'formulaire_icon' => 'Ikona',
	'formulaire_icon_explication' => 'Môžete zadať relatívnu adresu umiestnenia ikony (napríklad: <i>images/objet-liste-contenus.png</i>). Ak si chcete pozrieť zoznam obrázkov nainštalovaných v najbežnejších priečinkoch, môžete <a href="../spip.php?page=icones_preview">navštíviť túto stránku.</a>', # MODIF
	'formulaire_identifiant_deja_pris' => 'Tento identifikátor sa už používa!',
	'formulaire_import_compos' => 'Nahrať rozloženia modulu noizetier',
	'formulaire_import_fusion' => 'Zlúčiť s aktuálnymi nastaveniami',
	'formulaire_import_remplacer' => 'Nahradiť aktuálne nastavenia',
	'formulaire_liste_compos_config' => 'Tento súbor s nastaveniami definuje tieto rozmiestnenia modulu noizetier:',
	'formulaire_liste_pages_config' => 'Tento súbor s nastaveniami definuje oriešky na týchto stránkach:',
	'formulaire_modifier_composition' => 'Upraviť toto rozloženie:',
	'formulaire_modifier_composition_heritages' => 'Upraviť závislosti tohto rozmiestnenia:', # MODIF
	'formulaire_modifier_noisette' => 'Upraviť tento oriešok',
	'formulaire_modifier_page' => 'Modifier cette page', # NEW
	'formulaire_noisette_sans_parametre' => 'Tento oriešok neponúka nastavenie.',
	'formulaire_nom' => 'Názov',
	'formulaire_nom_explication' => 'Môžete používať tag  &lt;multi&gt;.',
	'formulaire_nouvelle_composition' => 'Nové rozloženie',
	'formulaire_obligatoire' => 'Povinné polia',
	'formulaire_supprimer_noisette' => 'Odstrániť tento oriešok',
	'formulaire_supprimer_noisettes_page' => 'Odstrániť oriešky tejto stránky',
	'formulaire_supprimer_page' => 'Supprimer cette page', # NEW
	'formulaire_type' => 'Typ rozloženia', # MODIF
	'formulaire_type_explication' => 'Uveďte, ktorý objekt/ktorá stránka používa toto rozmiestnenie.', # MODIF
	'formulaire_type_import' => 'Typ nahrávania',
	'formulaire_type_import_explication' => 'Súbor s nastaveniami môžete zlúčiť so svojimi aktuálnymi nastaveniami (oriešky každej stránky budú pridané k orieškom, ktoré sú už definované) alebo ním môžete svoje nastavenia nahradiť.',

	// I
	'icone_introuvable' => 'Icône introuvable !', # NEW
	'ieconfig_ne_pas_importer' => 'Nenahrávať',
	'ieconfig_noizetier_export_explication' => 'Exportuje nastavenia orieškov a rozmiestnenia modulu noiZetier.',
	'ieconfig_noizetier_export_option' => 'Zaradené do exportu?',
	'ieconfig_non_installe' => '<b>Zásuvný modul Nahrávanie a export nastavení):</b> tento zásuvný modul nie je na vašej stránke nainštalovaný. Na spúšťanie noiZetiera nie je potrebný. Keď si ho však aktivujete, budete môcť exportovať a nahrávať nastavenia orieškov do noiZetiera.',
	'ieconfig_probleme_import_config' => 'Pri nahrávaní nastavení modulu noiZetier sa vyskytol problém.',
	'info_composition' => 'ROZLOŽENIE:',
	'info_page' => 'STRÁNKA:',
	'installation_tables' => 'Tabuľky zásuvného modulu noiZetier boli nainštalované.<br />',
	'item_titre_perso' => 'vlastný názov',

	// L
	'label_afficher_titre_noisette' => 'Zobraziť názvy orieškov?',
	'label_niveau_titre' => 'Úroveň nadpisu:',
	'label_noizetier_css' => 'Triedy CSS:',
	'label_texte' => 'Text:',
	'label_titre' => 'Názov:',
	'label_titre_noisette' => 'Názov orieška:',
	'label_titre_noisette_perso' => 'Vlastný názov:',
	'liste_icones' => 'Zoznam ikon',
	'liste_pages' => 'Liste des pages', # NEW

	// M
	'masquer' => 'Masquer', # NEW
	'mode_noisettes' => 'Éditer les noisettes', # NEW
	'modif_en_cours' => 'Modifications en cours', # NEW

	// N
	'ne_pas_definir_d_heritage' => 'Nedefinovať dedičnosť',
	'noisette_numero' => 'noisette numéro :', # NEW
	'noisettes_composition' => 'Oriešky, ktoré sa používa iba toto rozmiestnení <i>@composition@:</i>',
	'noisettes_disponibles' => 'Noisettes disponibles', # NEW
	'noisettes_page' => 'Špeciálne oriešky pre stránku <i>@type@</i>:',
	'noisettes_toutes_pages' => 'Oriešky spoločné pre všetky stránky:',
	'noizetier' => 'noiZetier',
	'nom_bloc_contenu' => 'Obsah',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navigácia',
	'nom_bloctexte' => 'Blok voľného textu',
	'non' => 'Nie',
	'notice_enregistrer_rang' => 'Ak chcete uložiť poradie orieškov, kliknite na tlačidlo Uložiť.',

	// O
	'operation_annulee' => 'Opération annulée.', # NEW
	'oui' => 'Áno',

	// P
	'page' => 'Stránka',
	'page_autonome' => 'Page autonome', # NEW
	'probleme_droits' => 'Vous n\'avez pas les droits nécessaires pour effectuer cette modification.', # NEW

	// Q
	'quitter_mode_noisettes' => 'Quitter l\'édition des noisettes', # NEW

	// R
	'retour' => 'Retour', # NEW

	// S
	'suggestions' => 'Suggestions', # NEW

	// W
	'warning_noisette_plus_disponible' => 'POZOR: tento oriešok nie je dostupný.',
	'warning_noisette_plus_disponible_details' => 'Šablóna tohto orieška (<i>@squelette@</i>) je nedostupná. Možno oriešok potrebuje zásuvný modul, ktorý ste deaktivovali alebo odinštalovali.'
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'confirmer_supprimer_entree' => 'Voulez-vous vraiment supprimer cette entrée ?', # NEW

	// D
	'description_menu_accueil' => 'Odkaz na úvodnú stránku webu.',
	'description_menu_articles_rubrique' => 'Zobraziť zoznam článkov v rubrike.',
	'description_menu_deconnecter' => 'Ak je návštevník online, pridať možnosť odpojenia.',
	'description_menu_espace_prive' => 'Odkaz umožňujúci prihlásenie na stránku, ak ste sa ešte neprihlásili a potom na vstup do súkromnej zóny, ak na to máte oprávnenie.',
	'description_menu_groupes_mots' => 'Automaticky vypíše skupiny kľúčových slov a články, ktoré sú k ním priradené. Podľa predvolených nastavení sa zobrazia skupiny kľúčových slov a kľúčové slová v rámci nich. Ak existuje súbor šablóny groupes_mots.html, použije sa odkaz na skupinu kľúčových slov.',
	'description_menu_lien' => 'Pridá samostatne zadaný odkaz, a to buď interný (relatívna adresa), alebo externý(http://...).',
	'description_menu_mapage' => 'Ak sa návštevníci prihlásia, pridať odkaz na ich autorskú stránku.',
	'description_menu_mots' => 'Automaticky zobrazí menu so zoznamom článkov prepojených s kľúčovým slovom.',
	'description_menu_objet' => 'Vytvorí odkaz na objekt SPIPu: článok, rubriku alebo iný. Podľa predvolených nastavení bude niesť názov objektu.',
	'description_menu_page_speciale' => 'Pridá odkaz na stránku šablóny pomocou adresy formulára <code>spip.php?page=name&param1=xx&param2=yyy...</code> Takéto stránky často využívajú zásuvné moduly.',
	'description_menu_page_speciale_zajax' => 'Pridať odkaz do bolu na stránke, na ktorú sa dá dostať progredníctvom adresy typu <code>spip.php?page=name&param1=xx&param2=yyy...</code> Na to treba šablónu typu Z a zásuvný modul <a href="http://www.spip-contrib.net/MediaBox">médiabox.</a>',
	'description_menu_rubriques' => 'Displays a list of sections and, if desired, the subsections to several levels. By default, all sections are shown from the site root, sorted by title (numerically then alphabetically).',
	'description_menu_rubriques_articles' => 'Zobrazí zoznam rubrík, v ktorom môžu byť podrubriky a články včlenené do rôznych úrovní. Podľa predvolených nastavení sa zobrazia všetky rubriky počnúc koreňovým adresárom stránky a budú zotriedené podľa názvu (najprv čísla, potom písmená). Články v danej rubrike budú vždy uvedené po jej podrubrikách.',
	'description_menu_secteurlangue' => 'This entry can be used by sites which have one language per sector. It displays a menu which lists the sections of the sector corresponding to the language of the page, and if desired the subsections to several levels. By default, all sections are shown from the site root, sorted by title (numerically then alphabetically).',
	'description_menu_texte_libre' => 'Jednoducho text, ktorý chcete', # MODIF

	// E
	'editer_menus_editer' => 'Upraviť toto menu',
	'editer_menus_explication' => 'Vytvorte a nastavte ponky menu pre svoju stránku.',
	'editer_menus_exporter' => 'Exportovať toto menu',
	'editer_menus_nouveau' => 'Vytvoriť nové menu',
	'editer_menus_titre' => 'Ponuky menu stránky',
	'entree_afficher_articles' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)', # NEW
	'entree_afficher_item_suite' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)', # NEW
	'entree_articles_max' => 'Si oui, afficher les articles seulement si la rubrique contient au maximum xx articles ? (mettre le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_articles_max_affiches' => 'Si oui, limiter le nombre d\'articles listés à xx maximum (suivis d\'un item "... Tous les articles" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_aucun' => 'Žiadny',
	'entree_bloc' => 'Blok Zpipu',
	'entree_choisir' => 'Vyberte typ položky, ktorú chcete pridať:',
	'entree_classe_parent' => 'Classe des liens des éléments parents. Cette classe sera rajoutée aux li>a ayant une suite ul/li. Par exemple, si vous saisissez "daddy", cela vous permet d\'utiliser le plugin menu deroulant 2 pour la mise en forme du menu.', # NEW
	'entree_connexion_objet' => 'Na zobrazenie objektu sa vyžaduje prihlásenie (vloženie "session") alebo odhlásenie (vloženie "nosession")',
	'entree_contenu' => 'Obsah',
	'entree_css' => 'CSS triedy tejto položky (objektu)',
	'entree_css_lien' => 'Triedy CSS pre odkazy',
	'entree_id_groupe' => 'Číslo skupiny kľúčových slov',
	'entree_id_mot' => 'Číslo kľúčového slova',
	'entree_id_objet' => 'Číslo',
	'entree_id_rubrique' => 'Číslo nadradenej rubriky',
	'entree_id_rubrique_ou_courante' => 'Numéro de la rubrique parente ou "courante" si la rubrique parente est la rubrique courante du contexte', # NEW
	'entree_id_rubriques_exclues' => 'Numéros des rubriques à exclure, séparés par des virgules', # NEW
	'entree_id_secteur_exclus' => 'Numéros des secteurs à exclure, séparés par des virgules', # NEW
	'entree_infini' => 'Do nekonečna',
	'entree_mapage' => 'Moja stránka',
	'entree_masquer_articles_uniques' => 'Si oui et si une rubrique contient un seul article, le masquer ? (mettre "oui" pour cela)', # NEW
	'entree_niveau' => 'Úroveň podrubrík',
	'entree_nombre_articles' => 'Maximálny počet článkov (predvolené 0)',
	'entree_page' => 'Názov stránky',
	'entree_parametres' => 'Zoznam parametrov',
	'entree_rubriques_max_affichees' => 'Si oui, limiter le nombre de rubriques listés à xx maximum (suivis d\'un item "... Toutes les rubriques" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum de rubriques, laissez vide pour afficher toutes les rubriques)', # NEW
	'entree_sousrub_cond' => 'Zobrazovať len podrubriky aktuálnej rubriky (zadajte "oui" (áno), v opačnom prípade nevypĺňajte)',
	'entree_sur_n_articles' => '@n@ zobrazených článkov',
	'entree_sur_n_mots' => '@n@ zobrazených kľúčových slov',
	'entree_sur_n_niveaux' => 'Na @n@ úrovni(ach)',
	'entree_titre' => 'Nadpis',
	'entree_titre_connecter' => 'Nadpis pre prístup do prihlasovacieho formulára',
	'entree_titre_prive' => 'Nadpis pre prístup do súkromnej zóny',
	'entree_traduction_articles_rubriques' => 'Dans la mesure du possible, afficher les articles de la rubrique dans la langue du contexte (mettre "trad" pour cela)', # NEW
	'entree_traduction_objet' => 'Pri článku vyberajte preklad v závislosti od kontextu (na to, aby se to dosiahli, vložte "trad")',
	'entree_tri_alpha' => 'Kritérium triedenia (abecedné)',
	'entree_tri_alpha_articles' => 'Critère de tri des articles (alphabétique). Si vous saisissez "date", le critère ajouté sera {par date} et les articles seront triés par date', # NEW
	'entree_tri_alpha_articles_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_alpha_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num' => 'Kritérium triedenia (číselné)',
	'entree_tri_num_articles' => 'Critère de tri des articles (numérique). Si vous saisissez "titre", le critère ajouté sera {par num titre} et les articles seront triés par numéro de titre', # NEW
	'entree_tri_num_articles_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_type_objet' => 'Typ objektu',
	'entree_url' => 'Internetová adresa',
	'entree_url_public' => 'Po prihlásení vypísať adresu',
	'erreur_aucun_type' => 'Žiaden typ položky sa nenašiel.',
	'erreur_autorisation' => 'Nemáte povolené upravovať menu.',
	'erreur_identifiant_deja' => 'Tento identifikátor už využíva iné menu.',
	'erreur_identifiant_forme' => 'Identifikátor musí obsahovať len písmená, čísla alebo podčiarkovníky.',
	'erreur_menu_inexistant' => 'Menu číslo @id@ neexistuje.',
	'erreur_mise_a_jour' => 'Počas aktualizácie databázy došlo k chybe.',
	'erreur_parametres' => 'V parametroch stránky je chyba',
	'erreur_type_menu' => 'Musíte si vybrať typ menu',
	'erreur_type_menu_inexistant' => 'Ce type de menu n\'est pas/plus disponible', # NEW

	// F
	'formulaire_ajouter_entree' => 'Pridať položku menu',
	'formulaire_ajouter_sous_menu' => 'Vytvoriť podmenu',
	'formulaire_css' => 'Triedy CSS',
	'formulaire_css_explication' => 'K svojmu menu môžete pridať ďalšie triedy CSS.',
	'formulaire_deplacer_bas' => 'Posunúť nadol',
	'formulaire_deplacer_haut' => 'Posunúť nahor',
	'formulaire_facultatif' => 'Nepovinné',
	'formulaire_identifiant' => 'Identifikátor',
	'formulaire_identifiant_explication' => 'Priraďte mu unikátne kľúčové slovo, ktoré vám umožní ľahko zavolať svoje menu.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Vyberte, ktoré menu chcete nahrať.',
	'formulaire_ieconfig_importer' => 'Nahrať',
	'formulaire_ieconfig_menu_meme_identifiant' => 'UPOZORNENIE: na vašej stránke je už menu s rovnakým názvom!',
	'formulaire_ieconfig_menus_a_exporter' => 'Menu na export:',
	'formulaire_ieconfig_ne_pas_importer' => 'Nenahrávať',
	'formulaire_ieconfig_remplacer' => 'Prepísať súčasné menu nahratým menu',
	'formulaire_ieconfig_renommer' => 'Premenovať toto menu pred nahrávaním',
	'formulaire_importer' => 'Nahrať menu',
	'formulaire_importer_explication' => 'Ak ste exportovali menu do súboru, môžete ho teraz nahrať.',
	'formulaire_modifier_entree' => 'Upraviť túto položku menu',
	'formulaire_modifier_menu' => 'Upraviť menu:',
	'formulaire_nouveau' => 'Nové menu',
	'formulaire_partie_construction' => 'Vytvorenie menu',
	'formulaire_partie_identification' => 'Identifikácia menu',
	'formulaire_supprimer_entree' => 'Odstrániť túto položku menu',
	'formulaire_supprimer_menu' => 'Odstrániť menu',
	'formulaire_supprimer_sous_menu' => 'Odstrániť toto podmenu',
	'formulaire_titre' => 'Nadpis',

	// I
	'info_afficher_articles' => 'Les articles seront inclus dans le menu.', # NEW
	'info_articles_max' => 'Seulement si la rubrique contient au plus @max@ articles', # NEW
	'info_articles_max_affiches' => 'Affichage limité à @max@ articles', # NEW
	'info_classe_parent' => 'Classe des éléments parents : ', # NEW
	'info_connexion_obligatoire' => 'Vyžaduje sa prihlásenie',
	'info_deconnexion_obligatoire' => 'Iba keď ste odhlásený',
	'info_masquer_articles_uniques' => 'Articles uniques masqués', # NEW
	'info_numero_menu' => 'MENU ČÍSLO:',
	'info_page_speciale' => 'Odkaz na stránku "@page@"',
	'info_page_speciale_zajax' => 'Modalbox pre stránku "@page@" bloku "@bloc@" ',
	'info_rubrique_courante' => 'Rubrique courante', # NEW
	'info_rubriques_exclues' => ' / sauf rubrique(s) @id_rubriques@', # NEW
	'info_rubriques_max_affichees' => 'Affichage limité à @max@ rubriques', # NEW
	'info_secteur_exclus' => ' / sauf secteur(s) @id_secteur@', # NEW
	'info_sousrub_cond' => 'Sú zobrazené iba podrubriky aktuálnej rubriky.',
	'info_tous_groupes_mots' => 'Všetky skupiny kľúčových slov',
	'info_traduction_recuperee' => 'Kontext určí vybraný preklad',
	'info_tri' => 'Triedenie:',
	'info_tri_alpha' => '(abecedné)',
	'info_tri_articles' => 'Tri des articles :', # NEW
	'info_tri_num' => '(číselné)',

	// N
	'noisette_description' => 'Vkladanie menu definované v zásuvnom module Menus.',
	'noisette_label_afficher_titre_menu' => 'Zobraziť názov menu?',
	'noisette_label_identifiant' => 'Menu, ktoré sa má zobraziť:',
	'noisette_nom_noisette' => 'Menu',
	'nom_menu_accueil' => 'Úvodná stránka',
	'nom_menu_articles_rubrique' => 'Články v rubrike',
	'nom_menu_deconnecter' => 'Odhlásiť sa',
	'nom_menu_espace_prive' => 'Prihlásenie/odkaz na súkromnú zónu',
	'nom_menu_groupes_mots' => 'Kľúčové slová a články skupiny kľúčových slov',
	'nom_menu_lien' => 'Samostatný odkaz',
	'nom_menu_mapage' => 'Moja stránka',
	'nom_menu_mots' => 'Články s kľúčovým slovom',
	'nom_menu_objet' => 'Článok, rubrika alebo iný objekt SPIPU',
	'nom_menu_page_speciale' => 'Odkaz na šablónu stránky',
	'nom_menu_page_speciale_zajax' => 'Blok na stránke v SPIPe',
	'nom_menu_rubriques' => 'Zoznam alebo strom rubrík',
	'nom_menu_rubriques_evenements' => 'Udalosti, ktoré sa týkajú rubriky',
	'nom_menu_secteurlangue' => 'Jazykové sektory',
	'nom_menu_texte_libre' => 'Potvrdený text',

	// T
	'tous_les_articles' => '... Tous les articles', # NEW
	'toutes_les_rubriques' => '... Toutes les rubriques' # NEW
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/menus?lang_cible=ca
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'confirmer_supprimer_entree' => 'Voulez-vous vraiment supprimer cette entrée ?', # NEW

	// D
	'description_menu_accueil' => 'Enllaç cap a la pàgina d\'inici del lloc.',
	'description_menu_articles_rubrique' => 'Mostra la llista d\'articles d\'una secció.',
	'description_menu_deconnecter' => 'Si el visitant està connectat, afegeix una entrada proposant-li la desconnexió.',
	'description_menu_espace_prive' => 'Enllaç que permet connectar-se al lloc, si encara no ho estem, i després anar a l\'espai privat sempre i quan estiguem autoritzats a fer-ho.',
	'description_menu_groupes_mots' => 'Mostra automàticament un menú que llista les paraules del grup i els articles lligats. Per defecte, mostra la llista dels grups de paraules i les paraules lligades. Si un esquelet grups_paraules.html existeix, s\'utilitzarà l\'enllaç cap al grup.',
	'description_menu_lien' => 'Afegeix un enllaç arbitrari, intern (URL relativa) o extern (http://...).',
	'description_menu_mapage' => 'Si el visitant està connectat, afegeix un enllaç cap a la pàgina de l\'autor.',
	'description_menu_mots' => 'Mostra automàticament un menú que llista els articles lligats a la paraula clau.',
	'description_menu_objet' => 'Crea un enllaç cap a un objecte d\'SPIP: article, secció o un altre. Per defecte, l\'entrada tindrà el títol de l\'objecte.',
	'description_menu_page_speciale' => 'Afegeix un enllaç cap a un esquelet accessible per un URL del tipus <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Aquestes pàgines sovint són subministrades per connectors.',
	'description_menu_page_speciale_zajax' => 'Ajoute un lien vers un bloc d\'une page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ceci nécéssite une squelette de type Z et le plugin <a href="http://www.spip-contrib.net/MediaBox">médiabox</a>.', # NEW
	'description_menu_rubriques_articles' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques et les articles sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement). Les articles sont placés systématiquement après les rubriques.', # NEW
	'description_menu_rubriques_completes' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement).', # NEW
	'description_menu_secteurlangue' => 'Aquesta entrada és específica pels llocs que utilitzen un sector per llengua. Mostra automàticament un menú que llista les seccions del sector corresponent a la llengua de la pàgina i, si es vol, les subseccions a diferents nivells. Per defecte, mostra totes les seccions des de l\'arrel, ordenades per títol (numèricament i després alfabèticament).',
	'description_menu_texte_libre' => 'Simplement le texte que vous souhaitez, ou un code de langue SPIP (<:...:>)', # NEW

	// E
	'editer_menus_editer' => 'Modificar aquest menú',
	'editer_menus_explication' => 'Creeu i configureu els menús del vostre lloc.',
	'editer_menus_exporter' => 'Exportar aquest menú',
	'editer_menus_nouveau' => 'Creeu un nou menú',
	'editer_menus_titre' => 'Menús del lloc',
	'entree_afficher_articles' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)', # NEW
	'entree_afficher_item_suite' => 'Inclure les articles dans le menu ? (mettre "oui" pour cela)', # NEW
	'entree_articles_max' => 'Si oui, afficher les articles seulement si la rubrique contient au maximum xx articles ? (mettre le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_articles_max_affiches' => 'Si oui, limiter le nombre d\'articles listés à xx maximum (suivis d\'un item "... Tous les articles" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum d\'articles, laissez vide pour afficher tous les articles)', # NEW
	'entree_aucun' => 'Cap',
	'entree_bloc' => 'Bloc Zpip', # NEW
	'entree_choisir' => 'Escolliu el tipus d\'entrada que voleu afegir:',
	'entree_classe_parent' => 'Classe des liens des éléments parents. Cette classe sera rajoutée aux li>a ayant une suite ul/li. Par exemple, si vous saisissez "daddy", cela vous permet d\'utiliser le plugin menu deroulant 2 pour la mise en forme du menu.', # NEW
	'entree_connexion_objet' => 'Obliger à être connecté (mettre "session") ou déconnecté (mettre "nosession") pour voir l\'objet', # NEW
	'entree_contenu' => 'Contenu', # NEW
	'entree_css' => 'Classes CSS de l\'entrada', # MODIF
	'entree_css_lien' => 'Classes CSS du lien', # NEW
	'entree_id_groupe' => 'Número del grup de paraula clau',
	'entree_id_mot' => 'Número de la paraula clau',
	'entree_id_objet' => 'Número',
	'entree_id_rubrique' => 'Número de la secció pare',
	'entree_id_rubrique_ou_courante' => 'Numéro de la rubrique parente ou "courante" si la rubrique parente est la rubrique courante du contexte', # NEW
	'entree_id_rubriques_exclues' => 'Numéros des rubriques à exclure, séparés par des virgules', # NEW
	'entree_id_secteur_exclus' => 'Numéros des secteurs à exclure, séparés par des virgules', # NEW
	'entree_infini' => 'Fins l\'infinit',
	'entree_mapage' => 'La meva pàgina personal',
	'entree_masquer_articles_uniques' => 'Si oui et si une rubrique contient un seul article, le masquer ? (mettre "oui" pour cela)', # NEW
	'entree_niveau' => 'Nivell de les subseccions',
	'entree_nombre_articles' => 'Número d\'articles com a màxim (0 per defecte)',
	'entree_page' => 'Nom de la pàgina',
	'entree_parametres' => 'Llista dels paràmetres',
	'entree_rubriques_max_affichees' => 'Si oui, limiter le nombre de rubriques listés à xx maximum (suivis d\'un item "... Toutes les rubriques" comportant un lien vers la rubrique parente) ? (indiquer le nombre maximum de rubriques, laissez vide pour afficher toutes les rubriques)', # NEW
	'entree_sousrub_cond' => 'N\'afficher que les sous-rubriques de la rubrique en cours (mettre "oui", sinon laisser vide)', # NEW
	'entree_suivant_connexion' => 'Restreindre cette entrée suivant la connexion (mettre "connecte" pour afficher seulement si le visiteur est connecté, "deconnecte" pour le cas contraire, ou laisser vide pour toujours afficher)', # NEW
	'entree_suivant_connexion_connecte' => 'seulement si connecté', # NEW
	'entree_suivant_connexion_deconnecte' => 'seulement si déconnecté', # NEW
	'entree_sur_n_articles' => '@n@ articles mostrat(s)',
	'entree_sur_n_mots' => '@n@ paraules mostrada(es)',
	'entree_sur_n_niveaux' => 'A @n@ nivell(s)',
	'entree_titre' => 'Títol',
	'entree_titre_connecter' => 'Títol per l\'accés al formulari d\'identificació',
	'entree_titre_prive' => 'Títol per accedir a l\'espai privat',
	'entree_traduction_articles_rubriques' => 'Dans la mesure du possible, afficher les articles de la rubrique dans la langue du contexte (mettre "trad" pour cela)', # NEW
	'entree_traduction_objet' => 'Dans le cas d\'un article, choisir la traduction en fonction du contexte (mettre "trad" pour cela)', # NEW
	'entree_tri_alpha' => 'Criteri d\'ordenació (alfabètic)', # MODIF
	'entree_tri_alpha_articles' => 'Critère de tri des articles (alphabétique). Si vous saisissez "date", le critère ajouté sera {par date} et les articles seront triés par date', # NEW
	'entree_tri_alpha_articles_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_alpha_inverse' => 'Inverser le critère de tri alphabétique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num' => 'Criteri d\'ordenació (numèric)', # MODIF
	'entree_tri_num_articles' => 'Critère de tri des articles (numérique). Si vous saisissez "titre", le critère ajouté sera {par num titre} et les articles seront triés par numéro de titre', # NEW
	'entree_tri_num_articles_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_tri_num_inverse' => 'Inverser le critère de tri numérique ? (mettre "oui" pour cela)', # NEW
	'entree_type_objet' => 'Tipus d\'objecte',
	'entree_url' => 'Adreça',
	'entree_url_public' => 'Adresse de retour après la connexion', # NEW
	'erreur_aucun_type' => 'No s\'ha trobat cap entrada.',
	'erreur_autorisation' => 'No estàs autoritzat per modificar els menús.',
	'erreur_identifiant_deja' => 'Aquest identificador ja es utilitzat per un menú.',
	'erreur_identifiant_forme' => 'L\'identificador només pot contenir lletres, xifres o el caràcter subratllat.',
	'erreur_menu_inexistant' => 'El menú demanat número @id@ no existeix.',
	'erreur_mise_a_jour' => 'S\'ha produït un error durant l\'actualització de la base de dades .',
	'erreur_parametres' => 'Hi ha un error en els paràmetres de la pàgina',
	'erreur_type_menu' => 'Has d\'escollir un tipus de menú',
	'erreur_type_menu_inexistant' => 'Ce type de menu n\'est pas/plus disponible', # NEW

	// F
	'formulaire_ajouter_entree' => 'Afegir una entrada',
	'formulaire_ajouter_sous_menu' => 'Crear un submenú',
	'formulaire_css' => 'Classes CSS',
	'formulaire_css_explication' => 'Podeu afegir al menú eventuals classes CSS suplementàries.',
	'formulaire_deplacer_bas' => 'Desplaçar avall',
	'formulaire_deplacer_haut' => 'Desplaçar amunt',
	'formulaire_facultatif' => 'Facultatiu',
	'formulaire_identifiant' => 'Identificador',
	'formulaire_identifiant_explication' => 'Doneu una paraula clau única que us permetrà cridar fàcilment el vostre menú.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Choisissez quel(s) menu(s) vous souhaitez importer.', # NEW
	'formulaire_ieconfig_importer' => 'Importer', # NEW
	'formulaire_ieconfig_menu_meme_identifiant' => 'ATTENTION : un menu avec le même identifiant existe déjà sur votre votre site !', # NEW
	'formulaire_ieconfig_menus_a_exporter' => 'Menus à exporter :', # NEW
	'formulaire_ieconfig_ne_pas_importer' => 'Ne pas importer', # NEW
	'formulaire_ieconfig_remplacer' => 'Remplacer le menu actuel par le menu importé', # NEW
	'formulaire_ieconfig_renommer' => 'Renommer ce menu avant import', # NEW
	'formulaire_importer' => 'Importar un menú',
	'formulaire_importer_explication' => 'Si heu exportar un menú a dins d\'un fitxer, ara el podreu importar.',
	'formulaire_modifier_entree' => 'Modificar aquesta entrada',
	'formulaire_modifier_menu' => 'Modificar el menú:',
	'formulaire_nouveau' => 'Nou menú',
	'formulaire_partie_construction' => 'Construcció del menú',
	'formulaire_partie_identification' => 'Identificació del menú',
	'formulaire_supprimer_entree' => 'Suprimir aquesta entrada',
	'formulaire_supprimer_menu' => 'Suprimir el menú',
	'formulaire_supprimer_sous_menu' => 'Suprimir aquest submenú',
	'formulaire_titre' => 'Títol',

	// I
	'info_afficher_articles' => 'Les articles seront inclus dans le menu.', # NEW
	'info_articles_max' => 'Seulement si la rubrique contient au plus @max@ articles', # NEW
	'info_articles_max_affiches' => 'Affichage limité à @max@ articles', # NEW
	'info_classe_parent' => 'Classe des éléments parents : ', # NEW
	'info_connexion_obligatoire' => 'Connexion obligatoire', # NEW
	'info_deconnexion_obligatoire' => 'Uniquement déconnecté', # NEW
	'info_masquer_articles_uniques' => 'Articles uniques masqués', # NEW
	'info_numero_menu' => 'MENÚ NÚMERO:',
	'info_page_speciale' => 'Enllaç cap a la pàgina «@page@»',
	'info_page_speciale_zajax' => 'Modalbox de la page « @page@ » pour le bloc « @bloc@ &#187', # NEW
	'info_rubrique_courante' => 'Rubrique courante', # NEW
	'info_rubriques_exclues' => ' / sauf rubrique(s) @id_rubriques@', # NEW
	'info_rubriques_max_affichees' => 'Affichage limité à @max@ rubriques', # NEW
	'info_secteur_exclus' => ' / sauf secteur(s) @id_secteur@', # NEW
	'info_sousrub_cond' => 'Seules les sous-rubriques de la rubriques en cours sont affichées.', # NEW
	'info_tous_groupes_mots' => 'Tots els grups de paraules',
	'info_traduction_recuperee' => 'Le contexte décidera de la traduction choisie', # NEW
	'info_tri' => 'Ordena:', # MODIF
	'info_tri_alpha' => '(alfabètica)',
	'info_tri_articles' => 'Tri des articles :', # NEW
	'info_tri_num' => '(numèrica)',

	// N
	'noisette_description' => 'Insère un menu défini avec le plugin Menus.', # NEW
	'noisette_label_afficher_titre_menu' => 'Afficher le titre du menu ?', # NEW
	'noisette_label_identifiant' => 'Menu à afficher :', # NEW
	'noisette_nom_noisette' => 'Menu', # NEW
	'nom_menu_accueil' => 'Inici',
	'nom_menu_articles_rubrique' => 'Articles d\'una secció',
	'nom_menu_deconnecter' => 'Desconnectar-se',
	'nom_menu_espace_prive' => 'Connectar-se / enllaç cap a l\'espai privat',
	'nom_menu_groupes_mots' => 'Paraules clau i Articles d\'un Grup de paraules',
	'nom_menu_lien' => 'Enllaç arbitrari',
	'nom_menu_mapage' => 'La meva pàgina',
	'nom_menu_mots' => 'Articles d\'una Paraula clau',
	'nom_menu_objet' => 'Article, secció o un altre objecte SPIP',
	'nom_menu_page_speciale' => 'Enllaç cap a una pàgina esquelet',
	'nom_menu_page_speciale_zajax' => 'Un bloc d\'une page Zpip', # NEW
	'nom_menu_rubriques_completes' => 'Liste ou arborescence de rubriques et d\'articles (avec beaucoup d\'options)', # NEW
	'nom_menu_rubriques_evenements' => 'Esdeveniments de les seccions',
	'nom_menu_secteurlangue' => 'Sectors de llengua',
	'nom_menu_texte_libre' => 'Texte libre', # NEW

	// T
	'tous_les_articles' => '... Tous les articles', # NEW
	'toutes_les_rubriques' => '... Toutes les rubriques' # NEW
);

?>

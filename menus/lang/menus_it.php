<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'description_menu_accueil' => 'Link verso il pannello di controllo del sito.',
	'description_menu_articles_rubrique' => 'Mostra la lista degli articoli di una rubrica.',
	'description_menu_deconnecter' => 'Se il visitatore è connesso, aggiungi un link per proporgli la disconnessione.',
	'description_menu_espace_prive' => 'Link che permette di connettersi al sito se non lo si è già, e poi di andare in redazione se si è autorizzati.',
	'description_menu_groupes_mots' => 'Mostra automaticamente un menu che elenca le parole chiave di un gruppo e gli articoli collegati. Normalmente, mostra la lista dei gruppi di parole chiave e le parole collegate. Se un modello groupes_mots.html esiste, il link verso il gruppo verrà utilizzato',
	'description_menu_lien' => 'Aggiunge un link arbitrario, interno (URL relativo) o esterno (http://...).',
	'description_menu_mapage' => 'Se il visitatore è connesso, aggiunge un link verso la sua pagina autore.',
	'description_menu_mots' => 'Mostra automaticamente un menù che elenca gli articoli collegati alla parola chiave.',
	'description_menu_objet' => 'Crea un link verso un oggetto di SPIP: articolo, rubrica o altro. In maniera predefinita, il link avrà il titolo dell\'oggetto.',
	'description_menu_page_speciale' => 'Aggiunge un link verso un modello accessibile con un url del tipo <code>spip.php?page=nome&amp;param1=xx&amp;param2=yyy...</code> Queste pagina sono spesso fornite dai plugin.',
	'description_menu_page_speciale_zajax' => 'Ajoute un lien vers un bloc d\'une page accessible par une url du type <code>spip.php?page=nom&param1=xx&param2=yyy...</code> Ceci nécéssite une squelette de type Z et le plugin <a href="http://www.spip-contrib.net/MediaBox">médiabox</a>.', # NEW
	'description_menu_rubriques' => 'Mostra una lista di rubriche e, se si vuole, le sotto rubriche su più livelli. Normalmente, mostra tutte le rubriche dalla radice, ordinate per titolo (numericamente e poi alfabeticamente).',
	'description_menu_rubriques_articles' => 'Affiche une liste de rubriques et, si on veut, les sous-rubriques et les articles sur plusieurs niveaux. Par défaut, affiche toutes les rubriques depuis la racine, triées par titre (numériquement puis alphabétiquement). Les articles sont placés systématiquement après les rubriques.', # NEW
	'description_menu_secteurlangue' => 'Questa voce è specifica per i siti che utilizzano un settore per lingua. Mostra automaticamente un menù che elenca le rubriche del settore corrispondente alla lingua della pagina e, se si vuole, le sotto rubriche su più livelli. Normalmente, mostra tutte le rubriche dalla radice, ordinate per titolo (numericamente e poi alfabeticamente).',
	'description_menu_texte_libre' => 'Simplement le texte que vous souhaitez', # NEW

	// E
	'editer_menus_editer' => 'Modifica questo menù',
	'editer_menus_explication' => 'Crea e configura qui i menù del tuo sito',
	'editer_menus_exporter' => 'Esporta questo menù',
	'editer_menus_nouveau' => 'Crea un nuovo menù',
	'editer_menus_titre' => 'Menù del sito',
	'entree_aucun' => 'Nessun',
	'entree_bloc' => 'Bloc Zpip', # NEW
	'entree_choisir' => 'Scegli il tipo di voce che vuoi aggiungere:',
	'entree_connexion_objet' => 'Obliger à être connecté (mettre "session") ou déconnecté (mettre "nosession") pour voir l\'objet', # NEW
	'entree_contenu' => 'Contenu', # NEW
	'entree_css' => 'Classi CSS della voce', # MODIF
	'entree_css_lien' => 'Classes CSS du lien', # NEW
	'entree_id_groupe' => 'Numero di gruppo della parola chiave',
	'entree_id_mot' => 'Numero della parola chiave',
	'entree_id_objet' => 'Numero',
	'entree_id_rubrique' => 'Numero della rubrica padre',
	'entree_infini' => 'All\'infinito',
	'entree_mapage' => 'La mia pagina personale',
	'entree_niveau' => 'Livelli di sotto rubriche',
	'entree_nombre_articles' => 'Numero massimo di articoli (0 predefinito)',
	'entree_page' => 'Nome della pagina',
	'entree_parametres' => 'Lista dei parametri',
	'entree_sousrub_cond' => 'N\'afficher que les sous-rubriques de la rubrique en cours (mettre "oui", sinon laisser vide)', # NEW
	'entree_sur_n_articles' => '@n@ articoli mostrati',
	'entree_sur_n_mots' => '@n@ parole chiave mostrate',
	'entree_sur_n_niveaux' => 'Su @n@ livelli',
	'entree_titre' => 'Titolo',
	'entree_titre_connecter' => 'Titolo per l\'accesso al form di login',
	'entree_titre_prive' => 'Titolo per accedere alla redazione',
	'entree_traduction_objet' => 'Dans le cas d\'un article, choisir la traduction en fonction du contexte (mettre "trad" pour cela)', # NEW
	'entree_tri_alpha' => 'Criterio di ordinamento (alfabetico)',
	'entree_tri_num' => 'Criterio di ordinamento (numerico)',
	'entree_type_objet' => 'Tipo di oggetto',
	'entree_url' => 'Indirizzo',
	'entree_url_public' => 'Adresse de retour après la connexion', # NEW
	'erreur_aucun_type' => 'Nessun tipo di voce trovata.',
	'erreur_autorisation' => 'Non sei autorizzato a modificare i menù.',
	'erreur_identifiant_deja' => 'Questo identificativo è già utilizzato da un menù.',
	'erreur_identifiant_forme' => 'L\'identificativo deve contenere solo lettere, cifre o il trattino basso.',
	'erreur_menu_inexistant' => 'Il menù richiesto numero @id@ non esiste.',
	'erreur_mise_a_jour' => 'Si è verificato un errore durante l\'aggiornamento del database.',
	'erreur_parametres' => 'C\'è un errore nei parametri della pagina',
	'erreur_type_menu' => 'Devi scegliere un tipo di menù',

	// F
	'formulaire_ajouter_entree' => 'Aggiungi una voce',
	'formulaire_ajouter_sous_menu' => 'Crea un sotto menù',
	'formulaire_css' => 'Classi CSS',
	'formulaire_css_explication' => 'Puoi aggiungere al menù delle eventuali classi CSS supplmentari.',
	'formulaire_deplacer_bas' => 'Sposta verso il basso',
	'formulaire_deplacer_haut' => 'Sposta verso l\'alto',
	'formulaire_facultatif' => 'Facoltativo',
	'formulaire_identifiant' => 'Identificativo',
	'formulaire_identifiant_explication' => 'Inserisci una parola chiave unica che ti permetterà di richiamare il tuo menù facilmente.',
	'formulaire_ieconfig_choisir_menus_a_importer' => 'Choisissez quel(s) menu(s) vous souhaitez importer.', # NEW
	'formulaire_ieconfig_importer' => 'Importer', # NEW
	'formulaire_ieconfig_menu_meme_identifiant' => 'ATTENTION : un menu avec le même identifiant existe déjà sur votre votre site !', # NEW
	'formulaire_ieconfig_menus_a_exporter' => 'Menus à exporter :', # NEW
	'formulaire_ieconfig_ne_pas_importer' => 'Ne pas importer', # NEW
	'formulaire_ieconfig_remplacer' => 'Remplacer le menu actuel par le menu importé', # NEW
	'formulaire_ieconfig_renommer' => 'Renommer ce menu avant import', # NEW
	'formulaire_importer' => 'Importa un menù',
	'formulaire_importer_explication' => 'Se hai esportato un menù in un file, ora lo puoi importare.',
	'formulaire_modifier_entree' => 'Modifica questa voce',
	'formulaire_modifier_menu' => 'Modifica il menù:',
	'formulaire_nouveau' => 'Nuovo menù',
	'formulaire_partie_construction' => 'Costruzione del menù',
	'formulaire_partie_identification' => 'Identificativo del menù',
	'formulaire_supprimer_entree' => 'Elimina questa voce',
	'formulaire_supprimer_menu' => 'Elimina il menù',
	'formulaire_supprimer_sous_menu' => 'Elimina il sotto menù',
	'formulaire_titre' => 'Titolo',

	// I
	'info_connexion_obligatoire' => 'Connexion obligatoire', # NEW
	'info_deconnexion_obligatoire' => 'Uniquement déconnecté', # NEW
	'info_numero_menu' => 'MENU NUMERO:',
	'info_page_speciale' => 'Link verso la pagina "@page@"',
	'info_page_speciale_zajax' => 'Modalbox de la page « @page@ » pour le bloc « @bloc@ &#187', # NEW
	'info_sousrub_cond' => 'Seules les sous-rubriques de la rubriques en cours sont affichées.', # NEW
	'info_tous_groupes_mots' => 'Tutti i gruppi di parole chiave',
	'info_traduction_recuperee' => 'Le contexte décidera de la traduction choisie', # NEW
	'info_tri' => 'Ordina:',
	'info_tri_alpha' => '(alfabetico)',
	'info_tri_num' => '(numerico)',

	// N
	'noisette_description' => 'Insère un menu défini avec le plugin Menus.', # NEW
	'noisette_label_afficher_titre_menu' => 'Afficher le titre du menu ?', # NEW
	'noisette_label_identifiant' => 'Menu à afficher :', # NEW
	'noisette_nom_noisette' => 'Menu', # NEW
	'nom_menu_accueil' => 'Pannello di controllo',
	'nom_menu_articles_rubrique' => 'Articoli di una rubrica',
	'nom_menu_deconnecter' => 'Disconnettersi',
	'nom_menu_espace_prive' => 'Connettersi / link alla redazione',
	'nom_menu_groupes_mots' => 'Parole chiave e articoli di un gruppo di parole chiave',
	'nom_menu_lien' => 'Link arbitrario',
	'nom_menu_mapage' => 'La mia pagina',
	'nom_menu_mots' => 'Articoli di un a parola chiave',
	'nom_menu_objet' => 'Articolo, rubrica o altro oggetto SPIP',
	'nom_menu_page_speciale' => 'Link verso una pagina di modello',
	'nom_menu_page_speciale_zajax' => 'Un bloc d\'une page Zpip', # NEW
	'nom_menu_rubriques' => 'Lista o gerarchia di rubriche',
	'nom_menu_rubriques_articles' => 'Liste ou arborescence de rubriques et d\'articles', # NEW
	'nom_menu_rubriques_evenements' => 'Eventi delle rubriche',
	'nom_menu_secteurlangue' => 'Settore di lingua',
	'nom_menu_texte_libre' => 'Texte libre' # NEW
);

?>

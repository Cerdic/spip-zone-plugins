<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/menus?lang_cible=it
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
	'description_menu_objet' => 'Crea un link verso un oggetto di SPIP: articolo, rubrica o altro. In maniera predefinita, il link avrà il titolo dell’oggetto.',
	'description_menu_page_speciale' => 'Aggiunge un link verso un modello accessibile con un url del tipo <code>spip.php?page=nome&param1=xx&param2=yyy...</code> Queste pagina sono spesso fornite dai plugin.',
	'description_menu_secteurlangue' => 'Questa voce è specifica per i siti che utilizzano un settore per lingua. Mostra automaticamente un menù che elenca le rubriche del settore corrispondente alla lingua della pagina e, se si vuole, le sotto rubriche su più livelli. Normalmente, mostra tutte le rubriche dalla radice, ordinate per titolo (numericamente e poi alfabeticamente).',

	// E
	'editer_menus_editer' => 'Modifica questo menù',
	'editer_menus_explication' => 'Crea e configura qui i menù del tuo sito',
	'editer_menus_exporter' => 'Esporta questo menù',
	'editer_menus_nouveau' => 'Crea un nuovo menù',
	'editer_menus_titre' => 'Menù del sito',
	'entree_aucun' => 'Nessun',
	'entree_choisir' => 'Scegli il tipo di voce che vuoi aggiungere:',
	'entree_css' => 'Classi CSS della voce', # MODIF
	'entree_id_groupe' => 'Numero di gruppo della parola chiave',
	'entree_id_mot' => 'Numero della parola chiave',
	'entree_id_objet' => 'Numero',
	'entree_id_rubrique' => 'Numero della rubrica padre',
	'entree_infini' => 'All’infinito',
	'entree_mapage' => 'La mia pagina personale',
	'entree_niveau' => 'Livelli di sotto rubriche',
	'entree_nombre_articles' => 'Numero massimo di articoli (0 predefinito)',
	'entree_page' => 'Nome della pagina',
	'entree_parametres' => 'Lista dei parametri',
	'entree_sur_n_articles' => '@n@ articoli mostrati',
	'entree_sur_n_mots' => '@n@ parole chiave mostrate',
	'entree_sur_n_niveaux' => 'Su @n@ livelli',
	'entree_titre' => 'Titolo',
	'entree_titre_connecter' => 'Titolo per l’accesso al form di login',
	'entree_titre_prive' => 'Titolo per accedere alla redazione',
	'entree_type_objet' => 'Tipo di oggetto',
	'entree_url' => 'Indirizzo',
	'erreur_aucun_type' => 'Nessun tipo di voce trovata.',
	'erreur_autorisation' => 'Non sei autorizzato a modificare i menù.',
	'erreur_identifiant_deja' => 'Questo identificativo è già utilizzato da un menù.',
	'erreur_identifiant_forme' => 'L’identificativo deve contenere solo lettere, cifre o il trattino basso.',
	'erreur_menu_inexistant' => 'Il menù richiesto numero @id@ non esiste.',
	'erreur_mise_a_jour' => 'Si è verificato un errore durante l’aggiornamento del database.',
	'erreur_parametres' => 'C’è un errore nei parametri della pagina',
	'erreur_type_menu' => 'Devi scegliere un tipo di menù',

	// F
	'formulaire_ajouter_entree' => 'Aggiungi una voce',
	'formulaire_ajouter_sous_menu' => 'Crea un sotto menù',
	'formulaire_css' => 'Classi CSS',
	'formulaire_css_explication' => 'Puoi aggiungere al menù delle eventuali classi CSS supplmentari.',
	'formulaire_deplacer_bas' => 'Sposta verso il basso',
	'formulaire_deplacer_haut' => 'Sposta verso l’alto',
	'formulaire_facultatif' => 'Facoltativo',
	'formulaire_identifiant' => 'Identificativo',
	'formulaire_identifiant_explication' => 'Inserisci una parola chiave unica che ti permetterà di richiamare il tuo menù facilmente.',
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
	'info_numero_menu' => 'MENU NUMERO:',
	'info_page_speciale' => 'Link verso la pagina "@page@"',
	'info_tous_groupes_mots' => 'Tutti i gruppi di parole chiave',
	'info_tri' => 'Ordina:', # MODIF
	'info_tri_alpha' => '(alfabetico)',
	'info_tri_num' => '(numerico)',

	// N
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
	'nom_menu_rubriques_evenements' => 'Eventi delle rubriche',
	'nom_menu_secteurlangue' => 'Settore di lingua'
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fulltext?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Met accenten wordt geen rekening gehouden ("déjà" of "deja" geven beiden o.a. de volgende resultaten: "déjà", "dejà", "déja"...).',
	'activer_indexation' => 'Activer l\'indexation des fichiers @ext@', # NEW
	'asie' => 'Azië',
	'asterisque_terminale' => 'levert geen resultaten op: het sterretje moet het laaste teken zijn', # MODIF
	'aussi' => 'ook',

	// C
	'casse_indifferente' => 'Hoofdletters of kleine letters in woorden hebben geen effect.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents', # NEW
	'configurer_egalement_doc' => 'Vous pouvez également configurer l\'indexation des documents :', # MODIF
	'convertir_myisam' => 'Converteer naar MyISAM',
	'convertir_toutes' => 'Converteer alle tekstConverteer alle tekstvelden naar MyISAM',
	'convertir_utf8' => 'converteer naar UTF-8 om de coherentie te herstellen',
	'creer_tous' => 'Maak alle voorgestelde FULLTEXT indexen aan',

	// D
	'des_utilisations' => '@nb@ utilisations', # NEW
	'descriptif' => 'Descriptif', # NEW
	'documents_proteges' => 'Documents protégés', # MODIF

	// E
	'enfan' => 'kind',
	'enfance' => 'kindertijd',
	'enfant' => 'kind',
	'enfanter' => 'kinderlijk',
	'enfantillage' => 'kindsheid',
	'enfants' => 'kinderen',
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.', # NEW
	'erreur_doc_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .doc', # MODIF
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.', # MODIF
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.', # MODIF
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .pdf', # MODIF
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .ppt', # MODIF
	'erreur_taille_index' => 'Il faut au moins indexer un caractère.', # MODIF
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.', # NEW
	'erreur_xls_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .xls', # MODIF
	'et' => 'en',
	'etranger' => 'vreemdeling',
	'exemples' => 'Voorbeelden',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.', # NEW

	// F
	'fant' => 'ind',
	'fonctionnement_recherche' => 'Toelichting bij het zoeken op deze site ',
	'fulltext_cree' => 'FULLTEXT aangemaakt',
	'fulltext_creer' => 'Créer l\'index @index@', # NEW
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :', # NEW
	'fulltext_documents' => 'Fulltext - Documents', # NEW
	'fulltext_index' => 'Fulltext - Index', # NEW

	// G
	'general' => 'Général', # NEW

	// I
	'id' => 'ID', # NEW
	'il_faut_myisam' => 'MyISAM is verplicht',
	'incoherence_charset' => 'Jouw site en de site van de database komen niet geheel overeen. Dit kan tot verkeerde resultaten leiden bij zoekopdrachten die letters met accenten bevatten:',
	'index_regenere' => 'tekstveld opnieuw aangamaakt',
	'index_reinitialise' => 'De documenten met een foutmelding zijn opnieuw geïnitialiseerd',
	'index_reinitialise_ptg' => 'Les documents protégés ont tous été réinitialisés', # NEW
	'index_reinitialise_totalement' => 'Les document ont tous été réinitialisés', # NEW
	'index_supprime' => 'index verwijderd',
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant l\'indexation des', # NEW
	'indiquer_options_bin' => 'Indiquer les options pour l\'indexation des', # NEW
	'infos' => 'Informations', # NEW
	'infos_documents_proteges' => 'Vous trouverez ici la liste des documents protégés et donc non-indexés par Fulltext', # NEW
	'infos_fulltext_document' => 'Vous pourrez ici choisir quels type de documents sont indexés par Fulltext et configurer les binaires utilisés et leurs options.', # NEW
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).', # NEW

	// L
	'liste_tables_connues' => 'Hier volgt een lijst van tekstvelden die beschikbaar zijn voor zoekopdrachten. Je kunt meer FULLTEXT elementen toevoegen -- zie de  toelichting bij',
	'logo' => 'Logo', # NEW

	// M
	'mais_pas' => 'maar NIET',
	'message_ok_configuration' => 'Enregistrement de vos préférences terminée', # MODIF
	'message_ok_update_configuration' => 'Mise à jour de vos préférences terminée', # MODIF

	// N
	'nb_err' => 'En erreur d\'indexation', # NEW
	'nb_index' => 'Indexés', # MODIF
	'nb_non_index' => 'Non-indexés', # MODIF
	'nb_ptg' => 'Protégés (non-indexés)', # MODIF
	'necessite_version_php' => 'Nécessite PHP 5.2 au minimum, ainsi que l\'option -enable-zip.', # MODIF
	'nombre_caracteres' => 'Nombre de caractères indexés (depuis le debut du document).', # MODIF
	'nombre_documents' => 'Nombre de documents à traiter par itération du CRON', # MODIF

	// O
	'ou_bien' => 'of ook',

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.', # NEW
	'pas_index' => 'Geen FULLTEXT index',
	'premier_soit' => 'OFWEL',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options proposées ici ab initio ne sont que des exemples.', # MODIF

	// R
	'regenerer_tous' => 'Maak alle FULLTEXT indexen opnieuw aan',
	'reinitialise_index_doc' => 'Initialiseer de documenten die een foutmelding geven opnieuw',
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés', # NEW
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents', # NEW
	'reserve_webmestres' => 'Pagina voorbehouden aan webmasters',
	'retour_configuration_fulltext' => 'Retour à la configuration de Fulltext', # MODIF
	'retourne' => 'Levert tekst op die het volgende bevat',

	// S
	'sequence_exacte' => 'de exacte zin',
	'soit' => 'OF',
	'statistiques_indexation' => 'Statistiques d\'indexation des documents :', # NEW
	'supprimer' => 'Verwijderen',

	// T
	'table_convertie' => 'tekstveld geconverteerd naar MyISAM',
	'table_format' => 'Het formaat van dit tekstveld is',
	'table_non_reconnue' => 'onherkenbaar tekstveld',
	'textes_premier' => 'maar toont eerst tekst die het volgende bevat',
	'titre_page_fulltext_index' => 'Configuration des index de recherche', # NEW

	// U
	'une_utilisation' => '1 utilisation', # NEW
	'utiliser_operateurs_logiques' => 'De zoekfunctie werkt volgens logische standaarden.',

	// V
	'voir_doc_ptg' => 'Voir les documents protegés' # NEW
);

?>

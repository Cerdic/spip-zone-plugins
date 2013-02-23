<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fulltext?lang_cible=fr_tu
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Les accents ne sont pas pris en compte (« déjà » ou « deja », retourneront à l\'identique « déjà », « dejà », « déja »...)',
	'activer_indexation' => 'Activer l\'indexation des fichiers @ext@', # NEW
	'asie' => 'asie',
	'asterisque_terminale' => 'ne retournera rien: l\'astérisque * doit être terminale', # MODIF
	'aussi' => 'aussi',

	// C
	'casse_indifferente' => 'La casse (minuscule/majuscule) des mots recherchés est indifférente.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents', # NEW
	'configurer_egalement_doc' => 'Vous pouvez également configurer l\'indexation des documents :', # MODIF
	'convertir_myisam' => 'Convertir en MyISAM',
	'convertir_toutes' => 'Convertir toutes les tables en MyISAM',
	'convertir_utf8' => 'convertir en UTF-8 pour restaurer la cohérence',
	'creer_tous' => 'Créer tous les index FULLTEXT suggérés',

	// D
	'des_utilisations' => '@nb@ utilisations', # NEW
	'descriptif' => 'Descriptif', # NEW
	'documents_proteges' => 'Documents protégés', # MODIF

	// E
	'enfan' => 'enfan',
	'enfance' => 'enfance',
	'enfant' => 'enfant',
	'enfanter' => 'enfanter',
	'enfantillage' => 'enfantillage',
	'enfants' => 'enfants',
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.', # NEW
	'erreur_doc_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .doc', # MODIF
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.', # MODIF
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.', # MODIF
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .pdf', # MODIF
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .ppt', # MODIF
	'erreur_taille_index' => 'Il faut au moins indexer un caractère.', # MODIF
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.', # NEW
	'erreur_xls_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .xls', # MODIF
	'et' => 'ET',
	'etranger' => 'étranger',
	'exemples' => 'Exemples d\'utilisation',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.', # NEW

	// F
	'fant' => 'fant',
	'fonctionnement_recherche' => 'Fonctionnement du moteur de recherche de ce site',
	'fulltext_cree' => 'FULLTEXT créé',
	'fulltext_creer' => 'Créer l\'index @index@', # NEW
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :', # NEW
	'fulltext_documents' => 'Fulltext - Documents', # NEW
	'fulltext_index' => 'Fulltext - Index', # NEW

	// G
	'general' => 'Général', # NEW

	// I
	'id' => 'ID', # NEW
	'il_faut_myisam' => 'il faut MyISAM',
	'incoherence_charset' => 'Une incohérence entre le charset de ton site et celui des tables de ta base de données risque de fausser les recherches avec caractères accentués :',
	'index_regenere' => 'index de la table régénérés',
	'index_reinitialise' => 'Les documents en erreur ont été réinitialisés',
	'index_reinitialise_ptg' => 'Les documents protégés ont tous été réinitialisés', # NEW
	'index_reinitialise_totalement' => 'Les document ont tous été réinitialisés', # NEW
	'index_supprime' => 'index supprimé',
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant l\'indexation des', # NEW
	'indiquer_options_bin' => 'Indiquer les options pour l\'indexation des', # NEW
	'infos' => 'Informations', # NEW
	'infos_documents_proteges' => 'Vous trouverez ici la liste des documents protégés et donc non-indexés par Fulltext', # NEW
	'infos_fulltext_document' => 'Vous pourrez ici choisir quels type de documents sont indexés par Fulltext et configurer les binaires utilisés et leurs options.', # NEW
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).', # NEW

	// L
	'liste_tables_connues' => 'Voici la liste des tables connues de la recherche. Tu peux y ajouter des éléments FULLTEXT, cf. documentation à l\'adresse',
	'logo' => 'Logo', # NEW

	// M
	'mais_pas' => 'mais PAS',
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
	'ou_bien' => 'ou bien',

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.', # NEW
	'pas_index' => 'Pas d\'index FULLTEXT',
	'premier_soit' => 'SOIT',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options proposées ici ab initio ne sont que des exemples.', # MODIF

	// R
	'regenerer_tous' => 'Régénérer tous les index FULLTEXT',
	'reinitialise_index_doc' => 'Réinitialiser l\'indexation des documents en erreur',
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés', # NEW
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents', # NEW
	'reserve_webmestres' => 'Page réservée aux webmestres',
	'retour_configuration_fulltext' => 'Retour à la configuration de Fulltext', # MODIF
	'retourne' => 'Retourne les textes qui contiennent',

	// S
	'sequence_exacte' => 'exactement la séquence de mots',
	'soit' => 'SOIT',
	'statistiques_indexation' => 'Statistiques d\'indexation des documents :', # NEW
	'supprimer' => 'Supprimer',

	// T
	'table_convertie' => 'table convertie en MyISAM',
	'table_format' => 'Cette table est au format',
	'table_non_reconnue' => 'table non reconnue',
	'textes_premier' => 'mais présente en premier les textes qui contiennent',
	'titre_page_fulltext_index' => 'Configuration des index de recherche', # NEW

	// U
	'une_utilisation' => '1 utilisation', # NEW
	'utiliser_operateurs_logiques' => 'La recherche utilise les opérateurs logiques les plus courants.',

	// V
	'voir_doc_ptg' => 'Voir les documents protegés' # NEW
);

?>

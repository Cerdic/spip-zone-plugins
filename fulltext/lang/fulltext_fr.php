<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/fulltext/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Les accents ne sont pas pris en compte (« déjà » ou « deja », retourneront à l\'identique « déjà », « dejà », « déja »...)',
	'activer_indexation' => 'Activer l\'indexation des fichiers @ext@',
	'asie' => 'asie',
	'asterisque_terminale' => 'ne retournera rien : l\'astérisque * doit être terminale',
	'aussi' => 'aussi',

	// C
	'casse_indifferente' => 'La casse (minuscule/majuscule) des mots recherchés est indifférente.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents',
	'configurer_egalement_doc' => 'Vous pouvez également configurer l\'indexation des documents :',
	'convertir_myisam' => 'Convertir en MyISAM',
	'convertir_toutes' => 'Convertir toutes les tables en MyISAM',
	'convertir_utf8' => 'convertir en UTF-8 pour restaurer la cohérence',
	'creer_tous' => 'Créer tous les index FULLTEXT suggérés',

	// D
	'des_utilisations' => '@nb@ utilisations',
	'descriptif' => 'Descriptif',
	'documents_proteges' => 'Documents protégés',

	// E
	'enfan' => 'enfan',
	'enfance' => 'enfance',
	'enfant' => 'enfant',
	'enfanter' => 'enfanter',
	'enfantillage' => 'enfantillage',
	'enfants' => 'enfants',
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.',
	'erreur_doc_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .doc',
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.',
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.',
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .pdf',
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .ppt',
	'erreur_taille_index' => 'Il faut au moins indexer un caractère.',
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.',
	'erreur_xls_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .xls',
	'et' => 'ET',
	'etranger' => 'étranger',
	'exemples' => 'Exemples d\'utilisation',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.',

	// F
	'fant' => 'fant',
	'fonctionnement_recherche' => 'Fonctionnement du moteur de recherche de ce site',
	'fulltext_cree' => 'FULLTEXT créé',
	'fulltext_creer' => 'Créer l\'index @index@',
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :',
	'fulltext_documents' => 'Fulltext - Documents',
	'fulltext_index' => 'Fulltext - Index',

	// G
	'general' => 'Général',

	// I
	'id' => 'ID',
	'il_faut_myisam' => 'il faut MyISAM',
	'incoherence_charset' => 'Une incohérence entre le charset de votre site et celui des tables de votre base de données risque de fausser les recherches avec caractères accentués:',
	'index_regenere' => 'index de la table régénérés',
	'index_reinitialise' => 'Les documents en erreur ont été réinitialisés',
	'index_reinitialise_ptg' => 'Les documents protégés ont tous été réinitialisés',
	'index_reinitialise_totalement' => 'Les document ont tous été réinitialisés',
	'index_supprime' => 'index supprimé',
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant l\'indexation des',
	'indiquer_options_bin' => 'Indiquer les options pour l\'indexation des',
	'infos' => 'Informations',
	'infos_documents_proteges' => 'Vous trouverez ici la liste des documents protégés et donc non-indexés par Fulltext',
	'infos_fulltext_document' => 'Vous pourrez ici choisir quels type de documents sont indexés par Fulltext et configurer les binaires utilisés et leurs options.',
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).',

	// L
	'liste_tables_connues' => 'Voici la liste des tables connues de la recherche. Vous pouvez y ajouter des éléments FULLTEXT, cf. documentation à l\'adresse',
	'logo' => 'Logo',

	// M
	'mais_pas' => 'mais PAS',
	'message_ok_configuration' => 'Enregistrement de vos préférences terminée',
	'message_ok_update_configuration' => 'Mise à jour de vos préférences terminée',

	// N
	'nb_err' => 'En erreur d\'indexation',
	'nb_index' => 'Indexés',
	'nb_non_index' => 'Non-indexés',
	'nb_ptg' => 'Protégés (non-indexés)',
	'necessite_version_php' => 'Nécessite PHP 5.2 au minimum, ainsi que l\'option -enable-zip.',
	'nombre_caracteres' => 'Nombre de caractères indexés (depuis le debut du document).',
	'nombre_documents' => 'Nombre de documents à traiter par itération du CRON',

	// O
	'ou_bien' => 'ou bien',

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.',
	'pas_index' => 'Pas d\'index FULLTEXT',
	'premier_soit' => 'SOIT',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options proposées ici ab initio ne sont que des exemples.',

	// R
	'regenerer_tous' => 'Régénérer tous les index FULLTEXT',
	'reinitialise_index_doc' => 'Réinitialiser l\'indexation des documents en erreur',
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés',
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents',
	'reserve_webmestres' => 'Page réservée aux webmestres',
	'retour_configuration_fulltext' => 'Retour à la configuration de Fulltext',
	'retourne' => 'Retourne les textes qui contiennent',

	// S
	'sequence_exacte' => 'exactement la séquence de mots',
	'soit' => 'SOIT',
	'statistiques_indexation' => 'Statistiques d\'indexation des documents :',
	'supprimer' => 'Supprimer',

	// T
	'table_convertie' => 'table convertie en MyISAM',
	'table_format' => 'Cette table est au format',
	'table_non_reconnue' => 'table non reconnue',
	'textes_premier' => 'mais présente en premier les textes qui contiennent',
	'titre_page_fulltext_index' => 'Configuration des index de recherche',

	// U
	'une_utilisation' => '1 utilisation',
	'utiliser_operateurs_logiques' => 'La recherche utilise les opérateurs logiques les plus courants.',

	// V
	'voir_doc_ptg' => 'Voir les documents protégés'
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/fulltext/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Les accents ne sont pas pris en compte (« déjà » ou « deja », retourneront à l\'identique « déjà », « dejà », « déja »...)',
	'activer_indexation' => 'Activer l\'indexation des',
	'asie' => 'asie',
	'asterisque_terminale' => 'ne retournera rien: l\'astérisque * doit être terminale',
	'aussi' => 'aussi',

	// C
	'casse_indifferente' => 'La casse (minuscule/majuscule) des mots recherchés est indifférente.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents',
	'configurer_egalement_doc' => 'Vous pouvez &eacute;galement configurer l\'indexation des documents :',
	'convertir_myisam' => 'Convertir en MyISAM',
	'convertir_toutes' => 'Convertir toutes les tables en MyISAM',
	'convertir_utf8' => 'convertir en UTF-8 pour restaurer la cohérence',
	'creer_tous' => 'Créer tous les index FULLTEXT suggérés',
	
	// D
	'des_utilisations' => '@nb@ utilisations',
	'descriptif' => 'Descriptif',
	'documents_proteges' => 'Documents prot&eacute;g&eacute;s',
	
	// E
	'enfan' => 'enfan',
	'enfance' => 'enfance',
	'enfant' => 'enfant',
	'enfanter' => 'enfanter',
	'enfantillage' => 'enfantillage',
	'enfants' => 'enfants',
	'erreur_doc_bin' => 'Vous devez renseigner le binaire &agrave; utiliser pour extraire les .doc',
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle sup&eacute;rieur &agrave; une seconde.',
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents &agrave; traiter par it&eacute;ration sup&eacute;rieur &agrave; un.',
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire &agrave; utiliser pour extraire les .pdf',
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire &agrave; utiliser pour extraire les .ppt',
	'erreur_taille_index' => 'Il faut au moins indexer un caract&egrave;re.',
	'erreur_xls_bin' => 'Vous devez renseigner le binaire &agrave; utiliser pour extraire les .xls',
	'et' => 'ET',
	'etranger' => 'étranger',
	'exemples' => 'Exemples d\'utilisation',

	// F
	'fant' => 'fant',
	'fonctionnement_recherche' => 'Fonctionnement du moteur de recherche de ce site',
	'fulltext_cree' => 'FULLTEXT créé',
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :',

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
	'message_ok_configuration' => 'Enregistrement de vos pr&eacute;f&eacute;rences termin&eacute;e',
	'message_ok_update_configuration' => 'Mise &agrave; jour de vos pr&eacute;f&eacute;rences termin&eacute;e',

	// N
	'nb_err' => 'En erreur d\'indexation', 
	'nb_index' => 'Index&eacute;s',
	'nb_non_index' => 'Non-index&eacute;s',
	'nb_ptg' => 'Prot&eacute;g&eacute;s (non-index&eacute;s)', 
	'necessite_version_php' => '(n&eacute;cessite PHP 5.2 au minimum, ainsi que l\'option -enable-zip)',
	'nombre_caracteres' => 'Nombre de caract&egrave;res index&eacute;s (depuis le debut du document).',
	'nombre_documents' => 'Nombre de documents &agrave; traiter par it&eacute;ration du CRON',

	// O
	'ou_bien' => 'ou bien',

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.',
	'pas_index' => 'Pas d\'index FULLTEXT',
	'premier_soit' => 'SOIT',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options propos&eacute;es ici ab initio ne sont que des exemples.',

	// R
	'regenerer_tous' => 'Régénérer tous les index FULLTEXT',
	'reinitialise_index_doc' => 'Réinitialiser l\'indexation des documents en erreur',
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés',
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents',
	'reserve_webmestres' => 'Page réservée aux webmestres',
	'retour_configuration_fulltext' => 'Retour &agrave; la configuration de Fulltext',
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

	// U
	'une_utilisation' => '1 utilisation',
	'utiliser_operateurs_logiques' => 'La recherche utilise les opérateurs logiques les plus courants.',
	
	// V
	'voir_doc_ptg' => 'Voir les documents protegés'
	
);

?>

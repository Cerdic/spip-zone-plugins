<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fulltext?lang_cible=et
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Aksendid tähtedel ei ole arvestatud ("ülestõusmine" või "ulestousmine", annavad sama tulemuse, ka "ülestousmine", "ulestõusmine"...)',
	'activer_indexation' => 'Activer l\'indexation des fichiers @ext@', # NEW
	'asie' => 'aasia',
	'asterisque_terminale' => 'ei anna tulemust,tärn peab olema lõpus', # MODIF
	'aussi' => 'ka',

	// C
	'casse_indifferente' => 'Tähtede suurus sõnades ei mõjuta otsingut.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents', # NEW
	'configurer_egalement_doc' => 'Vous pouvez également configurer l\'indexation des documents :', # MODIF
	'convertir_myisam' => 'Convert to MyISAM',
	'convertir_toutes' => 'Convert all tables to MyISAM',
	'convertir_utf8' => 'convert to UTF-8 to restore coherency',
	'creer_tous' => 'Create all the suggested FULLTEXT indexes',

	// D
	'des_utilisations' => '@nb@ utilisations', # NEW
	'descriptif' => 'Descriptif', # NEW
	'documents_proteges' => 'Documents protégés', # MODIF

	// E
	'enfan' => 'mad',
	'enfance' => 'madal',
	'enfant' => 'madu',
	'enfanter' => 'madrats',
	'enfantillage' => 'madrigal',
	'enfants' => 'madratsid',
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.', # NEW
	'erreur_doc_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .doc', # MODIF
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.', # MODIF
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.', # MODIF
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .pdf', # MODIF
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .ppt', # MODIF
	'erreur_taille_index' => 'Il faut au moins indexer un caractère.', # MODIF
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.', # NEW
	'erreur_xls_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .xls', # MODIF
	'et' => 'JA',
	'etranger' => 'riided',
	'exemples' => 'Näited kasutamise kohta',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.', # NEW

	// F
	'fant' => 'rats',
	'fonctionnement_recherche' => 'Kuidas töötab otsing sellel lehel',
	'fulltext_cree' => 'FULLTEXT created',
	'fulltext_creer' => 'Créer l\'index @index@', # NEW
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :', # NEW
	'fulltext_documents' => 'Fulltext - Documents', # NEW
	'fulltext_index' => 'Fulltext - Index', # NEW

	// G
	'general' => 'Général', # NEW

	// I
	'id' => 'ID', # NEW
	'il_faut_myisam' => 'MyISAM is required',
	'incoherence_charset' => 'Sinu saidi ja andmebaasi karakterite komplektide vahel on vastukäivus, mis võib tulemusena otsingutele anda halbu vastuseid, kui kasutad aksentide, tähemärkidega tähti:',
	'index_regenere' => 'table index regenerated',
	'index_reinitialise' => 'The documents showing an error have been reinitialised',
	'index_reinitialise_ptg' => 'Les documents protégés ont tous été réinitialisés', # NEW
	'index_reinitialise_totalement' => 'Les document ont tous été réinitialisés', # NEW
	'index_supprime' => 'index deleted',
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant l\'indexation des', # NEW
	'indiquer_options_bin' => 'Indiquer les options pour l\'indexation des', # NEW
	'infos' => 'Informations', # NEW
	'infos_documents_proteges' => 'Vous trouverez ici la liste des documents protégés et donc non-indexés par Fulltext', # NEW
	'infos_fulltext_document' => 'Vous pourrez ici choisir quels type de documents sont indexés par Fulltext et configurer les binaires utilisés et leurs options.', # NEW
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).', # NEW

	// L
	'liste_tables_connues' => 'Here is the list of tables taken into account for searches. You can add more FULLTEXT elements -- see the documentation at',
	'logo' => 'Logo', # NEW

	// M
	'mais_pas' => 'kuid MITTE',
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
	'ou_bien' => 'või muidu',

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.', # NEW
	'pas_index' => 'No FULLTEXT index',
	'premier_soit' => 'KAS',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options proposées ici ab initio ne sont que des exemples.', # MODIF

	// R
	'regenerer_tous' => 'Regenerate all FULLTEXT indexes',
	'reinitialise_index_doc' => 'Reinitialise the indexation of documents showing an error',
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés', # NEW
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents', # NEW
	'reserve_webmestres' => 'Ainult weebivaldaja võib seda lehekülge kasutada',
	'retour_configuration_fulltext' => 'Retour à la configuration de Fulltext', # MODIF
	'retourne' => 'Annab vastuseks tekstid, milles leidub',

	// S
	'sequence_exacte' => 'täpne fraas',
	'soit' => 'VÕI',
	'statistiques_indexation' => 'Statistiques d\'indexation des documents :', # NEW
	'supprimer' => 'Kustuta',

	// T
	'table_convertie' => 'table converted to MyISAM',
	'table_format' => 'This table\'s format is',
	'table_non_reconnue' => 'unrecognised table',
	'textes_premier' => 'kuid näitab esmalt teksti, milles leidub',
	'titre_page_fulltext_index' => 'Configuration des index de recherche', # NEW

	// U
	'une_utilisation' => '1 utilisation', # NEW
	'utiliser_operateurs_logiques' => 'Otsing kasutab standartseid loogilisi tähemärke.',

	// V
	'voir_doc_ptg' => 'Voir les documents protegés' # NEW
);

?>

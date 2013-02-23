<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fulltext?lang_cible=da
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Accenttegn spiller ingen rolle (idé eller ide vil give de samme resultater...)',
	'activer_indexation' => 'Activer l\'indexation des fichiers @ext@', # NEW
	'asie' => 'asien',
	'asterisque_terminale' => 'vil ikke give resultat: man skal afslutte med stjernen (*)', # MODIF
	'aussi' => 'også',

	// C
	'casse_indifferente' => 'Det er lige meget, om man skriver med store eller små bogstaver.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents', # NEW
	'configurer_egalement_doc' => 'Vous pouvez également configurer l\'indexation des documents :', # MODIF
	'convertir_myisam' => 'Konverter til MyISAM',
	'convertir_toutes' => 'Convertir toutes les tables en MyISAM', # NEW
	'convertir_utf8' => 'convertir en UTF-8 pour restaurer la cohérence', # NEW
	'creer_tous' => 'Dan alle foreslåede FULLTEXT tabeller',

	// D
	'des_utilisations' => '@nb@ utilisations', # NEW
	'descriptif' => 'Descriptif', # NEW
	'documents_proteges' => 'Documents protégés', # MODIF

	// E
	'enfan' => 'barn',
	'enfance' => 'barndom',
	'enfant' => 'barn',
	'enfanter' => 'barnefødsel',
	'enfantillage' => 'barnagtig',
	'enfants' => 'barndomsven',
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.', # NEW
	'erreur_doc_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .doc', # MODIF
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.', # MODIF
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.', # MODIF
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .pdf', # MODIF
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .ppt', # MODIF
	'erreur_taille_index' => 'Il faut au moins indexer un caractère.', # MODIF
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.', # NEW
	'erreur_xls_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .xls', # MODIF
	'et' => 'OG',
	'etranger' => 'fremmed',
	'exemples' => 'Eksempler på brug',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.', # NEW

	// F
	'fant' => 'domsven',
	'fonctionnement_recherche' => 'Hvordan søgefunktionen virker på denne side',
	'fulltext_cree' => 'FULLTEXT créé', # NEW
	'fulltext_creer' => 'Créer l\'index @index@', # NEW
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :', # NEW
	'fulltext_documents' => 'Fulltext - Documents', # NEW
	'fulltext_index' => 'Fulltext - Index', # NEW

	// G
	'general' => 'Général', # NEW

	// I
	'id' => 'ID', # NEW
	'il_faut_myisam' => 'MyISAM kræves',
	'incoherence_charset' => 'Une incohérence entre le charset de votre site et celui des tables de votre base de données risque de fausser les recherches avec caractères accentués:', # NEW
	'index_regenere' => 'index de la table régénérés', # NEW
	'index_reinitialise' => 'Les documents en erreur ont été réinitialisés', # NEW
	'index_reinitialise_ptg' => 'Les documents protégés ont tous été réinitialisés', # NEW
	'index_reinitialise_totalement' => 'Les document ont tous été réinitialisés', # NEW
	'index_supprime' => 'index supprimé', # NEW
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant l\'indexation des', # NEW
	'indiquer_options_bin' => 'Indiquer les options pour l\'indexation des', # NEW
	'infos' => 'Informations', # NEW
	'infos_documents_proteges' => 'Vous trouverez ici la liste des documents protégés et donc non-indexés par Fulltext', # NEW
	'infos_fulltext_document' => 'Vous pourrez ici choisir quels type de documents sont indexés par Fulltext et configurer les binaires utilisés et leurs options.', # NEW
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).', # NEW

	// L
	'liste_tables_connues' => 'Voici la liste des tables connues de la recherche. Vous pouvez y ajouter des éléments FULLTEXT, cf. documentation à l\'adresse', # NEW
	'logo' => 'Logo', # NEW

	// M
	'mais_pas' => 'men IKKE',
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
	'ou_bien' => 'ou bien', # NEW

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.', # NEW
	'pas_index' => 'Pas d\'index FULLTEXT', # NEW
	'premier_soit' => 'ENTEN',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options proposées ici ab initio ne sont que des exemples.', # MODIF

	// R
	'regenerer_tous' => 'Régénérer tous les index FULLTEXT', # NEW
	'reinitialise_index_doc' => 'Réinitialiser l\'indexation des documents en erreur', # NEW
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés', # NEW
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents', # NEW
	'reserve_webmestres' => 'Kun webadministratorer kan bruge denne side',
	'retour_configuration_fulltext' => 'Retour à la configuration de Fulltext', # MODIF
	'retourne' => 'Giver tekst som indeholder',

	// S
	'sequence_exacte' => 'den præcise sætning',
	'soit' => 'ELLER',
	'statistiques_indexation' => 'Statistiques d\'indexation des documents :', # NEW
	'supprimer' => 'Slet',

	// T
	'table_convertie' => 'table convertie en MyISAM', # NEW
	'table_format' => 'Cette table est au format', # NEW
	'table_non_reconnue' => 'table non reconnue', # NEW
	'textes_premier' => 'men viser først tekst, som indeholder',
	'titre_page_fulltext_index' => 'Configuration des index de recherche', # NEW

	// U
	'une_utilisation' => '1 utilisation', # NEW
	'utiliser_operateurs_logiques' => 'Søgningen bruger de standard logiske operatorer.',

	// V
	'voir_doc_ptg' => 'Voir les documents protegés' # NEW
);

?>

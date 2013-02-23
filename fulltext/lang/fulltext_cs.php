<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fulltext?lang_cible=cs
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'accents_pas_pris' => 'Nezáleží na diakritice (Můžete napsat "růže" nebo "ruze", výsledek bude stejný. Totéž platí pro "rúže", "ruže", "růze" atp.)',
	'activer_indexation' => 'Activer l\'indexation des fichiers @ext@', # NEW
	'asie' => 'asie',
	'asterisque_terminale' => 'nevyhledá žádné výsledky: hvězdička musí být na konci', # MODIF
	'aussi' => 'také',

	// C
	'casse_indifferente' => 'Forma písmen ve slovech (malé/velké písmeno) nemá vliv.',
	'configuration_indexation_document' => 'Configuration de l\'indexation des documents', # NEW
	'configurer_egalement_doc' => 'Vous pouvez également configurer l\'indexation des documents :', # MODIF
	'convertir_myisam' => 'Konvertovat do MyISAM',
	'convertir_toutes' => 'Konvertovat všechny tabulky do MyISAM',
	'convertir_utf8' => 'obnovit koherenci převedením do UTF-8',
	'creer_tous' => 'Vytvořit všechny navrhované indexy FULLTEXTu',

	// D
	'des_utilisations' => '@nb@ utilisations', # NEW
	'descriptif' => 'Descriptif', # NEW
	'documents_proteges' => 'Documents protégés', # MODIF

	// E
	'enfan' => 'probl',
	'enfance' => 'problematika',
	'enfant' => 'problém',
	'enfanter' => 'problém',
	'enfantillage' => 'problémovost',
	'enfants' => 'problémy',
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.', # NEW
	'erreur_doc_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .doc', # MODIF
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.', # MODIF
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.', # MODIF
	'erreur_pdf_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .pdf', # MODIF
	'erreur_ppt_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .ppt', # MODIF
	'erreur_taille_index' => 'Il faut au moins indexer un caractère.', # MODIF
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.', # NEW
	'erreur_xls_bin' => 'Vous devez renseigner le binaire à utiliser pour extraire les .xls', # MODIF
	'et' => 'A',
	'etranger' => 'cizinec',
	'exemples' => 'Použití příkladů',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.', # NEW

	// F
	'fant' => 'blém',
	'fonctionnement_recherche' => 'Jak na této stránce funguje vyhledávání',
	'fulltext_cree' => 'FULLTEXT byl vytvořen',
	'fulltext_creer' => 'Créer l\'index @index@', # NEW
	'fulltext_documentation' => 'Pour plus d\'information sur la configuration, consultez la documentation en ligne :', # NEW
	'fulltext_documents' => 'Fulltext - Documents', # NEW
	'fulltext_index' => 'Fulltext - Index', # NEW

	// G
	'general' => 'Général', # NEW

	// I
	'id' => 'ID', # NEW
	'il_faut_myisam' => 'Je požadován MyISAM',
	'incoherence_charset' => 'Sada znaků na stránce neodpovídá té použité v databázi. To může vést ke špatným výsledkům vyhledávání, pokud použijete následující znaky s diakritikou:',
	'index_regenere' => 'obsah tabulky byl obnoven',
	'index_reinitialise' => 'Dokumenty, ve kterých byla zaznamenána chyba, byly reinicializovány.',
	'index_reinitialise_ptg' => 'Les documents protégés ont tous été réinitialisés', # NEW
	'index_reinitialise_totalement' => 'Les document ont tous été réinitialisés', # NEW
	'index_supprime' => 'rejstřík byl smazán',
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant l\'indexation des', # NEW
	'indiquer_options_bin' => 'Indiquer les options pour l\'indexation des', # NEW
	'infos' => 'Informations', # NEW
	'infos_documents_proteges' => 'Vous trouverez ici la liste des documents protégés et donc non-indexés par Fulltext', # NEW
	'infos_fulltext_document' => 'Vous pourrez ici choisir quels type de documents sont indexés par Fulltext et configurer les binaires utilisés et leurs options.', # NEW
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).', # NEW

	// L
	'liste_tables_connues' => 'Zde je seznam tabulek, které byly zohledněny při hledání. Můžete přidat další prvky FULLTEXTu – viz dokumentace na',
	'logo' => 'Logo', # NEW

	// M
	'mais_pas' => 'ale NE',
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
	'ou_bien' => 'nebo jinak',

	// P
	'pas_document_ptg' => 'Il n\'y a pas de document protégé.', # NEW
	'pas_index' => 'Chybí rejstřík FULLTEXTu',
	'premier_soit' => 'BUĎ',

	// Q
	'que_des_exemples' => 'NB : les adresses de binaires et options proposées ici ab initio ne sont que des exemples.', # MODIF

	// R
	'regenerer_tous' => 'Obnovit všechny rejstříky FULLTEXTu',
	'reinitialise_index_doc' => 'Opětovné spuštění indexace dokumentů obsahuje chybu',
	'reinitialise_index_ptg' => 'Réinitialiser l\'indexation des documents protégés', # NEW
	'reinitialise_totalement_doc' => 'Réinitialiser l\'indexation de tous les documents', # NEW
	'reserve_webmestres' => 'Na tuto stranu má přístup pouze administrátor',
	'retour_configuration_fulltext' => 'Retour à la configuration de Fulltext', # MODIF
	'retourne' => 'Vyhledá texty obsahující',

	// S
	'sequence_exacte' => 'přesnou frázi',
	'soit' => 'NEBO',
	'statistiques_indexation' => 'Statistiques d\'indexation des documents :', # NEW
	'supprimer' => 'Vymazat',

	// T
	'table_convertie' => 'tabulka byla zkonvertována do MyISAM',
	'table_format' => 'Tato tabulka je ve formátu',
	'table_non_reconnue' => 'neznámá tabulka',
	'textes_premier' => 'ale zobrazí nejprve texty, které obsahují',
	'titre_page_fulltext_index' => 'Configuration des index de recherche', # NEW

	// U
	'une_utilisation' => '1 utilisation', # NEW
	'utiliser_operateurs_logiques' => 'Vyhledávání používá standardní logické operátory.',

	// V
	'voir_doc_ptg' => 'Voir les documents protegés' # NEW
);

?>

<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/fulltext/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
    // A
    'accents_pas_pris' => 'Accents are not taken into account ("d&#233;j&#224;" or "deja", will return the same results, including "d&#233;j&#224;", "dej&#224;", "d&#233;ja"...)',
    'asterisque_terminale' => 'will return no results: the asterisk must be final',
    'aussi' => 'also',
    
    // C
    'casse_indifferente' => 'The case (lowercase/uppercase) of letters in the words is without effect.',
    'convertir_myisam' => 'Convert to MyISAM',
    'convertir_toutes' => 'Convert all tables to MyISAM',
    'convertir_utf8' => 'convert to UTF-8 to restore coherency',
    'creer_tous' => 'Create all the suggested FULLTEXT indexes',
    
    // E
    'et' => 'AND',
    'exemples' => 'Usage examples',
    
    // F
    'fonctionnement_recherche' => 'How the search mechanism works on this site',
    'fulltext_cree' => 'FULLTEXT created',
    
    // I
    'il_faut_myisam' => 'MyISAM is required',
    'incoherence_charset' => 'There is an inconsistency between the character set of your site and that of the database. This may lead to bad results being given for searches which contain accentuated characters:',
            
    'index_supprime' => 'index deleted',
    'index_regenere' => 'table index regenerated',
    
    // L
    'liste_tables_connues' => 'Here is the list of tables taken into account for searches. You can add more FULLTEXT elements -- see the documentation at',
    
    // M
    'mais_pas' => 'but NOT',
    
    // O
    'ou_bien' => 'or else',
    
    // P
    'pas_index' => 'No FULLTEXT index',
    
    // R
    'regenerer_tous' => 'Regenerate all FULLTEXT indexes',
    'reserve_webmestres' => 'Only webmasters can use this page',
    'retourne' => 'Returns texts containing',
    
    // S
    'sequence_exacte' => 'the exact phrase',
    'soit' => 'AS',
    'supprimer' => 'Delete',
    
    // T
    'table_convertie' => 'table converted to MyISAM',
    'table_format' => 'This table\'s format is',
    'table_non_reconnue' => 'unrecognised table',
    'textes_premier' => 'but presents first the texts which contain',
    
    // U
    'utiliser_operateurs_logiques' => 'The search uses standard logical operators.'
    
);

?>

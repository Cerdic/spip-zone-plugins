<?php


/**
 * Déclarations des tables et objets au compilateur
 *
 * @package SPIP\Core\Pipelines
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Interfaces de la table forum pour le compilateur
 * @seeAlso safehtml($t)
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 * @return array $interfaces
 */
function sanitizer_declarer_tables_interfaces($interfaces) {
    // better protect the backend input field
    /**
     * articles
     */
    $interfaces['table_des_traitements']['TITRE']['articles'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_TYPO_SANS_NUMERO);
    $interfaces['table_des_traitements']['TEXTE']['articles'] = str_replace("%s", "sanitizerFilterText(%s)", _TRAITEMENT_RACCOURCIS);
    $interfaces['table_des_traitements']['DESCRIPTIF']['articles'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_RACCOURCIS);
    $interfaces['table_des_traitements']['CHAPO']['articles'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_RACCOURCIS);
    $interfaces['table_des_traitements']['PS']['articles'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_RACCOURCIS);

    /**
     * evenement
     */
    $interfaces['table_des_traitements']['TITRE']['evenements'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_TYPO_SANS_NUMERO);

    $interfaces['table_des_traitements']['LIEU']['evenements'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_TYPO_SANS_NUMERO);
    $interfaces['table_des_traitements']['ADRESSE']['evenements'] = str_replace("%s","sanitizerFilterText(%s)", _TRAITEMENT_RACCOURCIS);
    return $interfaces;
}

/**
 * Filter out scripts
 * @param $s
 * @return mixed
 */
function sanitizerFilterText($s) {
    $s =  interdire_scripts($s);
    $s =  echappe_js($s);
    return $s;
}

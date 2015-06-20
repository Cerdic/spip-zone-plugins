<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_uploadhtml5_saisies_dist() {
    $saisies = array(
        array(
            'saisie' => 'case',
            'options' => array(
                'nom' => 'charger_public',
                'label' => _T('uploadhtml5:label_charger_public'),
                'label_case' => _T('uploadhtml5:case_charger_public')
            )
        ),
        array(
            'saisie' => 'case',
            'options' => array(
                'nom' => 'remplacer_editer_logo',
                'label' => _T('uploadhtml5:label_remplacer_editer_logo'),
                'label_case' => _T('uploadhtml5:case_remplacer_editer_logo')
            )
        ),
        array(
            'saisie' => 'input',
            'options' => array(
                'nom' => 'max_file_size',
                'label' => _T('uploadhtml5:max_file_size'),
                'explication' => _T('uploadhtml5:explication_max_file_size'),
            )
        ),
        array(
            'saisie' => 'input',
            'options' => array(
                'nom' => 'max_file',
                'label' => _T('uploadhtml5:max_file'),
                'explication' => _T('uploadhtml5:explication_max_file'),
            )
        )
    );

    return $saisies;
}


function formulaires_configurer_uploadhtml5_charger_dist() {
    return lire_config('uploadhtml5');
}
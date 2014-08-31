<?php
// On va créer les champs extras pour spip_projets

function rss_commits_declarer_champs_extras($champs = array())
{
    $champs['spip_projets']['versioning_path'] = array(
        'saisie' => 'input',//Type du champ (voir plugin Saisies)
        'options' => array(
            'nom' => 'versioning_path',
            'label' => _T('commit:champ_versioning_path_label'),
            'sql' => "varchar(255) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
            'modifier' => array('auteur' => '0minirezo')),//Seuls les administrateurs peuvent modifier
        ),
    );
    $champs['spip_projets']['versioning_trac'] = array(
        'saisie' => 'input',//Type du champ (voir plugin Saisies)
        'options' => array(
            'nom' => 'versioning_trac',
            'label' => _T('commit:champ_versioning_trac_label'),
            'sql' => "varchar(255) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
            'modifier' => array('auteur' => '0minirezo')),//Seuls les administrateurs peuvent modifier
        ),
    );
    $champs['spip_projets']['versioning_type'] = array(
        'saisie' => 'input',//Type du champ (voir plugin Saisies)
        'options' => array(
            'nom' => 'versioning_type',
            'label' => _T('commit:champ_versioning_type_label'),
            'sql' => "varchar(255) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
            'modifier' => array('auteur' => '0minirezo')),//Seuls les administrateurs peuvent modifier
        ),
    );
    $champs['spip_projets']['versioning_rss'] = array(
        'saisie' => 'input',//Type du champ (voir plugin Saisies)
        'options' => array(
            'nom' => 'versioning_rss',
            'label' => _T('commit:champ_versioning_rss_label'),
            'sql' => "varchar(255) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
            'modifier' => array('auteur' => '0minirezo')),//Seuls les administrateurs peuvent modifier
        ),
    );
    return $champs;
}

?>
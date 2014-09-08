<?php

include_spip('rss_commits_fonctions');
include_spip('base/abstract_sql');

function genie_import_commits_dist ($t)
{
    $log = array();
    $log[] = "\n ----------" . date_format(date_create(), 'Y-m-d H:i:s') . ' : on lance ' . __FUNCTION__ ;
    $commits_anciens = array();
    $commits_nouveaux = array();
    $commits_en_bdd   = sql_allfetsel('id_projet,url_revision', 'spip_commits', "url_revision !=''");
    if (count($commits_en_bdd) > 0) {
        foreach ($commits_en_bdd as $key => $value) {
            $commits_anciens[] = $value['id_projet'] . '|' . $value['url_revision'];
        }
    }
    $commits = lister_rss_commits();
    if (count($commits) > 0) {
        foreach ($commits as $key => $value) {
            if (!in_array($value['id_projet'] . '|' . $value['url_revision'], $commits_anciens)) {
                // On stocke dans le tableau, les nouveaux commits qui doivent être ajoutés en BDD.
                $commits_nouveaux[] = $value;
                $log[] = 'Le commit ' . $value['url_revision'] . ' va être enregistré pour le projet n#' . $value['id_projet'];
            } else {
                $log[] = 'Le commit ' . $value['url_revision'] . ' est déjà enregistré pour le projet n#' . $value['id_projet'];
            }
        }
        // On insère par lot les nouveaux commits pour éviter un débordement de mémoire
        // cf. http://programmer.spip.net/sql_insertq_multi,591
        if (count($commits_nouveaux) > 0) {
            sql_insertq_multi('spip_commits', $commits_nouveaux);
        } else {
            $log[] = 'Pas d\'ajout de nouveaux commits dans la BDD';
        }
    } else {
        $log[] = 'Il n\'y a pas de nouveaux commits';
    }
    $log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : ' . __FUNCTION__ . ' a fini son travail' . "\n ----------";

    spip_log(implode("\n", $log), 'rss_commits');
}
?>
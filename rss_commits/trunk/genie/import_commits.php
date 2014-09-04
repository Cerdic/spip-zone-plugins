<?php

include_spip('rss_commits_fonctions');
include_spip('base/abstract_sql');

function genie_import_commits_dist ($t)
{

    $commits = lister_rss_commits();
    if (count($commits) > 0) {
        foreach ($commits as $key => $value) {
            if (!$commit_enregistre = sql_fetsel(
                '*',
                'spip_commits',
                'id_projet='
                . $value['id_projet']
                . ' AND url_revision="'
                . $value['url_revision']
                . '"'
            )) {
                // On nettoie un peu le texte de tout espace indésirable dùu au CDATA.
                $value['texte'] = trim($value['texte']);
                $id_commit = sql_insertq('spip_commits', $value);
            }
        }
    }
}
?>
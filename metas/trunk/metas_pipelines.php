<?php

/**
 * Affichage des metas dans le privé
 *
 * @param array $flux
 * @return array
 */

function metas_affiche_milieu($flux)
{
    spip_log(__LINE__ . "metas_affiche_milieu", "ben." . _LOG_ERREUR);
    $en_cours = trouver_objet_exec($flux['args']['exec']);

    //debug
    ob_start();
    var_export($flux['args']);
    var_export($en_cours);
    $en_cours_debug = ob_get_contents();
    ob_end_clean();
    spip_log(__LINE__ . " dollar flux + dollar en_vours:" . $en_cours_debug, "ben." . _LOG_ERREUR);

    spip_log(__LINE__ . " edition:" . $en_cours['edition'], "ben." . _LOG_ERREUR);
    spip_log(__LINE__ . " type:" . $en_cours['type'], "ben." . _LOG_ERREUR);
    spip_log(__LINE__ . " id_table_objet:" . $en_cours['id_table_objet'], "ben." . _LOG_ERREUR);
    spip_log(__LINE__ . " id_article:" . $flux['args']['id_article'], "ben." . _LOG_ERREUR);
    if ($en_cours['edition'] == true // page visu
        AND $type = $en_cours['type']
            AND $id_table_objet = $en_cours['id_table_objet']
                AND ($id = intval($flux['args'][$id_table_objet]))
    ) {
        spip_log(__LINE__, "ben." . _LOG_ERREUR);
        $texte = recuperer_fond(
            'prive/squelettes/inclure/editer_metas',
            array(
                 'table_source' => 'metas',
                 'objet' => $type,
                 'id_objet' => $id
            )
        );

        spip_log(__LINE__." texte:". $texte, "ben." . _LOG_ERREUR);


        $flux['data'] .= $texte;


        spip_log(__LINE__ , "ben." . _LOG_ERREUR);
    }
    return $flux;
}

?>
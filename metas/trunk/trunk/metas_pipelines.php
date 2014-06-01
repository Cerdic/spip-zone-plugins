<?php

/**
 * Affichage des metas dans le privé
 *
 * @param array $flux
 * @return array
 */

function metas_affiche_milieu($flux)
{
    $en_cours = trouver_objet_exec($flux['args']['exec']);

    // Mode edition, affichage du formulaire.
    if ($en_cours['edition'] == true // page visu
        AND $type = $en_cours['type']
            AND $id_table_objet = $en_cours['id_table_objet']
                AND ($id = intval($flux['args'][$id_table_objet]))
    ) {
        $texte = recuperer_fond(
            'prive/squelettes/inclure/editer_metas',
            array(
                 'table_source' => 'metas',
                 'objet' => $type,
                 'id_objet' => $id
            )
        );
        // on affiche le texte des metas au niveau du commentaire affiche_milieu (et pas en fin de page)
        if ($p = strpos($flux['data'], "<!--affiche_milieu-->"))
            $flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
        else
            $flux['data'] .= $texte;
    }


    return $flux;
}

?>
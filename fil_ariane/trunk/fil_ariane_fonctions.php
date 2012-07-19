<?php

/***
 * Plugin Fil d'Ariane pour SPIP
 * Auteur : Cyril Marion, Ateliers CYM
 *
 * Tres forte inspiration du site http://programmer.spip.org/
 * Notamment la rubrique Recuperer-objet-et-id_objet
 */

/***
 * Balise #FIL_ARIANE
 * Récupère l'objet depuis le contexte
 * et construit un fil d'Ariane.
 */
function balise_FIL_ARIANE_dist($p){

    // L'id de l'objet
    $_id_objet = $p->boucles[$p->id_boucle]->primary;

    // Le champ (?)
    $id_objet = champ_sql($_id_objet, $p);

    // L'objet issu du nom de la table
    $objet = $p->boucles[$p->id_boucle]->id_table;

    $p->code = "construire_FIL_ARIANE('$objet', $id_objet)";
    return $p;
}
/***
 * Construit le fil d'Ariane
 */
function construire_FIL_ARIANE($objet, $id_objet){
    $objet = objet_type($objet);
    $fil_ariane = '<div class="fil_ariane">';
    $fil_ariane.= '<a href="#">Racine du site</a> &gt; <a href="#">Rubrique</a> &gt; <strong>' . $objet . ' ' . $id_objet . '</strong></a>';
    $fil_ariane.= '</div>';
    return $fil_ariane;
}

?>

<?php

/***
 * Plugin Fil d'Ariane pour SPIP
 * Auteur : Cyril Marion, Ateliers CYM
 */

/***
 * Balise #FIL_ARIANE
 * Récupère l'objet depuis le contexte
 * et construit un fil d'Ariane.
 */
function balise_FIL_ARIANE_dist($p){
    $p->code = "construire_FIL_ARIANE()";;
    return $p;
}
/***
 * Construit le fil d'ariane
 */
function construire_FIL_ARIANE(){
    $fil_ariane = '<div class="fil_ariane">';
    $fil_ariane.= '<a href="#">Racine du site</a> &gt; <a href="#">Rubrique</a> &gt; <strong>Hello world !</strong></a>';
    $fil_ariane.= '</div>';
    return $fil_ariane;
}

?>

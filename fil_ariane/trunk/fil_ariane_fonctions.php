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

    // on attend un tableau avec le fil d'ariane
    // le 1er paramètre passé avec la balise "fil_ariane"
    $fil = interprete_argument_balise(1,$p);

    if (!$fil) {
        // On appele la fonction qui construit le fil en prenant en compte seulement l'objet
        // L'id de l'objet
        $_id_objet = $p->boucles[$p->id_boucle]->primary;

        // Code php mis en cache, et qui sera exécuté et qui est sensé ramener la valeur du champ
        $id_objet = champ_sql($_id_objet, $p);

        // L'objet issu du nom de la table
        $objet = $p->boucles[$p->id_boucle]->id_table;

        $p->code = "calcule_hierarchie_objet('$objet', $id_objet)";
    }

    else {
        // On décortique le tableau $fil et on appelle la fonction qui construit le fil
        // avec les valeurs du tableau
        $p->code = "construire_FIL_ARIANE($fil)";

    }

    return $p;
}

/***
 * @param $objet
 * @param $id_objet
 * Calcule la hierarchie d'un objet et la retourne sous forme d'un tableau
 */
function calcule_hierarchie_objet($objet, $id_objet) {
    return '';
}
/***
 * Construit le fil d'Ariane
 */
function construire_FIL_ARIANE($fil){

    $fil_ariane = '<div class="fil_ariane">';

    if (!is_array($fil)) {
        return '';
    }

    $nb= count($fil);
    $passe=0;
    $fil_ariane.=" nb elets : $nb | ";

    foreach($fil as $titre => $lien) {
        if($passe>0) $fil_ariane.=" &gt; ";
        $fil_ariane.= "<a". (($passe == $nb-1)?' class="on"':'') ." href='$lien'>$titre</a>";
        $passe++;
    }


    // dernier : en gras, sans lien et sans '>'

    $fil_ariane.= '</div>';
    return $fil_ariane;
}

?>

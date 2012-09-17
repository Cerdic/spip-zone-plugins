<?php

/***
 * Plugin Fil d'Ariane pour SPIP
 * Auteur : Cyril Marion, Ateliers CYM
 *
 * Tres forte inspiration du site http://programmer.spip.org/
 * Notamment la rubrique Recuperer-objet-et-id_objet
 */

// Si cette constante est vraie la hierarchie commence par "accueil"
if (!defined('_FIL_ARIANE_ACCUEIL')) define('_FIL_ARIANE_ACCUEIL',true);
#defined('_FIL_ARIANE_ACCUEIL') || define('_FIL_ARIANE_ACCUEIL',true);

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

/**
 * @param $objet
 * @param $id_objet
 * Calcule la hierarchie d'un objet et la retourne sous forme d'un tableau
 */
function calcule_hierarchie_objet($objet, $id_objet) {

    if($f = charger_fonction ($objet , 'fil_ariane', true)){
        $fil = $f($id_objet);
        return construire_FIL_ARIANE($fil);
    }

    $fil_ariane_objet = charger_fonction ('objet' , 'fil_ariane');
    $fil = $fil_ariane_objet($objet, $id_objet);
    return construire_FIL_ARIANE($fil);

}
/**
 * Construit le fil d'Ariane
 */
function construire_FIL_ARIANE($fil){

    $fil_ariane = '<div class="fil_ariane">';

    if (!is_array($fil)) {
        return '';
    }

    $nb= count($fil);
    $passe=0;

    foreach($fil as $titre => $lien) {
        if($passe>0) $fil_ariane.=" &gt; ";
        $fil_ariane.= "<a". (($passe == $nb-1)?' class="on"':'') ." href='$lien'>$titre</a>";
        $passe++;
    }

    $fil_ariane.= '</div>';
    return $fil_ariane;
}

/**
 * Calcule un tableau de valeurs représentant une hiérarchie de fil d'Ariane.
 * @param int $id_objet
 * @return array
 *    couples titre => url
 */
function fil_ariane_objet_dist($objet,$id_objet) {

    $url    = generer_url_entite($id_objet,$objet);
    $titre  = generer_info_entite($id_objet, $objet, 'titre');

    $fil = array();
    if (_FIL_ARIANE_ACCUEIL) $fil[_T('public:accueil_site')] = $GLOBALS['meta']['adresse_site'];
    $fil[$titre] =  $url;
    return $fil;
}
/*
function fil_ariane_article_dist($id_article) {
    return array;
}
*/

<?php

/***
 * Plugin Fil d'Ariane pour SPIP
 * Auteur : Cyril Marion, Ateliers CYM
 *
 * Tres forte inspiration du site http://programmer.spip.org/
 * Notamment la rubrique Recuperer-objet-et-id_objet
 */

// Si cette constante est vraie le fil d'Ariane commence par "accueil"
if (!defined('_FIL_ARIANE_ACCUEIL')) define('_FIL_ARIANE_ACCUEIL',true);
#defined('_FIL_ARIANE_ACCUEIL') || define('_FIL_ARIANE_ACCUEIL',true);

/***
 * Balise #FIL_ARIANE
 * Récupère l'objet depuis le contexte
 * et construit un fil d'Ariane.
 */
function balise_FIL_ARIANE_dist($p){

    // il est possible qu'il y ait un tableau des valeurs souhaitées pour  le fil d'Ariane
    // il s'agit dans ce cas du 1er paramètre passé avec la balise "fil_ariane"
    $fil = interprete_argument_balise(1,$p);

    if (!$fil) {
        // On appele la fonction qui construit le fil d'Ariane
        // en prenant en compte seulement l'id de l'objet
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

    $objet = objet_type($objet); // pour obtenir le type d'objet au singulier

    if($f = charger_fonction ($objet , 'fil_ariane', true)){
        $fil = $f($id_objet);
        return construire_FIL_ARIANE($fil);
    }

    $fil_ariane_objet = charger_fonction ($objet , 'fil_ariane');
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

    // si on doit tracer le 1er item, on l'ajoute au début du tableau
    if (_FIL_ARIANE_ACCUEIL) $fil = array(_T('public:accueil_site') => $GLOBALS['meta']['adresse_site']) + $fil;

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
    $fil[$titre] =  $url;
    return $fil;
}

function fil_ariane_organisation_dist($id_organisation) {

    $fil = array();

    // trouver le nom et le parent de l'organisation en cours
   $organisation = sql_fetsel('nom,id_parent', 'spip_organisations', 'id_organisation = '.sql_quote($id_organisation));

    // url de l'organisation
    $url = generer_url_entite($id_organisation,'organisation');

    // parent de l'organisation
    $id_parent  = $organisation['id_parent'];

    // tant qu'il y a des parents, je place nom => url dans le tableau
    while ($id_parent) {
        // on trouve le parent, son nom, son url
        $parent = sql_fetsel('nom,id_parent', 'spip_organisations', 'id_organisation = '.sql_quote($id_parent));
        $url_parent = generer_url_entite($id_parent,'organisation');

        $fil[$parent['nom']] = $url_parent;
        $id_parent = $parent['id_parent'];
    }

    // on inverse le tableau
    $fil = array_reverse($fil,true);

    $fil[$organisation['nom']] = $url;

    return $fil;
}

function fil_ariane_rubrique_dist($id_rubrique) {

    $fil = array();

    // trouver le titre et l'id du parent de la rubrique en cours; on calcule son url;
    $rubrique = sql_fetsel('titre, id_parent', 'spip_rubriques', 'id_rubrique = '.sql_quote($id_rubrique));

    $titre = typo(supprimer_numero($rubrique['titre']));
    $id_parent = $rubrique['id_parent'];
    $url = generer_url_entite($id_rubrique,'rubrique');

    // tant qu'il y a des parents, je place nom => url dans le tableau
    while ($id_parent) {
        // on trouve le parent, son titre; on calcule son url
        $parent = sql_fetsel('titre, id_parent', 'spip_rubriques', 'id_rubrique = '.sql_quote($id_parent));

        $nom_parent = typo(supprimer_numero($parent['titre']));
        $url_parent = generer_url_entite($id_parent,'rubrique');

        $fil[$nom_parent] = $url_parent;
        $id_parent = $parent['id_parent'];
    }

    // on inverse le tableau
    $fil = array_reverse($fil,true);

    $fil[$titre] = $url;

    return $fil;
}

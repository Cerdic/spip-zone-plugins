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

// Si cette constante est vraie le fil d'Ariane se termine par un lien
if (!defined('_FIL_ARIANE_LIEN')) define('_FIL_ARIANE_LIEN',false);

// Cette constante définit le nom de la classe CSS attribué au conteneur du fil
if (!defined('_FIL_ARIANE_STYLE')) define('_FIL_ARIANE_STYLE','fil_ariane hierarchie breadcrumb');

// Cette constante définit le caractère séparateur entre chaque élément du fil (les espaces comptent !)
if (!defined('_FIL_ARIANE_SEP')) define('_FIL_ARIANE_SEP',' &gt; ');

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

    $fil_ariane_objet = charger_fonction ('objet' , 'fil_ariane');
    $fil = $fil_ariane_objet($objet, $id_objet);
    return construire_FIL_ARIANE($fil);

}
/**
 * Construit le fil d'Ariane
 */
function construire_FIL_ARIANE($fil){

    $fil_ariane = '<div class="'. _FIL_ARIANE_STYLE .'">';

    if (!is_array($fil)) {
        return '';
    }

    // si on doit tracer le 1er élément, on l'ajoute au début du tableau
    if (_FIL_ARIANE_ACCUEIL)
        $fil = array(_T('public:accueil_site') => $GLOBALS['meta']['adresse_site']) + $fil;


    $nb= count($fil);
    $passe=0;

    foreach($fil as $titre => $lien) {

        // si on a déja tracé un élément, mais qu'on est pas encore arrivé au dernier
        if($passe>0)
            $fil_ariane.="<span class=\"sep divider\">" . _FIL_ARIANE_SEP . "</span>";

        // tant qu'on est pas encore arrivé au dernier élément
        if($passe<$nb-1)
            $fil_ariane.= "<a href='$lien'>$titre</a>";

        // si on arrive au dernier
        elseif ($passe >= $nb-1) {
            if (_FIL_ARIANE_LIEN)
                $fil_ariane.= "<a class='on' href='$lien'>$titre</a>";
            else
                $fil_ariane.= "<span class='on'>$titre</span>";
        }

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

    $url    = generer_url_entite($id_objet, $objet);
    $titre  = generer_info_entite($id_objet, $objet, 'titre');

    $fil = array();
    $fil[$titre] =  $url;
    return $fil;
}

function fil_ariane_organisation_dist($id_organisation) {
    return fil_ariane_hierarchie_objet('organisation', $id_organisation, 'nom', 'id_parent');
}

function fil_ariane_rubrique_dist($id_rubrique) {
    return fil_ariane_hierarchie_objet('rubrique', $id_rubrique, 'titre', 'id_parent');
}

function fil_ariane_article_dist($id_article) {
    // récupere l'id de la rubrique parent, le titre de l'article
    $item = sql_fetsel('id_rubrique, titre','spip_articles',"id_article = ".sql_quote($id_article));

    // récupère la hierarchie de la rubrique parent
    $fil_ariane_rubrique = charger_fonction ('rubrique' , 'fil_ariane');
    $fil = $fil_ariane_rubrique($item['id_rubrique']);

    // ajoute le titre et l'url de l'article
    $fil[typo(supprimer_numero($item['titre']))] = generer_url_entite($id_article,'article');

    return $fil;
}

function fil_ariane_produit_dist($id_produit) {
    // récupère l'id de la rubrique parent ainsi que le titre du produit
    $item = sql_fetsel('id_rubrique, titre','spip_produits',"id_produit = ".sql_quote($id_produit));

    // récupère la hierarchie de la rubrique du produit
    $fil_ariane_rubrique = charger_fonction ('rubrique' , 'fil_ariane');
    $fil = $fil_ariane_rubrique($item['id_rubrique']);

    // ajoute le titre et l'url du produit
    $fil[typo(supprimer_numero($item['titre']))] = generer_url_entite($id_produit,'produit');

    return $fil;
}

function fil_ariane_mot_dist($id_mot) {
    // récupère l'id du groupe, le titre du mot
    $item = sql_fetsel('id_groupe, titre','spip_mots', "id_mot = ".sql_quote($id_mot));

    // récupère la hierarchie du parent (si le plugin groupes de mots arborescents)
    # $fil = fil_ariane_hierarchie_objet('groupe' , $item['id_groupe'], 'titre', 'id_parent');

    // récupère le nom du groupe
    $groupe = sql_getfetsel('titre', 'spip_groupes_mots', "id_groupe = ".sql_quote($item['id_groupe']));

    // ajoute le titre et l'url du groupe
    $fil[typo(supprimer_numero($groupe))] = generer_url_entite($item['id_groupe'],'groupe');

   // ajoute le titre et l'url du mot
    $fil[typo(supprimer_numero($item['titre']))] = generer_url_entite($id_mot,'mot');

    return $fil;
}


function fil_ariane_hierarchie_objet($objet, $id_objet, $col_titre, $col_parent){
    $fil = array();

    // trouver le nom du champ contenant la clé primaire de l'objet
    $col_id = id_table_objet($objet);

    // trouver le nom de la table contenant l'objet
    $table = table_objet_sql($objet);

    // trouver le titre et l'id du parent de l'objet en cours; on calcule son url;
    $item = sql_fetsel("$col_titre AS titre , $col_parent AS id_parent", $table, "$col_id = ".sql_quote($id_objet));

    $titre = typo(supprimer_numero($item['titre']));
    $id_parent = $item['id_parent'];
    $url = generer_url_entite($id_objet,$objet);

    // tant qu'il y a des parents, je place nom => url dans le tableau
    while ($id_parent > 0) {
        // on trouve le parent, son titre; on calcule son url
        $parent = sql_fetsel("$col_titre AS titre , $col_parent AS id_parent", $table, "$col_id = ".sql_quote($id_parent));

        $nom_parent = typo(supprimer_numero($parent['titre']));
        $url_parent = generer_url_entite($id_parent,$objet);

        $fil[$nom_parent] = $url_parent;
        $id_parent = $parent['id_parent'];
    }

    // on inverse le tableau
    $fil = array_reverse($fil,true);

    $fil[$titre] = $url;

    return $fil;

}

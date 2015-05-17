<?php
/**
 * Fonctions utiles au plugin Court-jus
 *
 * @plugin     Court-jus
 * @copyright  2014
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Courtjus\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_URL_RUBRIQUE_dist($p) {
    $id_rubrique = interprete_argument_balise(1,$p);
    if (!$id_rubrique) $id_rubrique = champ_sql('id_rubrique', $p);

    $code = "courtjus_calculer_rubrique($id_rubrique)";
    $p->code = $code;
    $p->interdire_scripts = false;
    return $p;
}

function courtjus_calculer_rubrique($id_rubrique) {

    // On récupère l'éventuel objet de redirection
    $objet = courtjus_trouver_objet($id_rubrique);
    if ($objet)
        return $objet;
    // Sinon, on cherche les enfant de la rubrique et on cherche un objet dedans
    else {
        $objet = courtjus_trouver_objet_enfant($id_rubrique);
        return $objet;
    }

    return generer_url_entite($id_rubrique, 'rubrique');
}

function courtjus_trouver_objet_enfant($id_rubrique) {

    $enfants = courtjus_quete_enfant($id_rubrique);

    // On cherche un éventuel objet dans les premiers enfants
    while (list($key,$enfant) = each($enfants) and !$objet) {
        $objet = courtjus_trouver_objet($enfant);
        if (!$objet) {
            $objet = courtjus_trouver_objet_enfant($enfant);
        }
    }

    return $objet;
}


function courtjus_trouver_objet_rubrique() {
    // On va cherché les différent objets intaller sur SPIP
    $objets = lister_tables_objets_sql();

    // On va filtrer pour n'avoir que les objet avec un id_rubrique
    $objet_in_rubrique = array();
    foreach($objets as $table => $data) {
        // Si on trouve "id_rubrique" dans la liste des champs, on garde
        // On exclue la table des rubriques de SPIP
        if (array_key_exists('id_rubrique', $data['field']) and $table != table_objet_sql('rubrique')) {
            // On garde le titre dans le tableau afin de pouvoir faire un classement par num titre.
            $objet_in_rubrique[] = array($table, $data['titre']);
        }
    }

    return $objet_in_rubrique;
}

function courtjus_trouver_objet($id_rubrique) {
    // On va compter le nombre d'objet présent dans la rubrique
    $tables = courtjus_trouver_objet_rubrique();

    // on va compter le nombre d'objet qu'il y a dans la rubrique.
    $objets_in_rubrique = array();
    foreach ($tables as $table) {
        // Simplification des variables. On a besoin du titre pour trouver le num titre
        list($table, $titre) = $table;
        $objet = table_objet($table);
        $champs_id = id_table_objet($table);
        $champs = array(
            $champs_id,
            $titre
        );

        $where = array(
            'id_rubrique='.intval($id_rubrique),
            'statut='.sql_quote('publie')
        );
        $objets_rubrique = sql_allfetsel($champs, $table, $where);

        foreach ($objets_rubrique as $objet_rubrique) {
            // Match va contenir le résulat de la recherche de num titre dans le titre
            $match = null;
            // On cherche le num titre dans le titre de l'objet
            preg_match('#^[0-9]*\.#', $objet_rubrique['titre'], $match);
            // On créer le tableau contenant les données de l'objet
            $objets_in_rubrique[] = array(
                'id_objet' => $objet_rubrique[$champs_id],
                'objet' => $objet,
                // Gros Hack, on utilise intval pour avoir une valeur numérique utilisable
                // Comme le . final est considéré comme une virgule, on fini avec une valeur entière.
                'num_titre' => intval($match[0])
            );
        }
    }

    // Maintenant qu'on a le tableau des objets de la rubrique on compte
    $nb_objet = count($objets_in_rubrique);

    // Si on est à 0 objet, on descend dans une sous rubrique
    if ($nb_objet <= 0) {
        // On renvoie false pour déclencher éventuellement la recherche dans une sous rubrique
        return false;
    }
    // Un seul objet dans la rubrique, on renvoie le tableau
    elseif ($nb_objet == 1) {
        return generer_url_entite($objets_in_rubrique[0]['id_objet'], $objets_in_rubrique[0]['objet']);
    }
    // S'il y plusieurs objets dans la rubrique et que le mode "par num titre" est activé, on regiride sur le num titre le plus petit.
    elseif ($nb_objet > 1) {
        // On va aller chercher array_column
        include_spip('array_column/src/array_column');
        // On créer un tableau avec uniquement les num titre
        $minmax = array_column($objets_in_rubrique, 'num_titre');

        // On recherche l'index dans le tableau minmax
        $index = array_search(min($minmax), $minmax);

        // Créer l'URL de redirection
        return generer_url_entite($objets_in_rubrique[$index]['id_objet'], $objets_in_rubrique[$index]['objet']);
    }

    // Sinon, si le mot "plus récent"" est activé on redirige sur l'article le plus récente.

}


function courtjus_quete_enfant($id_rubrique) {
    // On récupère tous les enfants direct.
    $enfants = sql_allfetsel('id_rubrique', table_objet_sql('rubrique'), 'id_parent='.intval($id_rubrique));

    // On simplifie le tableau pour n'avoir que des id
    include_spip('array_column/src/array_column');
    $enfants = array_column($enfants, 'id_rubrique');

    return $enfants;
}

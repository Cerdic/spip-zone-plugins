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

/**
 * Créer la balise #URL_RUBRIQUE et y affecter les fonctions du courtjus
 *
 * @param mixed $p
 * @access public
 * @return mixed
 */
function balise_URL_RUBRIQUE_dist($p) {
    $id_rubrique = interprete_argument_balise(1,$p);
    if (!$id_rubrique) $id_rubrique = champ_sql('id_rubrique', $p);

    $code = "courtjus_calculer_rubrique($id_rubrique)";
    $p->code = $code;
    $p->interdire_scripts = false;
    return $p;
}

/**
 * Calculer l'url de la rubrique
 *
 * @param mixed $id_rubrique
 * @access public
 * @return mixed
 */
function courtjus_calculer_rubrique($id_rubrique) {

    $par_rubrique = lire_config('courtjus/squelette_par_rubrique');
    // Si on n'intervient pas sur les squelettes par rubrique
    if (empty($par_rubrique)) {
        // Si on trouve une squelette spécifique à cette rubrique, et que l'option est activé, on renvoie l'URL rubrique
        // Cela ne gère pas pour les sous-rubrique cependant
        if (find_in_path('rubrique='.$id_rubrique.'.html')
        or find_in_path('rubrique-'.$id_rubrique.'.html'))
            return generer_url_entite($id_rubrique, 'rubrique');

        // Pour gérer les fichiers rubrique-X parent, on va tester chaque parent
        include_spip('public/quete');
        $parent = quete_parent($id_rubrique);
        do {
            if (find_in_path('rubrique-'.$parent.'.html'))
                return generer_url_entite($id_rubrique, 'rubrique');
        }
        while ( ($parent = quete_parent($parent)) > 0);
    }
    // On récupère l'éventuel objet de redirection
    $objet = courtjus_trouver_objet($id_rubrique);
    if ($objet)
        return $objet;
    // Sinon, on cherche les enfant de la rubrique et on cherche un objet dedans
    else {
        // On chercher parmit les enfants de la rubrique
        $objet = courtjus_trouver_objet_enfant($id_rubrique);

        // Si on a trouver un objet enfant.
        if ($objet)
            return $objet;
        // Si on a rien trouvé du tout, on renvoie l'URL de rubrique
        else
            return generer_url_entite($id_rubrique, 'rubrique');
    }
}

/**
 * Fonction récurcive de recherche dans les sous-rubriques
 *
 * @param mixed $id_rubrique
 * @access public
 * @return mixed
 */
function courtjus_trouver_objet_enfant($id_rubrique) {

    // Chercher les enfants de la rubrique
    $enfants = courtjus_quete_enfant($id_rubrique);

    // On cherche un éventuel objet dans les premiers enfants
    while (list($key,$enfant) = each($enfants) and !$objet) {
        $objet = courtjus_trouver_objet($enfant);

        // S'il n'y a pas d'objet au premier niveau on lance la récurcivité pour trouver continuer de descendre dans la hiérachie.
        if (!$objet) {
            $objet = courtjus_trouver_objet_enfant($enfant);
        }
    }
    // On renvoie l'url
    return $objet;
}


/**
 * Renvoie le tableau des objets qui possède un id_rubrique. (sans la table spip_rubrique)
 *
 * @access public
 * @return mixed
 */
function courtjus_trouver_objet_rubrique() {
    // On va cherché les différent objets intaller sur SPIP
    $objets = lister_tables_objets_sql();

    // On va filtrer pour n'avoir que les objet avec un id_rubrique
    $objet_in_rubrique = array();
    foreach($objets as $table => $data) {
        // Si on trouve "id_rubrique" dans la liste des champs, on garde
        // On exclue la table des rubriques de SPIP automatiquement
        // On exclu aussi éléments marqué comme exclu dans la config
        if (array_key_exists('id_rubrique', $data['field'])
        and $table != table_objet_sql('rubrique')
        and !in_array($table, lire_config('courtjus/objet_exclu'))) {
            // On garde le champ qui fait office de titre pour l'objet dans le tableau afin de pouvoir faire un classement par num titre.
            $objet_in_rubrique[] = array($table, $data['titre']);
        }
    }

    return $objet_in_rubrique;
}

/**
 * Fonction qui traite les objet d'une rubrique et renvoie l'url du court-cuircuit.
 *
 * @param mixed $id_rubrique
 * @access public
 * @return mixed
 */
function courtjus_trouver_objet($id_rubrique) {

    // Aller chercher les filtres
    include_spip('inc/flitres');

    // On récupère le configuration du plugin
    $config = lire_config('courtjus');

    // On va compter le nombre d'objet présent dans la rubrique
    $tables = courtjus_trouver_objet_rubrique();

    // on va compter le nombre d'objet qu'il y a dans la rubrique.
    $objets_in_rubrique = array();

    // On boucle sur tout les table qui pourrait être ratacher à une rubrique
    foreach ($tables as $table) {
        // Simplification des variables. On a besoin du titre pour trouver le num titre
        list($table, $titre) = $table;
        // L'objet
        $objet = table_objet($table);
        // l'identifiant de l'objet
        $champs_id = id_table_objet($table);

        // Les champs qui seront utilisé pour la requête.
        $champs = array(
            $champs_id,
            $titre
        );

        // Le where
        $where = array(
            'id_rubrique='.intval($id_rubrique),
            'statut='.sql_quote('publie')
        );

        // On récupère les objets de la rubrique.
        $objets_rubrique = sql_allfetsel($champs, $table, $where);

        // On bouble sur les objets a l'intérique de la rubrique.
        foreach ($objets_rubrique as $objet_rubrique) {

            $num_titre = recuperer_numero($objet_rubrique['titre']);

            // On créer le tableau contenant les données de l'objet
            $objets_in_rubrique[] = array(
                'id_objet' => $objet_rubrique[$champs_id],
                'objet' => $objet,
                'num_titre' => $num_titre
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
    elseif ($nb_objet > 1
      and array_sum(array_column($objets_in_rubrique, 'num_titre')) > 0
      and $config['num_titre'] == 'on') {

        // On créer un tableau avec uniquement les num titre
        $minmax = array_column($objets_in_rubrique, 'num_titre');

        // On va filtrer ce tableau pour n'avoir que des nombres à tester
        $minmax = array_filter($minmax, 'is_numeric');

        // On recherche l'index dans le tableau minmax
        $index = array_search(min($minmax), $minmax);

        // Créer l'URL de redirection
        return generer_url_entite($objets_in_rubrique[$index]['id_objet'], $objets_in_rubrique[$index]['objet']);
    }

    // Sinon, si le mot "plus récent"" est activé on redirige sur l'article le plus récente.

}

/**
 * Renvoie tout les enfants direct d'une rubrique
 *
 * @param mixed $id_rubrique
 * @access public
 * @return mixed
 */
function courtjus_quete_enfant($id_rubrique) {
    // On récupère tous les enfants direct.
    $enfants = sql_allfetsel('id_rubrique, titre', table_objet_sql('rubrique'), 'id_parent='.intval($id_rubrique));

    // On va chercher un éventuel num_titre dans les titre
    foreach ($enfants as $index => $enfant) {
        $match = null;
        if (preg_match('#^[0-9]*\.#', $enfant['titre'], $match)) {
            $enfants[$index]['num_titre'] = intval($match[0]);
        }
    }

    // On simplifie le tableau pour n'avoir que des id
    $enfants = array_column($enfants, 'id_rubrique', 'num_titre');
    ksort($enfants);
    return $enfants;
}

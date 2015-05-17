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

	$code = "court_jus_calculer_rubrique ($id_rubrique)";
	$p->code = $code;
	$p->interdire_scripts = false;
	return $p;
}

function court_jus_calculer_rubrique($id_rubrique) {
    court_jus_trouver_objet($id_rubrique);
}

function court_jus_trouver_objet_rubrique() {
    // On va cherché les différent objets intaller sur SPIP
    $objets = lister_tables_objets_sql();

    // On va filtrer pour n'avoir que les objet avec un id_rubrique
    $objet_in_rubrique = array();
    foreach($objets as $table => $data) {
        // Si on trouve "id_rubrique" dans la liste des champs, on garde
        // On exclue la table des rubriques de SPIP
        if (array_key_exists('id_rubrique', $data['field']) and $table != table_objet_sql('rubrique')) {
            $objet_in_rubrique[] = $table;
        }
    }

    return $objet_in_rubrique;
}

function court_jus_trouver_objet($id_rubrique) {
    // On va compter le nombre d'objet présent dans la rubrique
    $tables = court_jus_trouver_objet_rubrique();

    // on va compter le nombre d'objet qu'il y a dans la rubrique.
    $objets_in_rubrique = array();
    foreach ($tables as $table) {
        $objet = table_objet($table);
        $champs_id = id_table_objet($table);
        $objets_rubrique = sql_allfetsel($champs_id, $table, 'id_rubrique='.intval($id_rubrique));
        foreach ($objets_rubrique as $objet_rubrique)
            $objets_in_rubrique[] = array('id_objet' => $objet_rubrique[$champs_id], 'objet' => $objet);
    }

    // Maintenant qu'on a le tableau des objets de la rubrique on compte
    $nb_objet = count($objets_in_rubrique);

    // Si on est à 0 objet, on descend dans une sous rubrique
    if ($nb_objet <= 0) {
        // Je sais pas encore comment par contre
    }
    // Un seul objet dans la rubrique, on renvoie le tableau
    elseif ($nb_objet = 1) {
        return generer_url_entite($objets_in_rubrique[0]['id_objet'], $objets_in_rubrique[0]['objet']);
    }

    // S'il y plusieurs objets dans la rubrique et que le mode "par num titre" est activé, on regiride sur le num titre le plus petit.

    // Sinon, si le mot "plus récent"" est activé on redirige sur l'article le plus récente.

}

court_jus_calculer_rubrique(22);
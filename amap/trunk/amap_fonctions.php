<?php
/**
 * Fonctions utiles au plugin Amap
 *
 * @plugin     Amap
 * @copyright  2013
 * @author     Pierre KUHN
 * @licence    GNU/GPL
 * @package    SPIP\Amap\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

//fonction qui permet de créer une rubrique
function create_rubrique($titre, $id_parent='0', $descriptif='') {
	$id_rubrique = find_rubrique($titre);
	if ($id_rubrique == 0) {
		$id_rubrique = sql_insertq(
			"spip_rubriques", array(
				"titre" => $titre,
				"id_parent" => $id_parent,
				"descriptif" => $descriptif
			)
		);
                sql_updateq(
                        "spip_rubriques", array(
                                "id_secteur" => $id_rubrique
                        ), "id_rubrique=$id_rubrique"
                );
                spip_log("1. (create_rubrique) rubrique cree : id = $id_rubrique, titre = $titre", "amap_installation");
        }
        else if ($id_rubrique > 0) {
                $id_rubrique = id_rubrique($titre);
                remplacer_rubrique($id_rubrique, $id_parent, $descriptif);
        }
        return $id_rubrique;
}

// fonction qui permet de trouver si une rubrique existe à partir du titre
function find_rubrique($titre) {
	$titre = addslashes($titre);
	$count = sql_countsel(
		"spip_rubriques",
		"titre = '$titre'"
	);
	return $count;
}

//fonction qui permet de trouver l'id d'une rubrique à partir du titre
function id_rubrique($titre) {
        $result = sql_fetsel(
                "id_rubrique",
                "spip_rubriques",
                "titre='$titre'"
        );
        $resultat = $result['id_rubrique'];
        spip_log("1. (id_rubrique) recherche de l'id_rubrique de $titre = $resultat", "amap_installation");
        return $resultat;
}

//fonction qui mets à jour une rubrique
function remplacer_rubrique($id_rubrique, $id_parent, $descriptif) {
        sql_updateq(
                "spip_rubriques", array(
                        "id_parent" => $id_parent,
                        "descriptif" => $descriptif
                ), "id_rubrique=$id_rubrique"
        );
        return true;
}
?>
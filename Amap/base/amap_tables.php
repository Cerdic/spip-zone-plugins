<?php

/**
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Config pris sur Soyezcreateurs
*
*/
include_spip('inc/meta');

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

// fonction qui permet de renommer une rubrique à partir du titre
function rename_rubrique($titre, $nouveau_titre) {
        $id_rubrique = id_rubrique($titre);
        if ($id_rubrique) {
                sql_updateq(
                        "spip_rubriques", array(
                                "titre" => $nouveau_titre
                        ), "id_rubrique=$id_rubrique"
                );
                spip_log("rename_rubrique) renommage de $titre en $nouveau_titre", "amap_installation");
        }
        return true;
}
       
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

//tables du plugins amap
function amap_declarer_tables_interfaces($interface){
	//-- Alias
	$interface['table_des_tables']['amap_livraisons'] = 'amap_livraisons';
	$interface['table_des_tables']['amap_paniers'] = 'amap_paniers';
	//-- filtre date
	$interface['table_date']['amap_paniers'] = 'date_distribution';
	//-- Savoit traiter "_ " en <br />
	$interface['table_des_traitements']['CONTENU_PANIER']['amap_livraisons'] = _TRAITEMENT_RACCOURCIS;
	return $interface;
}

//creation des tables
function amap_declarer_tables_principales($tables_principales){
	//-- Table amap_livraisons -------------------
	$spip_amap_livraisons = array(
		'id_amap_livraison'  => 'bigint NOT NULL AUTO_INCREMENT',
		'date_livraison'  => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		'contenu_panier'  => 'text DEFAULT "" NOT NULL',
		);
	$spip_amap_livraisons_key = array(
		'PRIMARY KEY'   => 'id_amap_livraison'
		);
	$tables_principales['spip_amap_livraisons'] = array(
		'field' => &$spip_amap_livraisons,
		'key' => &$spip_amap_livraisons_key,
		);

	//-- Table amap_paniers -------------------
	$spip_amap_paniers = array(
		'id_amap_panier'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_auteur'  => 'bigint NOT NULL',
		'date_distribution'  => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		);
	$spip_amap_paniers_key = array(
		'PRIMARY KEY'   => 'id_amap_panier'
		);
	$tables_principales['spip_amap_paniers'] = array(
		'field' => &$spip_amap_paniers,
		'key' => &$spip_amap_paniers_key,
		);

    return $tables_principales;
}

//creation de champs extra
function amap_declarer_champs_extras($champs = array()){
	// table auteur un champ adherent
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'type_adherent', // nom sql
		'label' => 'amap:type_adherent_auteur', // chaine de langue 'prefix:cle'
		'type' => 'menu-radio', // type de saisie
		'enum' => array(
			"adherent" => _T('amap:adherent'),
			"producteur" => _T('amap:producteur'),
		),
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	// table auteurs un champ adhésion
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'adhesion', // nom sql
		'label' => 'amap:adhesion_auteur', // chaine de langue 'prefix:cle'
		'type' => 'input', // type de saisie
		'sql' => "bigint NULL", // declaration sql
	));
	// table auteur un champ type_panier
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'type_panier', // nom sql
		'label' => 'amap:type_panier_auteur', // chaine de langue 'prefix:cle'
		'type' => 'menu-radio', // type de saisie
		'enum' => array(
			"petit" => _T('amap:petit'),
			"grand" => _T('amap:grand'),
		),
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>

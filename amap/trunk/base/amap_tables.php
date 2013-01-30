<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;
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
	$interface['table_des_tables']['amap_disponibles'] = 'amap_disponibles';
	$interface['table_des_tables']['amap_livraisons'] = 'amap_livraisons';
	$interface['table_des_tables']['amap_paniers'] = 'amap_paniers';
	$interface['table_des_tables']['amap_responsables'] = 'amap_responsables';
	//-- filtre date
	$interface['table_date']['amap_paniers'] = 'date_distribution';
	$interface['table_date']['amap_responsables'] = 'date_distribution';
	//-- Savoir traiter "_ " en <br />
	$interface['table_des_traitements']['INFO_SUPPLEMENTAIRE'][] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['CONTENU_PANIER'][] = _TRAITEMENT_RACCOURCIS;
	return $interface;
}

//creation des tables
function amap_declarer_tables_principales($tables_principales){
	//-- Table amap_disponibles -------------------
	$spip_amap_disponibles_field = array(
		'id_amap_disponible'	=> 'bigint(21) NOT NULL auto_increment',
		'id_amap_panier'  		=> 'bigint(21) NOT NULL',
		'type_disponibilite'	=> 'text',
		'info_supplementaire'	=> 'text',
		);
	$spip_amap_disponibles_key = array(
		'PRIMARY KEY'   => 'id_amap_disponible'
		);
	$tables_principales['spip_amap_disponibles'] = array(
		'field' => &$spip_amap_disponibles_field,
		'key' => &$spip_amap_disponibles_key,
		);

	//-- Table amap_livraisons -------------------
	$spip_amap_livraisons_field = array(
		'id_amap_livraison'	=> 'bigint(21) NOT NULL auto_increment',
		'date_livraison'  	=> 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		'contenu_panier'  	=> 'text',
		);
	$spip_amap_livraisons_key = array(
		'PRIMARY KEY'   => 'id_amap_livraison'
		);
	$tables_principales['spip_amap_livraisons'] = array(
		'field' => &$spip_amap_livraisons_field,
		'key' => &$spip_amap_livraisons_key,
		);

	//-- Table amap_paniers -------------------
	$spip_amap_paniers_field = array(
		'id_amap_panier'	=> 'bigint(21) NOT NULL auto_increment',
		'id_auteur'  		=> 'bigint(21) NOT NULL',
		'id_producteur' 	=> 'bigint(21) NOT NULL',
		'date_distribution' => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		'dispo'  			=> 'bigint(21) DEFAULT "0" NOT NULL',
		);
	$spip_amap_paniers_key = array(
		'PRIMARY KEY'   => 'id_amap_panier'
		);
	$tables_principales['spip_amap_paniers'] = array(
		'field' => &$spip_amap_paniers_field,
		'key' => &$spip_amap_paniers_key,
		);

	//-- Table amap_responsables -------------------
	$spip_amap_responsables_field = array(
		'id_amap_responsable'  	=> 'bigint(21) NOT NULL auto_increment',
		'id_auteur'  			=> 'bigint(21) NOT NULL',
		'date_distribution'  	=> 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		);
	$spip_amap_responsables_key = array(
		'PRIMARY KEY'   => 'id_amap_responsable'
		);
	$tables_principales['spip_amap_responsables'] = array(
		'field' => &$spip_amap_responsables_field,
		'key' => &$spip_amap_responsables_key,
		);

    return $tables_principales;
}

//creation de champs extra
function amap_declarer_champs_extras($champs = array()){
	// type d'adhérent
	$champs['spip_auteurs']['type_adherent'] = array(
		'saisie' => 'radio', // Type du champ (voir plugin Saisies)
		'options' => array(
				'nom' => 'type_adherent',
				'label' => _T('amap:type_adherent_auteur'),
				'sql' => "varchar(15) DEFAULT ''",
				'defaut' => '', // Valeur par défaut
				'restrictions'=>array(
						'voir' => array('auteur' => ''), // Tout le monde peut voir
						'modifier' => array('auteur' => ''), // Seuls les auteurs peuvent modifier
				),
				'datas' => array(
						'adherent' => _T('amap:adherent'),
						'producteur' => _T('amap:producteur'),
				),
		),
	);

	// l'adhésion
	$champs['spip_auteurs']['adhesion'] = array(
		'saisie' => 'input',//Type du champ (voir plugin Saisies)
		'options' => array(
				'nom' => 'adhesion',
				'label' => _T('amap:adhesion_auteur'),
				'sql' => "bigint(21) NULL", // declaration sql
				'defaut' => '',// Valeur par défaut
				'restrictions'=>array(
						'voir' => array('auteur' => ''), // Tout le monde peut voir
						'modifier' => array('auteur' => ''), // Seuls les auteurs peuvent modifier
				),
		),
	);

	// type de panier
	$champs['spip_auteurs']['type_panier'] = array(
		'saisie' => 'radio',//Type du champ (voir plugin Saisies)
		'options' => array(
				'nom' => 'type_panier',
				'label' => _T('amap:type_panier_auteur'),
				'sql' => "varchar(10) DEFAULT ''", // declaration sql
				'defaut' => '',// Valeur par défaut
				'restrictions'=>array(
						'voir' => array('auteur' => ''), // Tout le monde peut voir
						'modifier' => array('auteur' => ''), // Seuls les auteurs peuvent modifier
				),
				'datas' => array(
						'petit' => _T('amap:petit'),
						'grand' => _T('amap:grand'),
				)
		)
	);

	return $champs;
}
?>

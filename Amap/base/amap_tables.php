<?php

/**
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Config pris sur Soyezcreateurs
*
*/
include_spip('inc/meta');

// activés les mots clef pour les articles
function amap_config_site() {	
	ecrire_meta('articles_mots', 'oui','non');
	spip_log("1. (amap_config_site) metas du plugins ecrite", "amap_installation");
	return true;
}

// fonction qui permet de trouver si un groupe de mots clés existe à partir du titre
function find_groupe($titre) {
	$titre = addslashes($titre);
	spip_log("1. (find_groupe) recherche des occurences dans la table spip_groupes_mots de l'id de : $titre", "amap_installation");
	$count = sql_countsel("spip_groupes_mots", "titre='$titre'");
	spip_log("2. (find_groupe) resultat de la recherche : $count occurences pour $titre", "amap_installation");
	return $count;
}

// fonction pour trouver l'id du groupe de mots clés à partir du titre du groupe
function id_groupe($titre) {
	$titre = addslashes($titre);
	spip_log("1. (id_groupe) selection dans la table spip_groupes_mots de l'id de : $titre", "amap_installation");
	$result = sql_fetsel("id_groupe", "spip_groupes_mots", "titre='$titre'");
	$resultat = $result['id_groupe'];
	spip_log("2. (id_groupe) selection = $resultat pour $titre", "amap_installation");
	return $resultat;
}

//fonction qui mets à jour un groupe de mots clés
function remplacer_groupe($titre, $descriptif, $texte, $unseul, $obligatoire, $tables_liees, $minirezo, $comite, $forum) {
	$id_groupe = id_groupe($titre);
	sql_updateq(
		"spip_groupes_mots", array(
			"descriptif" => $descriptif,
			"texte" => $texte,
			"unseul" => $unseul,
			"obligatoire" => $obligatoire,
			"tables_liees" => $tables_liees,
			"minirezo" => $minirezo,
			"comite" => $comite,
			"forum" => $forum
		), "id_groupe=$id_groupe"
	);
	return true;
}

//fonction qui permet de créer un groupe de mots clés
function create_groupe($groupe, $descriptif='', $texte='', $unseul='non', $obligatoire='non', $articles='oui', $breves='non', $rubriques='non', $syndic='non', $evenements='non', $minirezo='oui', $comite='oui', $forum='non') {
	$id_groupe = find_groupe($groupe);
	$tables_liees = '';
	if ($articles == 'oui') 
		$tables_liees.='articles,';
	if ($breves == 'oui') 
		$tables_liees.='breves,';
	if ($rubriques == 'oui') 
		$tables_liees.='rubriques,';
	if ($syndic == 'oui') 
		$tables_liees.='syndic,';
	if ($evenements == 'oui') 
		$tables_liees.='evenements,';
	spip_log("1. (create_groupe) pret a creer groupe : titre = $groupe. retour de find_groupe = $id_groupe", "amap_installation");
	if ($id_groupe == 0) {
		$id_insert = sql_insertq(
			"spip_groupes_mots", array(
				"id_groupe" => '',
				"titre" => $groupe,
				"descriptif" => $descriptif,
				"texte" => $texte,
				"unseul" => $unseul,
				"obligatoire" => $obligatoire,
				"tables_liees" => $tables_liees,
				"minirezo" => $minirezo,
				"comite" => $comite,
				"forum" => $forum
			)
		);
		spip_log("2. (create_groupe) retour de find_groupe : $id_groupe, donc insertion avec id = $id__insert et titre = $groupe", "amap_installation");
	} 
	else if ($id_groupe > 0) {
		$id_insert = remplacer_groupe($groupe, $descriptif, $texte, $unseul, $obligatoire, $tables_liees, $minirezo, $comite, $forum);
		spip_log("2. (create_groupe) retour de find_groupe : $id_groupe... passage a remplacer_groupe", "amap_installation");
	}
	return $id_insert;
}

function supprimer_mot_groupe($nom_groupe,$nom_mot) {
	$id_groupe = id_groupe($nom_groupe);
	$id_mot = id_mot($nom_mot, $id_groupe);
	if ($id_mot>0) {
		sql_delete("spip_mots", "id_mot=$id_mot");
		sql_delete("spip_mots_articles", "id_mot=$id_mot");
		sql_delete("spip_mots_rubriques", "id_mot=$id_mot");
		sql_delete("spip_mots_syndic", "id_mot=$id_mot");
		sql_delete("spip_mots_forum", "id_mot=$id_mot");
	}
}

function vider_groupe($nom_groupe) {
	$id_groupe = id_groupe($nom_groupe);
	if ($id_groupe>0) {
		$id_mots = sql_select('id_mot',  'spip_mots',  'id_groupe='.sql_quote($id_groupe));
		while($id_mot = sql_fetch($id_mots)){
			sql_delete("spip_mots", "id_mot=".$id_mot['id_mot']);
			sql_delete("spip_mots_articles", "id_mot=".$id_mot['id_mot']);
			sql_delete("spip_mots_rubriques", "id_mot=".$id_mot['id_mot']);
			sql_delete("spip_mots_syndic", "id_mot=".$id_mot['id_mot']);
			sql_delete("spip_mots_forum", "id_mot=".$id_mot['id_mot']);
		}
		sql_delete("spip_groupes_mots", "id_groupe=$id_groupe");
	}
}

// fonction qui permet de trouver si un mot clé existe à partir du titre et de l'id du groupe
function find_mot($titre, $id_groupe) {
	$titre = addslashes($titre);
	$count = sql_countsel(
		"spip_mots", 
		"titre = '$titre' AND id_groupe = $id_groupe"
	);
	return $count;
}

//fonction qui permet de trouver l'id du mot clé à partir du titre et de l'id du groupe
function id_mot($titre, $id_groupe) {
	spip_log("1. (id_mot) debut de recherche de l'id de $titre avec $id_groupe", "amap_installation");
	$titre = addslashes($titre);
	$result = sql_fetsel(
		"id_mot", 
		"spip_mots", 
		"titre='$titre' AND id_groupe = $id_groupe"
	);
	$id_mot = $result['id_mot'];
	spip_log("2. (id_mot) retour de la fonction id_mot = $id_mot", "amap_installation");
	return $id_mot;
}

//fonction qui permet de créer un mot clé
function create_mot($groupe, $mot, $descriptif='', $texte='') {
	$id_groupe = id_groupe($groupe);
	$find_mot = find_mot($mot, $id_groupe);
	if ($find_mot == 0) {
		spip_log("1. (create_mot) debut create_mot. mot inexistant donc creation : $id_groupe - $mot", "amap_installation");
		$motcle = sql_insertq(
			"spip_mots", array(
				"id_mot" => '',
				"titre" => $mot,
				"descriptif" => $descriptif,
				"texte" => $texte,
				"id_groupe" => $id_groupe,
				"type" => $groupe
			)
		);
		spip_log("2. (create_mot) mot cle $mot insere sous l'id $motcle dans la table avec groupe = $id_groupe", "amap_installation");
	}
	else if ($find_mot > 0) {
		$id_mot = id_mot($mot, $id_groupe);
		spip_log("1. (create_mot) mise a jour dans la table du mot cle : $mot", "amap_installation");
		remplacer_mot($id_mot, $descriptif, $texte, $id_groupe, $groupe);
	}
	else {
		spip_log("insertion impossible ! debug : groupe = $groupe --- id_groupe = $id_groupe", "amap_installation");
	}
}

//fonction qui permet de mettre à jour un mot clé 
function remplacer_mot($id_mot, $descriptif, $texte, $id_groupe, $groupe) {
	sql_updateq(
			"spip_mots", array(
				"descriptif" => $descriptif,
				"texte" => $texte,
				"id_groupe" => $id_groupe,
				"type" => $groupe
			), "id_mot=$id_mot"
		);
	return true;
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
	$interface['table_des_tables']['paniers'] = 'paniers';
	return $interface;
}

function amap_declarer_tables_principales($tables_principales){

	//-- Table paniers -------------------
	$spip_paniers = array(
		'id_panier'  => 'bigint NOT NULL AUTO_INCREMENT',
		'nom'  => 'text NOT NULL',
		'prenom'  => 'text NOT NULL',
		'date_distribution'  => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		);
	$spip_paniers_key = array(
		'PRIMARY KEY'   => 'id_panier'
		);
	$tables_principales['spip_paniers'] = array(
		'field' => &$spip_paniers,
		'key' => &$spip_paniers_key,
		);

    return $tables_principales;
}

//creation des mots clef pour le plugins
function amap_config_motsclefs() {
	//les groupes puis mots
	create_groupe("_Amap_config", "Groupe pour configurer le plugin", "C'est mots clef vous servirons a configurer le plugins et créé les saisons.", 'non', 'non', 'oui', 'non', 'non', 'non', 'non', 'oui', 'non', 'non');
		create_mot("_Amap_config", "amap_agenda", "Mettre ce mot clef à l'article de l'agenda", "");
		create_mot("_Amap_config", "amap_contrat", "Mettre ce mot clef à l'article du contrat", "");
		create_mot("_Amap_config", "amap_sortie", "Mettre ce mot clef à l'article de la sortie", "");
		create_mot("_Amap_config", "amap_responsable", "Mettre ce mot clef à l'article de la responsabilite", "");
		create_mot("_Amap_config", "amap_vacance", "Mettre ce mot clef à l'article des vacances", "");
	return true;
}

//creation de champs extra sur la table auteurs
function amap_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'adhesion', // nom sql
		'label' => 'amap:adhesion', // chaine de langue 'prefix:cle'
		'precisions' => '', // precisions sur le champ
		'obligatoire' => false, // 'oui' ou '' (ou false)
		'rechercher' => false, // false, ou true ou directement la valeur de ponderation (de 1 à 8 generalement)
		'type' => 'input', // type de saisie
		'sql' => "bigint NULL", // declaration sql
	));
	$champs[] = new ChampExtra(array(
		'table' => 'auteurs', // sur quelle table ?
		'champ' => 'type_panier', // nom sql
		'label' => 'amap:type_panier', // chaine de langue 'prefix:cle'
		'type' => 'menu-radio', // type de saisie
		'enum' => array(
			"petit" => "Petit",
			"grand" => "Grand",
		),
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>

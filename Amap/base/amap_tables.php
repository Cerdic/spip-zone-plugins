<?php

/**
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Config pris sur Soyezcreateurs
*
*/
include_spip('inc/meta');

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

function amap_declarer_tables_interfaces($interface){
	//-- Alias
	$interface['table_des_tables']['amap_banques'] = 'amap_banques';
	$interface['table_des_tables']['amap_contrats'] = 'amap_contrats';
	$interface['table_des_tables']['amap_evenements'] = 'amap_evenements';
	$interface['table_des_tables']['amap_famille_varietes'] = 'amap_famille_varietes';
	$interface['table_des_tables']['amap_lieux'] = 'amap_lieux';
	$interface['table_des_tables']['amap_paniers'] = 'amap_paniers';
	$interface['table_des_tables']['amap_participation_sorties'] = 'amap_participation_sorties';
	$interface['table_des_tables']['amap_personnes'] = 'amap_personnes';
	$interface['table_des_tables']['amap_prix'] = 'amap_prix';
	$interface['table_des_tables']['amap_produits'] = 'amap_produits';
	$interface['table_des_tables']['amap_produits_distributions'] = 'amap_produits_distributions';
	$interface['table_des_tables']['amap_reglements'] = 'amap_reglemenst';
	$interface['table_des_tables']['amap_saisons'] = 'amap_saisons';
	$interface['table_des_tables']['amap_sorties'] = 'amap_sorties';
	$interface['table_des_tables']['amap_types_contrats'] = 'amap_types_contrats';
	$interface['table_des_tables']['amap_vacances'] = 'amap_vacances';
	$interface['table_des_tables']['amap_varietes'] = 'amap_varietes';
	return $interface;
}

function amap_declarer_tables_principales($tables_principales){
 	//-- Table banques -------------------
	$spip_amap_banques = array(
		'id_banque'  => 'bigint NOT NULL AUTO_INCREMENT',
		'label_banque'  => 'varchar(50) NOT NULL'
		);
	$spip_amap_banques_key = array(
		'PRIMARY KEY'   => 'id_banque'
		);
	$tables_principales['spip_amap_banques'] = array(
		'field' => &$spip_amap_banques,
		'key' => &$spip_amap_banques_key,
		);

	//-- Table contrats -------------------
	$spip_amap_contrats = array(
		'id_contrat'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_produit'  => 'bigint NOT NULL',
		'id_saison'  => 'bigint NOT NULL',
		'id_personne'  => 'bigint NOT NULL',
		'id_type'  => 'bigint NOT NULL',
		'demi_panier'  => 'boolean NULL',
		'debut_contrat'  => 'bigint NULL',
		'nb_distribution'  => 'bigint NULL'
		);
	$spip_amap_contrats_key = array(
		'PRIMARY KEY'   => 'id_contrat'
		);
	$tables_principales['spip_amap_contrats'] = array(
		'field' => &$spip_amap_contrats,
		'key' => &$spip_amap_contrats_key,
		'join' => array('id_produit'=>'id_produit','id_saison'=>'id_saison','id_personne'=>'id_personne','id_type'=>'id_type','debut_contrat'=>'id_evenement')
		);

	//-- Table evenements -------------------
	$spip_amap_evenements = array(
		'id_evenement'  => 'bigint NOT NULL AUTO_INCREMENT',
		'date_evenement'  => 'datetime DEFAULT "0000-00-00" NOT NULL',
		'id_saison'  => 'bigint NOT NULL',
		'id_lieu'  => 'varchar(40) NULL',
		'id_personne1'  => 'varchar(60) NULL',
		'id_personne2'  => 'varchar(60) NULL',
		'id_personne3'  => 'varchar(60) NULL'
		);
	$spip_amap_evenements_key = array(
		'PRIMARY KEY'   => 'id_evenement'
		);
	$tables_principales['spip_amap_evenements'] = array(
		'field' => &$spip_amap_evenements,
		'key' => &$spip_amap_evenements_key,
		'join' => array('id_saison'=>'id_saison','id_lieu'=>'id_lieu','id_personne1'=>'id_personne','id_personne2'=>'id_personne','id_personne3'=>'id_personne')
		);

	//-- Table famille_varietes -------------------
	$spip_amap_famille_varietes = array(
		'id_famille'  => 'bigint NOT NULL AUTO_INCREMENT',
		'label_famille'  => 'varchar(30) NOT NULL',
		'id_produit'  => 'bigint NOT NULL',
		);
	$spip_amap_famille_varietes_key = array(
		'PRIMARY KEY'   => 'id_famille'
		);
	$tables_principales['spip_amap_famille_varietes'] = array(
		'field' => &$spip_amap_famille_varietes,
		'key' => &$spip_amap_famille_varietes_key,
		'join' => array('id_produit'=>'id_produit')
		);

	//-- Table lieux -------------------
	$spip_amap_lieux = array(
		'id_lieu'  	=> 'bigint NOT NULL AUTO_INCREMENT',
		'lieux_nom' 	=> 'varchar(40) NOT NULL',
		'lieux_rue' 	=> 'varchar(40) NULL',
		'lieux_cp'  	=> 'varchar(5) NULL',
		'lieux_ville' => 'varchar(30) NULL',
		'lieux_telephone'    => 'varchar(13) NULL'
		);
	$spip_amap_lieux_key = array(
		'PRIMARY KEY'   => 'id_lieu'
		);
	$tables_principales['spip_amap_lieux'] = array(
		'field' => &$spip_amap_lieux,
		'key' => &$spip_amap_lieux_key,
		);

	//-- Table paniers -------------------
	$spip_amap_paniers = array(
		'id_produit'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_evenement'  => 'bigint NOT NULL',
		'id_element'  => 'bigint NOT NULL',
		'id_famille'  => 'bigint NOT NULL',
		'id_variete'  => 'bigint NULL',
		'quantite'  => 'bigint NULL',
		'poids'  => 'varchar(6) NULL'
		);
	$spip_amap_paniers_key = array(
		'PRIMARY KEY'   => 'id_produit, id_evenement, id_element'
		);
	$tables_principales['spip_amap_paniers'] = array(
		'field' => &$spip_amap_paniers,
		'key' => &$spip_amap_paniers_key,
		'join' => array('id_produit'=>'id_produit','id_evenement'=>'id_evenement','id_famille'=>'id_famille','id_variete'=>'id_variete')
		);

	//-- Table participation_sorties -------------------
	$spip_amap_participation_sorties = array(
		'id_sortie'  => 'bigint NOT NULL',
		'id_personne'  => 'bigint NOT NULL'
		);
	$spip_amap_participation_sorties_key = array(
		'PRIMARY KEY'   => 'id_sortie,id_personne'
		);
	$tables_principales['spip_amap_participation_sorties'] = array(
		'field' => &$spip_amap_participation_sorties,
		'key' => &$spip_amap_participation_sorties_key,
		'join' => array('id_sortie'=>'id_sortie','id_personne'=>'id_personne')
		);

	//-- Table personnes -------------------
	$spip_amap_personnes = array(
		'id_personne'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_auteur'  => 'bigint NULL',
		'prenom'  => 'varchar(20) NULL',
		'nom'  => 'varchar(30) NOT NULL',
		'tel_fixe'  => 'varchar(13) NULL',
		'tel_portable'  => 'varchar(13) NULL',
		'adhesion'  => 'bigint NULL'
		);
	$spip_amap_personnes_key = array(
		'PRIMARY KEY'   => 'id_personne'
		);
	$tables_principales['spip_amap_personnes'] = array(
		'field' => &$spip_amap_personnes,
		'key' => &$spip_amap_personnes_key,
		);

	//-- Table prix -------------------
	$spip_amap_prix = array(
		'id_produit'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_saison'  => 'bigint NOT NULL',
		'id_type'  => 'bigint NOT NULL',
		'prix_distribution'  => 'bigint NOT NULL'
		);
	$spip_amap_prix_key = array(
		'PRIMARY KEY'   => 'id_produit,id_saison,id_type'
		);
	$tables_principales['spip_amap_prix'] = array(
		'field' => &$spip_amap_prix,
		'key' => &$spip_amap_prix_key,
		'join' => array('id_produit'=>'id_produit','id_saison'=>'id_saison','id_type'=>'id_type')
		);

	//-- Table produits -------------------
	$spip_amap_produits = array(
		'id_produit'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_auteur'  => 'bigint NULL',
		'label_produit'  => 'varchar(20) NOT NULL',
		);
	$spip_amap_produits_key = array(
		'PRIMARY KEY'   => 'id_produit'
		);
	$tables_principales['spip_amap_produits'] = array(
		'field' => &$spip_amap_produits,
		'key' => &$spip_amap_produits_key,
		'join' => array('id_auteur'=>'id_personne')
		);

	//-- Table produits_distributions -------------------
  	$spip_amap_produits_distributions = array(
		'id_evenement'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_produit'  => 'bigint NOT NULL'
		);
	$spip_amap_produits_distributions_key = array(
		'PRIMARY KEY'   => 'id_evenement,id_produit'
		);
	$tables_principales['spip_amap_produits_distributions'] = array(
		'field' => &$spip_amap_produits_distributions,
		'key' => &$spip_amap_produits_distributions_key,
		'join' => array('id_evenement'=>'id_evenement','id_produit'=>'id_produit')
		);

	//-- Table reglements -------------------
	$spip_amap_reglements = array(
		'id_cheque'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_contrat'  => 'bigint NOT NULL',
		'id_banque'  => 'bigint NULL',
		'ref_cheque'  => 'varchar(12) NULL',
		'montant_euros'  => 'varchar(4) NOT NULL'
		);
	$spip_amap_reglements_key = array(
		'PRIMARY KEY'   => 'id_cheque'
		);
	$tables_principales['spip_amap_reglements'] = array(
		'field' => &$spip_amap_reglements,
		'key' => &$spip_amap_reglements_key,
		'join' => array('id_contrat'=>'id_contrat','id_banque'=>'id_banque')
		);

	//-- Table saisons -------------------
	$spip_amap_saisons = array(
		'id_saison'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_agenda'  => 'text DEFAULT NULL',
		'id_contrat'  => 'text DEFAULT NULL',
		'id_sortie'  => 'text DEFAULT NULL',
		'id_responsable'  => 'text DEFAULT NULL',
		'id_vacance'  => 'text DEFAULT NULL'
		);
	$spip_amap_saisons_key = array(
		'PRIMARY KEY'   => 'id_saison'
		);
	$tables_principales['spip_amap_saisons'] = array(
		'field' => &$spip_amap_saisons,
		'key' => &$spip_amap_saisons_key,
		);

	//-- Table sorties -------------------
	$spip_amap_sorties = array(
		'id_sortie'  => 'bigint NOT NULL AUTO_INCREMENT',
		'date_sortie'  => 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL',
		'id_saison'  => 'bigint NOT NULL',
		'id_produit'  => 'bigint NOT NULL',
		);
	$spip_amap_sorties_key = array(
		'PRIMARY KEY'   => 'id_sortie'
		);
	$tables_principales['spip_amap_sorties'] = array(
		'field' => &$spip_amap_sorties,
		'key' => &$spip_amap_sorties_key,
		'join' => array('id_saison'=>'id_saison','id_produit'=>'id_produit')
		);

	//-- Table types_contrats -------------------
	$spip_amap_types_contrats = array(
		'id_type_contrat'  => 'bigint NOT NULL AUTO_INCREMENT',
		'label_type_contrat' => 'varchar(20) NOT NULL'
		);
	$spip_amap_types_contrats_key = array(
		'PRIMARY KEY'   => 'id_type_contrat'
		);
	$tables_principales['spip_amap_types_contrats'] = array(
		'field' => &$spip_amap_types_contrats,
		'key' => &$spip_amap_types_contrats_key,
		);

	//-- Table vacances -------------------
	$spip_amap_vacances = array(
		'id_contrat'  => 'bigint NOT NULL',
		'id_evenement'  => 'bigint NOT NULL',
		'id_remplacant'  => 'bigint DEFAULT NULL',
		'remplacant_ext'  => 'varchar(150) DEFAULT NULL',
		'paye'  => 'boolean NOT NULL'
		);
	$spip_amap_vacances_key = array(
		'PRIMARY KEY'   => 'id_contrat,id_evenement'
		);
	$tables_principales['spip_amap_vacances'] = array(
		'field' => &$spip_amap_vacances,
		'key' => &$spip_amap_vacances_key,
		'join' => array('id_contrat'=>'id_contrat','id_evenement'=>'id_evenement','id_remplacant'=>'id_personne')
		);

	//-- Table varietes -------------------
	$spip_amap_varietes = array(
		'id_variete'  => 'bigint NOT NULL AUTO_INCREMENT',
		'id_famille'  => 'bigint NOT NULL',
		'label_variete'  => 'varchar(30) NOT NULL',
		);
	$spip_amap_varietes_key = array(
		'PRIMARY KEY'   => 'id_variete'
		);
	$tables_principales['spip_amap_varietes'] = array(
		'field' => &$spip_amap_varietes,
		'key' => &$spip_amap_varietes_key,
		'join' => array('id_famille'=>'id_famille')
		);
    return $tables_principales;
}

//fonction qui permet de créer le tout
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
?>

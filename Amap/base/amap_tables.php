<?php

/**
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Config pris sur Soyezcreateurs
*
*/
include_spip('inc/meta');

//fonction qui permet de créer les métas de config du site
function amap_config_site() {
	ecrire_meta('config_precise_groupes', 'oui','non');
	ecrire_meta('articles_mots', 'oui','non');
	return true;
}

// GROUPE
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

// fonction qui suprime des mots clef
function supprimer_mot_groupe($nom_groupe,$nom_mot) {
	$id_groupe = id_groupe($nom_groupe);
	if ($id_groupe>0) {
		$id_mot = id_mot($nom_mot, $id_groupe);
		if ($id_mot>0) {
			sql_delete("spip_mots", "id_mot=$id_mot");
			sql_delete("spip_mots_articles", "id_mot=$id_mot");
			sql_delete("spip_mots_rubriques", "id_mot=$id_mot");
			sql_delete("spip_mots_syndic", "id_mot=$id_mot");
			sql_delete("spip_mots_forum", "id_mot=$id_mot");
		}
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

// MOT
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
	$interface['table_des_tables']['amap_banque'] = 'amap_banque';
	$interface['table_des_tables']['amap_contrat'] = 'amap_contrat';
	$interface['table_des_tables']['amap_evenements'] = 'amap_evenements';
	$interface['table_des_tables']['amap_famille_variete'] = 'amap_famille_variete';
	$interface['table_des_tables']['amap_lieu'] = 'amap_lieu';
	$interface['table_des_tables']['amap_panier'] = 'amap_panier';
	$interface['table_des_tables']['amap_participation_sortie'] = 'amap_participation_sortie';
	$interface['table_des_tables']['amap_personne'] = 'amap_personne';
	$interface['table_des_tables']['amap_prix'] = 'amap_prix';
	$interface['table_des_tables']['amap_produit'] = 'amap_produit';
	$interface['table_des_tables']['amap_produit_distribution'] = 'amap_produit_distribution';
	$interface['table_des_tables']['amap_reglement'] = 'amap_reglement';
	$interface['table_des_tables']['amap_saison'] = 'amap_saison';
	$interface['table_des_tables']['amap_sortie'] = 'amap_sortie';
	$interface['table_des_tables']['amap_type_contrat'] = 'amap_type_contrat';
	$interface['table_des_tables']['amap_vacance'] = 'amap_vacance';
	$interface['table_des_tables']['amap_variete'] = 'amap_variete';
	return $interface;
}

function amap_declarer_tables_principales($tables_principales){
 	//-- Table banque -------------------
	$spip_amap_banque = array(
		'id_banque'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'label_banque'  => 'VARCHAR(50) NOT NULL'
		);
	$spip_amap_banque_key = array(
		'PRIMARY KEY'   => 'id_banque'
		);
	$tables_principales['spip_amap_banque'] = array(
		'field' => &$spip_amap_banque,
		'key' => &$spip_amap_banque_key,
		);

	//-- Table contrat -------------------
	$spip_amap_contrat = array(
		'id_contrat'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_produit'  => 'BIGINT(20) NOT NULL',
		'id_saison'  => 'BIGINT(20) NOT NULL',
		'id_personne'  => 'BIGINT(20) NOT NULL',
		'id_type'  => 'BIGINT(20) NOT NULL',
		'demi_panier'  => 'BIGINT(20) NULL',
		'debut_contrat'  => 'BIGINT(20) NULL',
		'nb_distribution'  => 'BIGINT(20) NULL'
		);
	$spip_amap_contrat_key = array(
		'PRIMARY KEY'   => 'id_contrat'
		);
	$tables_principales['spip_amap_contrat'] = array(
		'field' => &$spip_amap_contrat,
		'key' => &$spip_amap_contrat_key,
		'join' => array('id_produit'=>'id_produit','id_saison'=>'id_saison','id_personne'=>'id_personne','id_type'=>'id_type','debut_contrat'=>'id_evenement')
		);

	//-- Table evenements -------------------
	$spip_amap_evenements = array(
		'id_evenement'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'date_evenement'  => 'BIGINT(20) NULL',
		'id_saison'  => 'BIGINT(30) NOT NULL',
		'id_lieu'  => 'BIGINT(13) NULL',
		'id_personne1'  => 'BIGINT(20) NULL',
		'id_personne2'  => 'BIGINT(20) NULL',
		'id_personne3'  => 'BIGINT(20) NULL'
		);
	$spip_amap_evenements_key = array(
		'PRIMARY KEY'   => 'id_evenement'
		);
	$tables_principales['spip_amap_evenements'] = array(
		'field' => &$spip_amap_evenements,
		'key' => &$spip_amap_evenements_key,
		'join' => array('id_saison'=>'id_saison','id_lieu'=>'id_lieu','id_personne1'=>'id_personne','id_personne2'=>'id_personne','id_personne3'=>'id_personne1')
		);

	//-- Table famille_variete -------------------
	$spip_amap_famille_variete = array(
		'id_famille'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'label_famille'  => 'VARCHAR(30) NOT NULL',
		'id_produit'  => 'BIGINT(20) NOT NULL',
		);
	$spip_amap_famille_variete_key = array(
		'PRIMARY KEY'   => 'id_famille'
		);
	$tables_principales['spip_amap_famille_variete'] = array(
		'field' => &$spip_amap_famille_variete,
		'key' => &$spip_amap_famille_variete_key,
		'join' => array('id_produit'=>'id_produit')
		);

	//-- Table lieu -------------------
	$spip_amap_lieu = array(
		'id_lieu'  	=> 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'nom_lieu' 	=> 'VARCHAR(40) NOT NULL',
		'rue_lieu' 	=> 'VARCHAR(40) NOT NULL',
		'cp_lieu'  	=> 'VARCHAR(5) NULL',
		'ville_lieu' => 'VARCHAR(30) NOT NULL',
		'telephone_lieu'    => 'VARCHAR(13) NULL'
		);
	$spip_amap_lieu_key = array(
		'PRIMARY KEY'   => 'id_lieu'
		);
	$tables_principales['spip_amap_lieu'] = array(
		'field' => &$spip_amap_lieu,
		'key' => &$spip_amap_lieu_key,
		);

	//-- Table panier -------------------
	$spip_amap_panier = array(
		'id_produit'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_evenement'  => 'BIGINT(20) NOT NULL',
		'id_element'  => 'BIGINT(20) NOT NULL',
		'id_famille'  => 'BIGINT(20) NOT NULL',
		'id_variete'  => 'BIGINT(20) NOT NULL',
		'quantite'  => 'BIGINT(20) NOT NULL',
		'poids'  => 'VARCHAR(6) NULL'
		);
	$spip_amap_panier_key = array(
		'PRIMARY KEY'   => 'id_produit, id_evenement, id_element'
		);
	$tables_principales['spip_amap_panier'] = array(
		'field' => &$spip_amap_panier,
		'key' => &$spip_amap_panier_key,
		'join' => array('id_produit'=>'id_produit','id_evenement'=>'id_evenement','id_famille'=>'id_famille','id_variete'=>'id_variete')
		);

	//-- Table participation_sortie -------------------
	$spip_amap_participation_sortie = array(
		'id_sortie'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_personne'  => 'BIGINT(20) NOT NULL'
		);
	$spip_amap_participation_sortie_key = array(
		'PRIMARY KEY'   => 'id_sortie,id_personne'
		);
	$tables_principales['spip_amap_participation_sortie'] = array(
		'field' => &$spip_amap_participation_sortie,
		'key' => &$spip_amap_participation_sortie_key,
		'join' => array('id_sortie'=>'id_sortie','id_personne'=>'id_personne')
		);

	//-- Table personne -------------------
	$spip_amap_personne = array(
		'id_personne'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'prenom'  => 'VARCHAR(20) NULL',
		'nom'  => 'VARCHAR(30) NOT NULL',
		'fixe'  => 'VARCHAR(13) NULL',
		'portable'  => 'VARCHAR(13) NULL',
		'adhesion'  => 'BIGINT(4) NULL'
		);
	$spip_amap_personne_key = array(
		'PRIMARY KEY'   => 'id_personne'
		);
	$tables_principales['spip_amap_personne'] = array(
		'field' => &$spip_amap_personne,
		'key' => &$spip_amap_personne_key,
		);

	//-- Table prix -------------------
	$spip_amap_prix = array(
		'id_produit'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_saison'  => 'BIGINT(20) NOT NULL',
		'id_type'  => 'BIGINT(20) NOT NULL',
		'prix_distribution'  => 'BIGINT(20) NOT NULL'
		);
	$spip_amap_prix_key = array(
		'PRIMARY KEY'   => 'id_produit,id_saison,id_type'
		);
	$tables_principales['spip_amap_prix'] = array(
		'field' => &$spip_amap_prix,
		'key' => &$spip_amap_prix_key,
		'join' => array('id_produit'=>'id_produit','id_saison'=>'id_saison','id_type'=>'id_type')
		);

	//-- Table produit -------------------
	$spip_amap_produit = array(
		'id_produit'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_paysan'  => 'BIGINT(20) NOT NULL',
		'label_produit'  => 'VARCHAR(20) NOT NULL',
		);
	$spip_amap_produit_key = array(
		'PRIMARY KEY'   => 'id_produit'
		);
	$tables_principales['spip_amap_produit'] = array(
		'field' => &$spip_amap_produit,
		'key' => &$spip_amap_produit_key,
		'join' => array('id_paysan'=>'id_personne')
		);

	//-- Table produit_distribution -------------------
  	$spip_amap_produit_distribution = array(
		'id_evenement'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_produit'  => 'BIGINT(20) NOT NULL'
		);
	$spip_amap_produit_distribution_key = array(
		'PRIMARY KEY'   => 'id_evenement,id_produit'
		);
	$tables_principales['spip_amap_produit_distribution'] = array(
		'field' => &$spip_amap_produit_distribution,
		'key' => &$spip_amap_produit_distribution_key,
		'join' => array('id_evenement'=>'id_evenement','id_produit'=>'id_produit')
		);

	//-- Table reglement -------------------
	$spip_amap_reglement = array(
		'id_cheque'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_contrat'  => 'BIGINT(20) NOT NULL',
		'id_banque'  => 'BIGINT(20) NULL',
		'ref_cheque'  => 'VARCHAR(12) NULL',
		'montant_euros'  => 'VARCHAR(4) NOT NULL'
		);
	$spip_amap_reglement_key = array(
		'PRIMARY KEY'   => 'id_cheque'
		);
	$tables_principales['spip_amap_reglement'] = array(
		'field' => &$spip_amap_reglement,
		'key' => &$spip_amap_reglement_key,
		'join' => array('id_contrat'=>'id_contrat','id_banque'=>'id_banque')
		);

	//-- Table saison -------------------
	$spip_amap_saison = array(
		'id_saison'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_agenda'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'id_contrat'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'id_sortie'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'id_responsable'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'id_vacance'  => 'BIGINT(20) DEFAULT "0" NOT NULL'
		);
	$spip_amap_saison_key = array(
		'PRIMARY KEY'   => 'id_saison'
		);
	$tables_principales['spip_amap_saison'] = array(
		'field' => &$spip_amap_saison,
		'key' => &$spip_amap_saison_key,
		);

	//-- Table sortie -------------------
	$spip_amap_sortie = array(
		'id_sortie'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'date_sortie'  => 'DATETIME DEFAULT "0000-00-00 00:00:00" NOT NULL',
		'id_saison'  => 'BIGINT(20) NOT NULL',
		'id_produit'  => 'BIGINT(20) NOT NULL',
		'id_variete'  => 'BIGINT(20) NOT NULL',
		'quantite'  => 'BIGINT(20) NOT NULL',
		'poids'  => 'BIGINT(20) NULL'
		);
	$spip_amap_sortie_key = array(
		'PRIMARY KEY'   => 'id_sortie'
		);
	$tables_principales['spip_amap_sortie'] = array(
		'field' => &$spip_amap_sortie,
		'key' => &$spip_amap_sortie_key,
		'join' => array('id_saison'=>'id_saison','id_produit'=>'id_produit')
		);

	//-- Table type_contrat -------------------
	$spip_amap_type_contrat = array(
		'id_type'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'label_type' => 'VARCHAR(20) NOT NULL'
		);
	$spip_amap_type_contrat_key = array(
		'PRIMARY KEY'   => 'id_type'
		);
	$tables_principales['spip_amap_type_contrat'] = array(
		'field' => &$spip_amap_type_contrat,
		'key' => &$spip_amap_type_contrat_key,
		);

	//-- Table vacance -------------------
	$spip_amap_vacance = array(
		'id_vacance'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_contrat'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'id_evenement'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'id_remplacant'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'remplacant_ext'  => 'VARCHAR(150) DEFAULT "0" NOT NULL',
		'paye'  => 'BOOLEAN NOT NULL'
		);
	$spip_amap_vacance_key = array(
		'PRIMARY KEY'   => 'id_vacance,id_contrat,id_evenement'
		);
	$tables_principales['spip_amap_vacance'] = array(
		'field' => &$spip_amap_vacance,
		'key' => &$spip_amap_vacance_key,
		'join' => array('id_contrat'=>'id_contrat','id_evenement'=>'id_evenement','id_remplacant'=>'id_personne')
		);

	//-- Table variete -------------------
	$spip_amap_variete = array(
		'id_variete'  => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
		'id_famille'  => 'BIGINT(20) NOT NULL',
		'label_variete'  => 'BIGINT(20) NULL',
		);
	$spip_amap_variete_key = array(
		'PRIMARY KEY'   => 'id_variete'
		);
	$tables_principales['spip_amap_variete'] = array(
		'field' => &$spip_amap_variete,
		'key' => &$spip_amap_variete_key,
		'join' => array('id_famille'=>'id_famille')
		);
    return $tables_principales;
}
//fonction qui permet de créer le contenu
function amap_config_motsclefs() {
	//les groupes puis mots
	create_groupe("modalites_affichage_article", "L'article correspondant aux distributions d'une saison donnée sera associé au mot clé « amap_agenda »", "", 'non', 'non', 'oui', 'non', 'non', 'non', 'oui', 'oui', 'oui', 'non');
		create_mot("modalites_affichage_article", "amap_contrat", "Affecter ce mot clef à l'article concernant les contrats.", "");
		create_mot("modalites_affichage_article", "amap_distribution", "Affecter ce mot clef à l'article concernant la distribution.", "");
		create_mot("modalites_affichage_article", "amap_responsable", "Affecter ce mot clef à l'article concernant le responsable.", "");
		create_mot("modalites_affichage_article", "amap_sortie", "Affecter ce mot clef à l'article concernant la sorties.", "");
		create_mot("modalites_affichage_article", "amap_vacance", "Affecter ce mot clef à l'article concernant les vacances.", "");
}
?>

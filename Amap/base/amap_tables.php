<?php

/**
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
*
*
*/

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
		'id_contrat'  => 'BIGINT(20) NOT NULL',
		'id_evenement'  => 'BIGINT(20) NOT NULL',
		'id_remplacant'  => 'BIGINT(20) DEFAULT "0" NOT NULL',
		'remplacant_ext'  => 'VARCHAR(150) DEFAULT "0" NOT NULL',
		'paye'  => 'BIGINT(20) DEFAUT "0" NOT NULL'
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
?>

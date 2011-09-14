<?php
function sitra_declarer_tables_principales($tables_principales){
// Table SITRA_OBJETS
$sitra_objets_field = array(
	'id_sitra_objet' => 'bigint(21) NOT NULL AUTO_INCREMENT',
	'id_sitra' => 'varchar(32) NOT NULL',
	'titre' => 'text NOT NULL',
	'adresse' => 'text NOT NULL',
	'commune' => 'varchar(64) NOT NULL',
	'code_postal' => 'varchar(5) NOT NULL',
	'insee' => 'varchar(5) NOT NULL',
	'telephone' => 'text NOT NULL',
	'fax' => 'text NOT NULL',
	'tel_fax' => 'text NOT NULL',
	'email' => 'text NOT NULL',
	'web' => 'text NOT NULL',
	'date_debut' => 'datetime default \'0000-00-00 00:00:00\' NOT NULL',
	'date_fin' => 'datetime default \'0000-00-00 00:00:00\' NOT NULL',
	'latitude' => 'varchar(12) NOT NULL',
	'longitude' => 'varchar(12) NOT NULL',
	'altitude' => 'varchar(5) NOT NULL',
	'classement_code' => 'varchar(32) NOT NULL',
	'classement' => 'varchar(32) NOT NULL',
	);

$sitra_objets_key = array(
	'PRIMARY KEY'	=> 'id_sitra_objet',
	'UNIQUE KEY id_sitra' => 'id_sitra',
	'KEY date_debut' => 'date_debut',
	'KEY date_fin' => 'date_fin'
	);

$sitra_objets_join = array(
	'id_sitra' => 'id_sitra'
	);

$tables_principales['spip_sitra_objets'] = array(
	'field' => &$sitra_objets_field,
	'key' => &$sitra_objets_key,
	'join' => &$sitra_objets_join
	);

// Table SITRA_OBJETS_DETAILS

$sitra_objets_details_field = array(
	'id_sitra' => 'varchar(32) NOT NULL',
	'lang' => 'varchar(3) NOT NULL',
	'titre_lang' => 'text NOT NULL',
	'lieu' => 'text NOT NULL',
	'description' => 'text NOT NULL',
	'description_courte' => 'text NOT NULL',
	'observation_dates' => 'text NOT NULL',
	'tarifs_en_clair' => 'text NOT NULL',
	'tarifs_complementaires' => 'text NOT NULL',
	'presta_accessibilite' => 'text NOT NULL',
	'presta_activites' => 'text NOT NULL',
	'presta_confort' => 'text NOT NULL',
	'presta_encadrement' => 'text NOT NULL',
	'presta_equipements' => 'text NOT NULL',
	'presta_services' => 'text NOT NULL',
	'presta_sitra' => 'text NOT NULL',
	'langues' => 'text NOT NULL',
	'capacites' => 'text NOT NULL'
	);

$sitra_objets_details_key = array(
	'PRIMARY KEY' => 'id_sitra, lang',
	'KEY id_sitra'	=> 'id_sitra'
	);

$sitra_objets_details_join = array(
	'id_sitra' => 'id_sitra'
	);

$tables_principales['spip_sitra_objets_details'] = array(
	'field' => &$sitra_objets_details_field,
	'key' => &$sitra_objets_details_key,
	'join' => &$sitra_objets_details_join
	);

// Table SITRA_CATEGORIES
$sitra_categories_field = array(
	'id_sitra' => 'varchar(32) NOT NULL',
	'id_categorie' => 'varchar(32) NOT NULL',
	'categorie' => 'varchar(64) NOT NULL'
	);

$sitra_categories_key = array(
	'PRIMARY KEY' => 'id_sitra, id_categorie',
	'KEY categorie' => 'categorie'
	);

$sitra_categories_join = array(
	'id_sitra' => 'id_sitra'
	);

$tables_principales['spip_sitra_categories'] = array(
	'field' => &$sitra_categories_field,
	'key' => &$sitra_categories_key,
	'join' => &$sitra_categories_join
	);

// Table SITRA_SELECTIONS
$sitra_selections_field = array(
	'id_sitra' => 'varchar(32) NOT NULL',
	'id_selection' => 'integer NOT NULL',
	'selection' => 'varchar(32) NOT NULL'
	);

$sitra_selections_key = array(
	'PRIMARY KEY' => 'id_sitra, id_selection',
	'KEY selection' => 'selection'
	);

$sitra_selections_join = array(
	'id_sitra' => 'id_sitra'
	);

$tables_principales['spip_sitra_selections'] = array(
	'field' => &$sitra_selections_field,
	'key' => &$sitra_selections_key,
	'join' => &$sitra_selections_join
	);


// Table SITRA_IMAGES
$sitra_images_field = array(
	'id_sitra' => 'varchar(32) NOT NULL',
	'num_image' => 'integer NOT NULL',
	'url_image' => 'varchar(255) NOT NULL',
	'type_image' => 'varchar(12) NOT NULL',
	'lien' => 'varchar(3) NOT NULL'
	);

$sitra_images_key = array(
	'PRIMARY KEY' => 'id_sitra, num_image',
	);

$sitra_images_join = array(
	'id_sitra' => 'id_sitra',
	'num_image' => 'num_image'
	);

$tables_principales['spip_sitra_images'] = array(
	'field' => &$sitra_images_field,
	'key' => &$sitra_images_key,
	'join' => &$sitra_images_join
);

// Table SITRA_IMAGES_DETAILS
$sitra_images_details_field = array(
	'id_sitra' => 'varchar(32) NOT NULL',
	'num_image' => 'integer NOT NULL',
	'lang' => 'varchar(3) NOT NULL',
	'titre' => 'text NOT NULL',
  	'descriptif' => 'text NOT NULL',
  	'copyright' => 'text NOT NULL'
	);

$sitra_images_details_key = array(
	'PRIMARY KEY' => 'id_sitra, num_image, lang',
	);

$sitra_images_details_join = array(
	'id_sitra' => 'id_sitra',
	'num_image' => 'num_image'
	);

$tables_principales['spip_sitra_images_details'] = array(
	'field' => &$sitra_images_details_field,
	'key' => &$sitra_images_details_key,
	'join' => &$sitra_images_details_join
);

// table critères internes
$sitra_criteres_field = array(
	'id_sitra' => 'varchar(32) NOT NULL',
	'id_critere' => 'integer NOT NULL DEFAULT \'0\''
	);

$sitra_criteres_key = array(
	'PRIMARY KEY' => 'id_sitra, id_critere'
	);

$sitra_criteres_join = array(
	'id_sitra' => 'id_sitra',
	'id_critere' => 'id_critere'
	);

$tables_principales['spip_sitra_criteres'] = array(
	'field' => &$sitra_criteres_field,
	'key' => &$sitra_criteres_key,
	'join' => &$sitra_criteres_join
	);

return $tables_principales;

} // fin sitra_declarer_tables_principales

function sitra_declarer_tables_interfaces($interface){
	// les noms des tables dans les boucles
	$interface['table_des_tables']['sitra_objets'] = 'sitra_objets';
	$interface['table_des_tables']['sitra_objets_details'] = 'sitra_objets_details';
	$interface['table_des_tables']['sitra_categories'] = 'sitra_categories';
	$interface['table_des_tables']['sitra_selections'] = 'sitra_selections';
	$interface['table_des_tables']['sitra_images'] = 'sitra_images';
	$interface['table_des_tables']['sitra_images_details'] = 'sitra_images_details';
	$interface['table_des_tables']['sitra_criteres'] = 'sitra_criteres';
	
	// les jointures 
	$interface['tables_jointures']['spip_sitra_objets'][]= 'sitra_categories';
	$interface['tables_jointures']['spip_sitra_objets'][]= 'sitra_selections';
	$interface['tables_jointures']['spip_sitra_objets'][]= 'sitra_objets_details';
	$interface['tables_jointures']['spip_sitra_objets'][]= 'sitra_images';
	$interface['tables_jointures']['spip_sitra_objets'][]= 'sitra_criteres';
	
	$interface['tables_jointures']['spip_sitra_categories'][] = 'sitra_objets';
	$interface['tables_jointures']['spip_sitra_selections'][] = 'sitra_objets';
	$interface['tables_jointures']['spip_sitra_criteres'][] = 'sitra_objets';
	
	$interface['tables_jointures']['spip_sitra_images'][] = 'sitra_images_details';
	$interface['tables_jointures']['spip_sitra_images_details'][] = 'sitra_images';
	
	// les dates	
	$interface['table_date']['sitra_objets'] = 'date_debut';
	$interface['table_date']['sitra_objets'] = 'date_fin';
	
	$interface['table_des_traitements']['DESCRIPTION'][] = 'propre(%s)';
	$interface['table_des_traitements']['DESCRIPTION_COURTE'][]= 'propre(%s)';
	$interface['table_des_traitements']['OBSERVATIONS_DATES'][]= 'propre(%s)';
	$interface['table_des_traitements']['TARIFS_EN_CLAIR'][]= 'propre(%s)';
	$interface['table_des_traitements']['TARIFS_COMPLEMENTAIRES'][]= 'propre(%s)';
	
	// Titre pour url
	$interface['table_titre']['sitra_objets'] = "titre, '' AS lang";
	
	return $interface;
}

?>
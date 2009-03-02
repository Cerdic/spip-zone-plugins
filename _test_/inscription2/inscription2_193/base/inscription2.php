<?php
function inscription2_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_auteurs'][] = 'auteurs_elargis';
	
	//-- Table des tables ----------------------------------------------------
	
	$interface['table_des_tables']['auteurs_elargis']='auteurs_elargis';
	$interface['table_des_tables']['societes']='societes';

	return $interface;
}

function inscription2_declarer_tables_principales($tables_principales){
	global $exceptions_des_champs_auteurs_elargis;
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
		
	if(function_exists('lire_config')){
		foreach(lire_config('inscription2',array()) as $cle => $val) {
			$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $cle);
			if($val!='' and !in_array($cle,$exceptions_des_champs_auteurs_elargis) and !ereg("^(categories|zone|newsletter).*$", $cle) ){
				if($cle == 'naissance' )
					$spip_auteurs_elargis[$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
				elseif($cle == 'validite' )
					$spip_auteurs_elargis[$cle] = "datetime DEFAULT '0000-00-00 00:00:00 NOT NULL";
				elseif($cle == 'pays')
					$spip_auteurs_elargis[$cle] = "int NOT NULL";
				elseif($cle == 'pays_pro')
					$spip_auteurs_elargis[$cle] = "int NOT NULL";
				else
					$spip_auteurs_elargis[$cle] = "text NOT NULL";
			}
		}
	}
	$spip_auteurs_elargis_key = array(
		"PRIMARY KEY" => "id_auteur");
	
	$tables_principales['spip_auteurs_elargis'] = array(
		'field' => &$spip_auteurs_elargis,
		'key' => &$spip_auteurs_elargis_key);
	 	//'join' => &$spip_auteurs_elargis_join
	 	
	$spip_geo_pays['id_pays'] = "SMALLINT NOT NULL";
	$spip_geo_pays['pays'] = "text NOT NULL";
	$spip_geo_pays_key = array("PRIMARY KEY" => "id_pays");
	
	$tables_principales['spip_geo_pays'] = array(
		'field' => &$spip_geo_pays, 
		'key' => &$spip_geo_pays_key);
	
	$spip_societes['id_societe'] = "BIGINT(21) NOT NULL";
	$spip_societes['nom'] = "VARCHAR(255) NOT NULL";
	$spip_societes['secteur'] = "VARCHAR(255) NOT NULL";
	$spip_societes['adresse'] = "TEXT NOT NULL";
	$spip_societes['code_postal'] = "VARCHAR(255) NOT NULL";
	$spip_societes['ville'] = "VARCHAR(255) NOT NULL";
	$spip_societes['id_pays'] = "SMALLINT NOT NULL";
	$spip_societes['telephone'] = "VARCHAR(255) NOT NULL";
	$spip_societes['fax'] = "VARCHAR(255) NOT NULL";
	
	$spip_societes_key = array('PRIMARY KEY' => 'id_societe', 'KEY id_pays' => 'id_pays');
	
	$tables_principales['spip_societes'] = array(
		'field' => &$spip_societes,
		'key' => &$spip_societes_key);
	
	return $tables_principales;
}
?>
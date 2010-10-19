<?php
function inscription2_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_auteurs'][] = 'auteurs_elargis';

	//-- Table des tables ----------------------------------------------------
	$interface['table_des_tables']['auteurs_elargis']='auteurs_elargis';
	$interface['table_des_tables']['geo_pays']='geo_pays';
	return $interface;
}

function inscription2_declarer_tables_principales($tables_principales){
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());

	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";

	if(function_exists('lire_config')){
		if((lire_config('inscription2') == '') OR !is_array(unserialize($GLOBALS['meta']['inscription2']))){
			spip_log('INSCRIPTION 2 : Reverifier les plugins');
			include_spip('inc/plugin');
			installe_plugins();
		}
		foreach(lire_config('inscription2',array()) as $cle => $val) {
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $cle);
			if($val!='' and !in_array($cle,$exceptions_des_champs_auteurs_elargis) and !preg_match("/^(categories|zone|newsletter).*$/", $cle) ){
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

	/*
	 * A partir de Inscription 2 (0.70)
	 * on utilise le plugin geographie pour gerer les pays.
	 * on ne le rend pas obligatoire en creant la table spip_geo_pays
	 * si le plugin n'est pas installe
	 */

	/* penser a modifier si le plugin geographie modifie cette table */
	$spip_geo_pays = array(
			"id_pays"	=> "smallint NOT NULL",
			"nom"	=> "text DEFAULT '' NOT NULL",
	);
	$spip_geo_pays_key = array(
			"PRIMARY KEY"		=> "id_pays"
	);
	$tables_principales['spip_geo_pays'] = array(
		'field'=>&$spip_geo_pays,
		'key'=>$spip_geo_pays_key);

	return $tables_principales;
}
?>

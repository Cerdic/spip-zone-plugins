<?php
 	// declaration des tables
	global $tables_principales;
	global $tables_auxiliaires;
	global $table_des_tables;
	
	//$var_user = array();
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
	
	foreach(lire_config('inscription2') as $cle => $val) {
		$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $cle);
		if($val!='' and $clef != 'login' and $cle != 'nom' and $cle != 'statut_nouveau' and $cle != 'email' and $cle != 'username' and $cle != 'statut_int'  and $cle != 'accesrestreint' and !ereg("^(categories|zone|newsletter).*$", $cle) ){
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
			//$var_user[$cle] = ' ';
		}
	}
	$spip_auteurs_elargis_key = array("PRIMARY KEY" => "id_auteur");
	$spip_geo_pays['id_pays'] = "bigint(21) NOT NULL";
	$spip_geo_pays['pays'] = "text NOT NULL ";
	$spip_geo_pays_key = array("PRIMARY KEY"	=> "id_pays");

	// surcharger auteur session, desactive car ca pete en 193
	/*
	if(is_array($var_user) and isset($GLOBALS['auteur_session']['id_auteur'])){
		$id = $GLOBALS['auteur_session']['id_auteur'];
		$query = spip_query("select ".join(', ', array_keys($var_user))." from spip_auteurs_elargis where id_auteur = $id");
		$query = spip_fetch_array($query);
		exit;
		$GLOBALS['auteur_session'] = array_merge($query,$GLOBALS['auteur_session'] );
	}
	*/	

	/* Gerer table Societes */
	$spip_societes['id_societe'] = "BIGINT(21) NOT NULL";
	$spip_societes['nom'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['secteur'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['adresse'] = "TEXT NOT NULL ";
	$spip_societes['code_postal'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['ville'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['id_pays'] = "BIGINT(21) NOT NULL";
	$spip_societes['telephone'] = "VARCHAR(255) NOT NULL ";
	$spip_societes['fax'] = "VARCHAR(255) NOT NULL ";	
	
	$spip_societes_key = array('PRIMARY KEY' => 'id_societe', 'KEY id_pays' => 'id_pays');

	$tables_auxiliaires['spip_auteurs_elargis'] = array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
	$tables_principales['spip_geo_pays'] = array('field' => &$spip_geo_pays, 'key' => &$spip_geo_pays_key);
	$tables_principales['spip_societes'] = array('field' => &$spip_societes, 'key' => &$spip_societes_key);

	global $tables_jointures;
	$tables_jointures['spip_auteurs']['']= 'auteurs_elargis';

    $table_des_tables['societes'] = 'societes';
?>
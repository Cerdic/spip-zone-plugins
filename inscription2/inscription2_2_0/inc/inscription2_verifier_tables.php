<?php
function inc_inscription2_verifier_tables_dist(){
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
	spip_log('INCRIPTION 2 : verification des tables','inscription2');
	
	//definition de la table cible
	$table_nom = "spip_auteurs_elargis";
	$desc = sql_showtable($table_nom,'', false);
		
	if(isset($desc['field']) and $desc['key']['PRIMARY KEY']!='id_auteur'){
		if(!isset($desc['field']['id_auteur'])){
			sql_alter("TABLE ".$table_nom." ADD id_auteur INT NOT NULL PRIMARY KEY");
		}
		sql_alter("TABLE ".$table_nom." DROP id, DROP INDEX id_auteur, ADD PRIMARY KEY (id_auteur)");
	}else if(!$desc){
		sql_create($table_nom,
			array("id_auteur"=> "bigint(21) NOT NULL default '0'"),
			array('PRIMARY KEY' => "id_auteur")
		);
	}
	
	if (is_array(lire_config('inscription2'))){
		$clef_passee = array();
		foreach(lire_config('inscription2',array()) as $clef => $val) {
			$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $clef);
			if(!in_array($cle,$clef_passee)){
				if(!in_array($cle,$exceptions_des_champs_auteurs_elargis) and !ereg("^(categories|zone|newsletter).*$", $cle) ){
					if($cle == 'naissance' and !isset($desc['field'][$cle]) and _request($clef)!=''){
						sql_alter("TABLE ".$table_nom." ADD ".$cle." DATE DEFAULT '0000-00-00' NOT NULL");
						$desc['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
					}elseif(_request($clef)!='' and !isset($desc['field'][$cle]) and $cle == 'validite'){
						sql_alter("TABLE ".$table_nom." ADD ".$cle." datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
						$desc['field'][$cle] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
					}elseif(_request($clef)!='' and !isset($desc['field'][$cle]) and $cle == 'pays'){
						sql_alter("TABLE ".$table_nom." ADD ".$cle." int NOT NULL");
						$desc['field'][$cle] = " int NOT NULL";
					}elseif(!isset($desc['field'][$cle]) and _request($clef)!=''){
						sql_alter("TABLE ".$table_nom." ADD ".$cle." text NOT NULL");
						$desc['field'][$cle] = "text NOT NULL";
					}
				}
				if(in_array($cle,$exceptions_des_champs_auteurs_elargis)){
					spip_log("INSCRIPTION 2 : le champs $cle est dans les exception de creation de champs...",'inscription2');
				}
				$clef_passee[] = $cle;
			}
		}
	}
	
	if($GLOBALS['meta']['spiplistes_version'] and !isset($desc['field']['spip_listes_format']))
		sql_alter("TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR(8) DEFAULT 'non' NOT NULL");
}
?>

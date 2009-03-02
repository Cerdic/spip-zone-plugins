<?php
function inc_inscription2_verifier_tables_dist(){
	global $exceptions_des_champs_auteurs_elargis;
	spip_log('verification des tables pour inscription2','inscription2');
	
	//definition de la table cible
	$table_nom = "spip_auteurs_elargis";
	$desc = sql_showtable($table_nom, true, '');
	if(!$desc){
		sql_create($table_nom,
			array('id_auteur'=> 'bigint NOT NULL'),
			array('KEY' => 'id_auteur')
		);
	}
	foreach(lire_config('inscription2',array()) as $clef => $val) {
		$cle = ereg_replace("_(obligatoire|fiche|table).*", "", $clef);
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
			spip_log("le champs $cle est dans les exception de creation de champs....");
		}
	}
	$listes = lire_config('plugin/SPIPLISTES');
	if($listes and !isset($desc['field']['spip_listes_format']))
	sql_alter("TABLE `".$table_nom."` ADD `spip_listes_format` VARCHAR( 8 ) DEFAULT 'non' NOT NULL");
}
?>
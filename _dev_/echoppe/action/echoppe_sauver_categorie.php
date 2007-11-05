<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_categorie(){
	if (_request('new') == 'new'){
		$sql_insert_categorie = "INSERT INTO spip_echoppe_categories VALUES ('','"._request('id_parent')."')";
		$res_insert_categorie = spip_query($sql_insert_categorie);
		$new_id_categorie = spip_insert_id();
		$sql_insert_categorie_descriptif = "INSERT INTO spip_echoppe_categories_descriptifs VALUES () ";
	}else{
	
	}

}

?>

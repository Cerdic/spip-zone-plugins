<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_categorie(){
	if (_request('new') == 'oui'){
		$sql_insert_categorie = "INSERT INTO spip_echoppe_categories VALUES ('','"._request('id_parent')."')";
		$res_insert_categorie = spip_query($sql_insert_categorie);
		echo $sql_insert_categorie.'<hr />';
		$new_id_categorie = spip_insert_id();
		$sql_insert_categorie_descriptif = "INSERT INTO spip_echoppe_categories_descriptifs VALUES ('','".$new_id_categorie."','".$lang."','".$titre."','".$descriptif."','".$texte."','".$logo."','','".$statut."') ";
		$res_insert_categorie_descriptif = spip_query($sql_insert_categorie_descriptif);
		echo $sql_insert_categorie_descriptif.'<hr />';
		
	}else{
		$sql_update_categorie_descriptif = "UPDATE spip_echoppe_categories_descriptifs SET titre = '".$titre."', descriptif = '".$descriptif."', texet = '".$texte."', statut = '".$statut."' WHERE id_categorie_descriptif = '"._request('id_categorie_descriptif')."' ";
		echo $sql_update_categorie_descriptif;
		$res_update_categorie_descriptif = spip_query($sql_insert_categorie_descriptif);
	}

}

?>

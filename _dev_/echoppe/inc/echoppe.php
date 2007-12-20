<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_echec_autorisation(){
	echo debut_boite_alerte();
	echo _T('echoppe:acces_non_autorise');
	echo fin_boite_alerte();
}

function recuperer_id_secteur($id, $id_objet, $objet){
	switch ($objet){
		case 'categorie':
			if($id != 0){
				$sql_recup_id_secteur = ("SELECT id_secteur FROM spip_echoppe_categories WHERE id_categorie = '".$id."';");
				$res_recup_id_secteur = spip_query($sql_recup_id_secteur);
				$id_secteur = spip_fetch_array($res_recup_id_secteur);
				return $id_secteur['id_secteur'];				
			}else{
				return $id;
			}
		break;
	}
}

?>

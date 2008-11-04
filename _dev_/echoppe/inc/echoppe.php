<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_echec_autorisation(){
	echo debut_boite_alerte();
	echo _T('echoppe:acces_non_autorise');
	echo fin_boite_alerte();
}

function recuperer_id_secteur($id_parent, $objet){
	switch ($objet){
		case 'categorie':
			if($id != 0){
				$res_recup_id_secteur = sql_select(array("id_secteur"),array("spip_echoppe_".$objet."s"), array("id_".$objet." = ".$id_parent));
				$temp = sql_fetch($res_recup_id_secteur);
				$id_secteur = $temp['id_secteur'];				
			}else{
				$id_secteur = $id_parent;
			}
		break;
	}
	
	return $id_secteur;
}

function array2select($tab, $nom, $style){
	
	$select .= '<select class="'.$style.'">';
	
	foreach ($tab as $option){
		$select .= '<option value="'.$option.'">'.$option.'</option>';
	}
	
	$select .= '</select>';
}

?>

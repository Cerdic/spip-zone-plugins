<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function zero_si_vide($_var){
	if ($_var == ""){
		$_var = 0; 
	}
	return $_var;
}

function calculer_prix_tvac($prix_htva, $taux_tva){
	if ($taux_tva == 0){
		$taux_tva = lire_config('echoppe/taux_de_tva_par_defaut',6);
	}
	$prix_ttc = $prix_htva + ($prix_htva * ($taux_tva / 100));
	$prix_ttc = round($prix_ttc, lire_config('echoppe/nombre_chiffre_apres_virgule',2));
	if (lire_config('echoppe/arrondi_superieur_de_prix_tvac','non') == 'on'){
		$prix_ttc = ceil($prix_ttc);
	}
	return $prix_ttc;
}

function echoppe_echec_autorisation(){
	echo debut_boite_alerte();
	echo _T('echoppe:acces_non_autorise');
	echo fin_boite_alerte();
}

function recuperer_id_secteur($id_parent, $objet){
	switch ($objet){
		case 'categorie':
			if($id_parent != 0){
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

function ref_produit_unique($ref_produit, $id_produit){
	
	return true;
}

?>

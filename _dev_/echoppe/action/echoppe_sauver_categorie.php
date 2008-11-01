<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_categorie(){
	include_spip('inc/echoppe');
	
	$lang_categorie = _request('lang_categorie');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$texte = _request('texte');
	$logo = _request('logo');
	$id_categorie = _request('id_categorie');
	$id_parent = _request('id_parent');
	$new = _request('new');
	$id_secteur = recuperer_id_secteur($id_parent, $id_categorie, 'categorie');
	$categorie = _request('categorie');
	
	switch ($new){
		
		case 'oui':
			$valeur = array('id_categorie'=>'','id_parent'=>$id_parent,'id_secteur'=>$id_secteur);
			$new_id_categorie = sql_insertq('spip_echoppe_categories',$categorie);
			$id_categorie = $new_id_categorie;
			break;
		
		
		default :
			/*$sql_update_categorie_descriptif = "UPDATE spip_echoppe_categories_descriptions SET titre = '".addslashes($titre)."', descriptif = '".addslashes($descriptif)."', texte = '".addslashes($texte)."', statut = '".$statut."' WHERE id_categorie = '".$id_categorie."' AND lang = '".$lang_categorie."' ";
			//echo $sql_update_categorie_descriptif;
			$res_update_categorie_descriptif = spip_query($sql_update_categorie_descriptif);*/
			$sav_update = sql_updateq('spip_echoppe_categories',$categorie,'id_categorie = '.$categorie['id_categorie']);
			$id_categorie = $categorie['id_categorie'];
			break;
		
	}
	$redirect = generer_url_ecrire('echoppe_categorie', 'id_categorie='.$id_categorie,'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>

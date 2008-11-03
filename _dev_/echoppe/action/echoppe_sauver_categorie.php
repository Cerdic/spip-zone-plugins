<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_categorie(){
	include_spip('inc/echoppe');
	
	$categorie = array();
	$categorie['titre'] = _request('titre_categorie');
	$categorie['descriptif'] = _request('descriptif_categorie');
	$categorie['texte'] = _request('texte');
	$categorie['id_categorie'] = _request('id_categorie');
	$categorie['id_parent'] = _request('id_parent');
	
	$new = _request('new');
	$id_secteur = recuperer_id_secteur($id_parent, $id_categorie, 'categorie');
	$lang_categorie = _request('lang_categorie');
	$logo = _request('logo');
	
	switch ($new){
		
		case 'oui':
			$valeur = array('id_categorie'=>'','id_parent'=>$id_parent,'id_secteur'=>$id_secteur);
			$new_id_categorie = sql_insertq('spip_echoppe_categories',$categorie);
			sql_updateq('spip_echoppe_categories',array('id_secteur' => recuperer_id_secteur($categorie['id_parent'],'categorie')), 'id_categorie = '.$new_id_categorie);
			$id_categorie = $new_id_categorie;
			break;
		
		
		default :
			$sav_update = sql_updateq('spip_echoppe_categories',$categorie,'id_categorie = '.$categorie['id_categorie']);
			sql_updateq('spip_echoppe_categories',array('id_secteur' => recuperer_id_secteur($categorie['id_parent'],'categorie')), 'id_categorie = '.$categorie['id_categorie']);
			$id_categorie = $categorie['id_categorie'];
			break;
		
	}
	$redirect = generer_url_ecrire('echoppe_categorie', 'id_categorie='.$id_categorie,'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>

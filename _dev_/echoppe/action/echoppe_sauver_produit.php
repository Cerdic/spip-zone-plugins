<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_produit(){
	$lang_produit = _request('lang');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$texte = _request('texte');
	$logo = _request('logo');
	$id_categorie = _request('id_categorie');
	$new = _request('new');
	//echo _request('new');
	
	switch ($new){
		case 'oui':
			$sql_insert_categorie = "INSERT INTO spip_echoppe_produits VALUES ('','"._request('id_parent')."')";
			$res_insert_categorie = spip_query($sql_insert_categorie);
			//echo $sql_insert_categorie.'<hr />';
			$new_id_categorie = spip_insert_id();
			$sql_insert_categorie_descriptif = "INSERT INTO spip_echoppe_produits_descriptions VALUES ('','".$new_id_categorie."','".$lang_categorie."','".addslashes($titre)."','".addslashes($descriptif)."','".addslashes($texte)."','".$logo."','','".$statut."') ";
			$res_insert_categorie_descriptif = spip_query($sql_insert_categorie_descriptif);
			$id_categorie = $new_id_categorie;
			break;
		
		case 'description' :
			$sql_insert_categorie_descriptif = "INSERT INTO spip_echoppe_categories_descriptions VALUES ('','".$id_categorie."','".$lang_categorie."','".addslashes($titre)."','".addslashes($descriptif)."','".addslashes($texte)."','".$logo."','".$lang_categorie."','".$statut."') ";
			$res_insert_categorie_descriptif = spip_query($sql_insert_categorie_descriptif);
			break;
		
		default :
			$sql_update_categorie_descriptif = "UPDATE spip_echoppe_categories_descriptions SET titre = '".addslashes($titre)."', descriptif = '".addslashes($descriptif)."', texte = '".addslashes($texte)."', statut = '".$statut."' WHERE id_categorie = '".$id_categorie."' AND lang = '".$lang_categorie."' ";
			//echo $sql_update_categorie_descriptif;
			$res_update_categorie_descriptif = spip_query($sql_update_categorie_descriptif);
			break;
		
	}
	$redirect = generer_url_ecrire('echoppe_categorie', 'id_categorie='.$id_categorie,'&');
	echo $redirect;
	redirige_par_entete($redirect);
}

?>

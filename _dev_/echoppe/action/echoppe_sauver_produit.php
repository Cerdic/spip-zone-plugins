<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_produit(){
	$lang_produit = _request('lang_produit');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$texte = _request('texte');
	$ps = _request('ps');
	$id_categorie = _request('id_categorie');
	$new = _request('new');
	$ref_produit = _request('ref_produit');
	
	//echo _request('new');
	
	switch ($new){
		case 'oui':
			$sql_insert_produit = "INSERT INTO spip_echoppe_produits VALUES ('','".$date_debut."', '".$date_fin."', '".$poids."', '".$hauteur."', '".$largeur."', '".$longeur."', '".$colisage."', '".$ref_produit."', '".$prix_base_htva."', '', '".$statut."');";
			$res_insert_produit = spip_query($sql_insert_categorie);
			//echo $sql_insert_categorie.'<hr />';
			$new_id_produit = spip_insert_id();
			$sql_insert_produit_descriptif = "INSERT INTO spip_echoppe_produits_descriptions VALUES ('','".$new_id_produit."','".$lang_produit."','".addslashes($titre)."','".addslashes($descriptif)."','".addslashes($texte)."','".addslashes($ps)."','".$tva."','".$quantite_mini."','') ";
			$res_insert_produit_descriptif = spip_query($sql_insert_categorie_descriptif);
			$id_produit = $new_id_produit;
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
	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$id_produit,'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>

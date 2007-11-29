<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_produit(){
	$lang_produit = _request('lang_produit');
	$titre = _request('titre');
	$descriptif = _request('description');
	$texte = _request('texte');
	$ps = _request('ps');
	$id_categorie = _request('id_categorie');
	$id_produit = _request('id_produit');
	$new = _request('new');
	$ref_produit = _request('ref_produit');
	$quantite_mini = _request('quantite_mini');
	$tva = _request('tva');
	$date_debut = _request('annee_date_en_ligne').'-'._request('mois_date_en_ligne').'-'._request('jour_date_en_ligne').' 00:00:00';
	$date_fin = _request('annee_date_retrait_ligne').'-'._request('mois_date_retrait_ligne').'-'._request('jour_date_retrait_ligne').' 00:00:00';
	$poids = _request('poids');
	$largeur = _request('largeur');
	$longueur = _request('longueur');
	$hauteur = _request('hauteur');
	$colisage = _request('colisage');
	$ref_produit = _request('ref_produit');
	$prix_base_htva = _request('prix_base_htva');
	
	//echo $new.'<---';
	
	switch ($new){
		case 'oui':
			$sql_insert_produit = "INSERT INTO spip_echoppe_produits VALUES ('','".$date_debut."', '".$date_fin."', '".$poids."', '".$hauteur."', '".$largeur."', '".$longeur."', '".$colisage."', '".$ref_produit."', '".$prix_base_htva."', '', 'prepa');";
			$res_insert_produit = spip_query($sql_insert_produit);
			//echo $sql_insert_produit.'<hr />';
			
			$new_id_produit = spip_insert_id();
			$id_produit = $new_id_produit;
			
			$sql_lien_produit_categorie = "INSERT INTO spip_echoppe_categories_produits VALUES ('".$id_categorie."', '".$id_produit."');";
			$res_lien_produit_categorie = spip_query($sql_lien_produit_categorie);
			
			
			$sql_insert_produit_descriptif = "INSERT INTO spip_echoppe_produits_descriptions VALUES ('','".$new_id_produit."','".$lang_produit."','".addslashes($titre)."','".addslashes($descriptif)."','".addslashes($texte)."','".addslashes($ps)."','".$tva."','".$quantite_mini."', '', '') ";
			$res_insert_produit_descriptif = spip_query($sql_insert_produit_descriptif);
			
			//echo $sql_insert_produit_descriptif.'<hr />';
			
			break;
		
		case 'ajout_description' :
			$sql_insert_produit_descriptif = "INSERT INTO spip_echoppe_produits_descriptions VALUES ('','".$id_produit."','".$lang_produit."','".addslashes($titre)."','".addslashes($descriptif)."','".addslashes($texte)."','".addslashes($ps)."','".$tva."','".$quantite_mini."', '', '') ";
			//echo $sql_insert_produit_descriptif;
			$res_insert_produit_descriptif = spip_query($sql_insert_produit_descriptif);
			break;
		
		case 'maj_description' :
			$sql_update_produit_descriptif = "UPDATE spip_echoppe_produits_descriptions SET titre = '".addslashes($titre)."', descriptif = '".addslashes($descriptif)."', texte = '".addslashes($texte)."',ps = '".addslashes($ps)."', tva = '".$tva."', quantite_mini = '".$quantite_mini."' WHERE id_produit = '".$id_produit."' AND lang = '".$lang_produit."' ";
			//echo $sql_update_produit_descriptif;
			$res_update_produit_descriptif = spip_query($sql_update_produit_descriptif);
			break;
		
		default :
			//$sql_update_produit_descriptif = "UPDATE spip_echoppe_produits_descriptions SET titre = '".addslashes($titre)."', descriptif = '".addslashes($descriptif)."', texte = '".addslashes($texte)."', statut = '".$statut."' WHERE id_produit = '".$id_produit."' AND lang = '".$lang_produit."' ";
			//echo $sql_update_produit_descriptif;
			//$res_update_produit_descriptif = spip_query($sql_update_produit_descriptif);
			break;
		
	}
	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$id_produit,'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>

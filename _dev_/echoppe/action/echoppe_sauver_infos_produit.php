<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_infos_produit(){
	$produit = array();
	$produit['id_produit'] = _request('id_produit');
	$produit['date_debut'] = _request('annee_date_en_ligne').'-'._request('mois_date_en_ligne').'-'._request('jour_date_en_ligne').' 00:00:00';
	$produit['date_fin'] = _request('annee_date_retrait_ligne').'-'._request('mois_date_retrait_ligne').'-'._request('jour_date_retrait_ligne').' 00:00:00';
	$produit['poids'] = _request('poids');
	$produit['largeur'] = _request('largeur');
	$produit['longueur'] = _request('longueur');
	$produit['hauteur'] = _request('hauteur');
	$produit['colisage'] = _request('colisage');
	$produit['ref_produit'] = _request('ref_produit');
	$produit['prix_base_htva'] = _request('prix_base_htva');
	
	sql_updateq("spip_echoppe_produits",$produit,"id_produit = '".$produit['id_produit']."'");
	
	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$produit['id_produit'].'&onglet=infos','&');
	
	redirige_par_entete($redirect);
	
}

?>

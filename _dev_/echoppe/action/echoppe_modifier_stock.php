<?php

function action_echoppe_modifier_stock(){

	$contexte = Array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['id_depot'] = _request('id_depot');
	$contexte['modif_stock'] = _request('modif_stock');
	$contexte['plus'] = _request('plus');
	$contexte['moins'] = _request('moins');
	
	
	$sql_recup_stock_du_depot = "SELECT quantite FROM spip_echoppe_stock_produits WHERE id_produit = '".$contexte['id_produit']."' AND id_depot = '".$contexte['id_depot']."';";
	$res_recup_stock_du_depot = spip_query($sql_recup_stock_du_depot);
	$la_quantite = spip_fetch_array($res_recup_stock_du_depot);
	$contexte['quantite'] = $la_quantite['quantite'];
	
	if (isset($contexte['moins'])){
		$contexte['quantite'] = $contexte['quantite'] - $contexte['modif_stock'];
	}
	
	if (isset($contexte['plus'])){
		$contexte['quantite'] = $contexte['quantite'] + $contexte['modif_stock'];
	}
	
	if ($contexte['id_depot'] == '-1' && $contexte['quantite'] <= 0) $contexte['quantite'] = 0;
	
	if (spip_num_rows($res_recup_stock_du_depot) == 1){
		$sql_maj_quantite = "UPDATE spip_echoppe_stock_produits SET quantite = '".$contexte['quantite']."' WHERE id_produit = '".$contexte['id_produit']."' AND id_depot = '".$contexte['id_depot']."';";
		$res_maj_quantite = spip_query($sql_maj_quantite);
	}else{
		$sql_maj_quantite = "INSERT INTO spip_echoppe_stock_produits VALUES ('', '".$contexte['id_produit']."', '', '".$contexte['id_depot']."', '".$contexte['quantite']."', NOW());";
		$res_maj_quantite = spip_query($sql_maj_quantite);	
	}
	$contexte['redirect'] = generer_url_ecrire("echoppe_produit", "id_produit=".$contexte['id_produit'], "&");
	
	redirige_par_entete($contexte['redirect']);
}

?>

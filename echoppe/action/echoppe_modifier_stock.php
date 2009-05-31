<?php

function action_echoppe_modifier_stock(){

	$contexte = Array();
	$contexte['ref_produit'] = _request('ref_produit');
	$contexte['id_depot'] = _request('id_depot');
	$contexte['id_produit'] = _request('id_produit');
	$contexte['modif_stock'] = _request('modif_stock');
	$contexte['plus'] = _request('plus');
	$contexte['moins'] = _request('moins');
	
	$sql_quantite = sql_select('quantite','spip_echoppe_stocks',array("ref_produit = '".$contexte['ref_produit']."'","id_depot = '".$contexte['id_depot']."'"));
	$la_quantite = sql_fetch($sql_quantite);
	$contexte['quantite'] = $la_quantite['quantite'];
	
	if (isset($contexte['moins'])){
		$contexte['quantite'] = $contexte['quantite'] - $contexte['modif_stock'];
	}
	
	if (isset($contexte['plus'])){
		$contexte['quantite'] = $contexte['quantite'] + $contexte['modif_stock'];
	}
	
	if ($contexte['id_depot'] == '-1' && $contexte['quantite'] <= 0) $contexte['quantite'] = 0;
	
	if (sql_count($sql_quantite) == 1){
		/*$sql_maj_quantite = "UPDATE spip_echoppe_stocks SET quantite = '".$contexte['quantite']."' WHERE ref_produit = '".$contexte['ref_produit']."' AND id_depot = '".$contexte['id_depot']."';";
		$res_maj_quantite = spip_query($sql_maj_quantite);*/
		sql_updateq('spip_echoppe_stocks' ,array("quantite" => $contexte['quantite']), "ref_produit = '".$contexte['ref_produit']."' AND id_depot = '".$contexte['id_depot']."'");
	}else{
		/*$sql_maj_quantite = "INSERT INTO spip_echoppe_stocks VALUES ('', '".$contexte['ref_produit']."', '', '".$contexte['id_depot']."', '".$contexte['quantite']."', NOW());";
		$res_maj_quantite = spip_query($sql_maj_quantite);	*/
		$res_maj_quantite = sql_insertq('spip_echoppe_stocks' , array("ref_produit" => $contexte['ref_produit'], "id_depot" => $contexte['id_depot'], "quantite" => $contexte['quantite']));
	}
	$contexte['redirect'] = generer_url_ecrire("echoppe_produit", "id_produit=".$contexte['id_produit'].'&onglet=stocks', "&");
	
	redirige_par_entete($contexte['redirect']);
}

?>

<?php
//
// sauver_categorie.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;


function action_sauver_categorie_dist(){
	if (_request('id_produit') == "new"){
		$id_produit = '';
		$token = _request('token');
		$titre = _request('titre');
		$soustitre = _request('soustitre');
		$descriptif = _request('descriptif');
		$texte = _request('texte');
		$logo = '';
		$url_site = _request('url_site');
		$nom_site = _request('nom_site');
		$id_categorie = _request('id_categorie');
		$id_parent = _request('id_parent');
		$id_secteur = _request('id_secteur');
		$id_gamme = _request('id_gamme');
		$prix_achat = _request('prix_achat');
		$prix_vente = _request('prix_vente');
		$tva = _request('tva');
		$lang = _request('lan');
		$lang_choisie = _request('lang_choisie');
		$date = 'CURRENT_TIMESTAMP';
		$date_modif = 'CURRENT_TIMESTAMP';
		$date_redac = 'CURRENT_TIMESTAMP';
	}else{
		$sql_produit = "SELECT * FROM spip_boutique_produits WHERE id_produit='"._request('id_produit')."';";
		$res_produit = spip_query($sql_produit);
	}
}
?>
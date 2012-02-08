<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Pour le pipeline
function produits_autoriser(){}

function autoriser_rubrique_creerproduitdans_dist($faire, $type, $id, $qui, $opt) {
	return produits_autoriser_creerproduitdans($id);
}

function autoriser_produit_creer_bouton_dist($faire, $type, $id, $qui, $opt) {
	if( isset($opt['contexte']['id_rubrique']) )
		return produits_autoriser_creerproduitdans($opt['contexte']['id_rubrique']);
	else
		return true ;
}

function produits_autoriser_creerproduitdans($id_rubrique) {
	include_spip('inc/config');
	$config = lire_config('produits') ;
	
	if($id_rubrique && $config['limiter_ajout']) {
		// La rubrique est-elle dans un des secteurs ?
		spip_log("creerproduitdans config ".print_r($config,true),"produits");
		$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
		if(is_array($config['limiter_ident_secteur']) && !in_array($id_secteur,$config['limiter_ident_secteur']))
			return false ;
	}
	return true;
}


?>

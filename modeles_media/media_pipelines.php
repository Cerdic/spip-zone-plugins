<?php

function media_affiche_milieu($flux){
	
	if ($flux['args']['exec']=='config_fonctions'){
		$flux['data'] .= recuperer_fond('prive/configurer/media',array());
	}

	return $flux;
}

function media_configurer_liste_metas($metas){
	$metas['media_taille_icone_largeur'] = 52;
	$metas['media_taille_icone_hauteur'] = 52;
	$metas['media_taille_petit_largeur'] = 100;
	$metas['media_taille_petit_hauteur'] = 100;
	$metas['media_taille_moyen_largeur'] = 250;
	$metas['media_taille_moyen_hauteur'] = 250;
	$metas['media_taille_grand_largeur'] = 500;
	$metas['media_taille_grand_hauteur'] = 500;
	$metas['media_largeur_min_legende'] = 120;
	$metas['media_largeur_max_legende'] = 350;
	return $metas;
}

function media_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/media.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

function mediabox_insert_head($flux){
	$flux = media_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}

function media_header_prive($flux){
	$flux = media_insert_head_css($flux); // en bénéficier aussi dans l'espace privé
	return $flux;
}


?>
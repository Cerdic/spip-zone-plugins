<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function marquepages_trier($tableau){
	arsort($tableau);
	return $tableau;
}

function filtre_url_mp_dist($login){
	$url = generer_url_public('mp');
	return $url;
}

function filtre_url_mp_auteur_dist($login){
	$url = generer_url_public('mp');
	$url = parametre_url($url, 'login', $login);
	return $url;
}

function filtre_url_mp_editer_dist($id_forum){
	$url = generer_url_public('mp');
	$url = parametre_url($url, 'id_forum', $id_forum);
	return $url;
}

function filtre_url_mp_site_dist($id_syndic){
	$url = generer_url_public('mp');
	$url = parametre_url($url, 'id_syndic', $id_syndic);
	return $url;
}

function filtre_url_mp_tag_dist($tag, $login=''){
	$url = generer_url_public('mp');
	if ($login)
		$url = parametre_url($url, 'login', $login);
	$url = parametre_url($url, 'titre_mot', $tag);
	return $url;
}
function filtre_url_mp_supprimer_tag_dist($url, $tag){
	$url = parametre_url($url, 'titre_mot', '');
	return $rurl;
}

?>

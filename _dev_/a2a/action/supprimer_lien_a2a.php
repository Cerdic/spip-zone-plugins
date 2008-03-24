<?php

function action_supprimer_lien_a2a(){
	include_spip('inc/utils');
	include_spip('inc/headers');
	$contexte = array();
	$contexte['id_article_cible'] = _request('id_article_cible');
	$contexte['id_article'] = _request('id_article');
	
	$sql = "DELETE FROM spip_article_articles WHERE id_article = '".$contexte['id_article']."' AND id_article_lie = '".$contexte['id_article_cible']."';";
	$res = spip_query($sql);
	$redirect = generer_url_ecrire("articles", "id_article=".$contexte['id_article'], "&");
	redirige_par_entete($redirect);
	
}


?>

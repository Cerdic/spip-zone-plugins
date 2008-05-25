<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_a2a_lier_article(){
	include_spip('public/assembler');
	$contexte['id_article_cible'] = _request('id_article');
	$contexte['id_article_source'] = _request('id_article_orig');
	//on verifie que cet article n'est pas deja lie
	$sql_test_liaison = "SELECT * FROM spip_articles_lies WHERE id_article="._q($contexte['id_article_source'])." AND id_article_cible="._q($contexte['id_article_cible']);
	$res_test_liaison = spip_query($sql_test_liaison);
	if (spip_num_rows($res_test_liaison) == 0){
		//on recupere le rang le plus haut pour definir celui de l'article a lier
		$sql_rang = "SELECT MAX(rang) AS max FROM spip_articles_lies WHERE id_article="._q($contexte['id_article_source']);
		$res_rang = spip_query($sql_rang);
		$rang_max = spip_fetch_array($res_rang);
		$rang = $rang_max['max'] + 1;
		//on ajoute le lien vers l'article
		$sql_liaison = "INSERT INTO spip_articles_lies VALUES ("._q($contexte['id_article_source']).", "._q($contexte['id_article_cible']).","._q($rang).");";
		$res_liaison = spip_query($sql_liaison);
	}
	
	$redirect = generer_url_ecrire("articles", "id_article=".$contexte['id_article_source'], "&");
	redirige_par_entete($redirect);
}

?>

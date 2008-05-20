<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_a2a_lier_article(){
	include_spip('public/assembler');
	$contexte['id_article_cible'] = _request('id_article');
	$contexte['id_article_source'] = _request('id_article_orig');

	$sql_test_liaison = "SELECT * FROM spip_articles_lies WHERE id_article='".$contexte['id_article_source']."' AND id_article_cible='".$contexte['id_article_cible']."';";
	$res_test_liaison = spip_query($sql_test_liaison);
	if (spip_num_rows($res_test_liaison) == 0){
		$sql_liaison = "INSERT INTO spip_articles_lies VALUES ('".$contexte['id_article_source']."', '".$contexte['id_article_cible']."');";
		$res_liaison = spip_query($sql_liaison);
	}

	$redirect = generer_url_ecrire("articles", "id_article=".$contexte['id_article_source'], "&");
	redirige_par_entete($redirect);
}

?>

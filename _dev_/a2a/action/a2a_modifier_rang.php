<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_a2a_modifier_rang(){
	$id_article_cible = _request('id_article_cible');
	$id_article = _request('id_article');
	$type_modif = _request('modifier_rang');
	//on verifie que cet article n'est pas deja lie
	if ($type_modif == "plus"){
			//on recupere le rang de l'article à modifier
			$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . 'AND id_article_lie=' . sql_quote($id_article_cible));
			//on intervertit le rang de l'article suivant
			sql_update('spip_articles_lies', array('rang' => $rang), 'id_article=' . sql_quote($id_article) . 'AND rang=' . sql_quote($rang + 1));
			//on met à jour le rang de l'article à modifier
			sql_update('spip_articles_lies', array('rang' => ++$rang), 'id_article=' . sql_quote($id_article) . 'AND id_article_lie=' . sql_quote($id_article_cible));
	}
	if ($type_modif == "moins"){
			//on recupere le rang de l'article à modifier
			$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . 'AND id_article_lie=' . sql_quote($id_article_cible));
			//on intervertit le rang de l'article précédent
			sql_update('spip_articles_lies', array('rang' => $rang), 'id_article=' . sql_quote($id_article) . 'AND rang=' . sql_quote($rang - 1));
			//on met à jour le rang de l'article à modifier
			sql_update('spip_articles_lies', array('rang' => --$rang), 'id_article=' . sql_quote($id_article) . 'AND id_article_lie=' . sql_quote($id_article_cible));
	}
}

?>

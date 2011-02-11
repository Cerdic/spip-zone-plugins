<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function action_a2a_dist(){

	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	list($action, $id_article_cible, $id_article_source, $type) = explode('/',$args);

	if (!$action_a2a = charger_fonction('a2a_'.$action,'action')) {
		include_spip('inc/minipres');
		minipres(_L('Action a2a_'.$action.' introuvable'));
	}

	$action_a2a($id_article_cible, $id_article_source, $type);
	
	include_spip('inc/header');
	if ($redirect = _request('redirect'))
		redirige_par_entete(str_replace('&amp;','&',$redirect));
		
	redirige_par_entete(generer_url_ecrire("articles", "id_article=".$id_article_source, "&"));
}


function action_a2a_lier_article_dist($id_article_cible, $id_article_source,$type=null){
	//on verifie que cet article n'est pas deja lie
	if (!sql_countsel('spip_articles_lies', array(
		'id_article=' . sql_quote($id_article_source),
		'id_article_lie=' . sql_quote($id_article_cible)))){
			//on recupere le rang le plus haut pour definir celui de l'article a lier
			$rang = sql_getfetsel('MAX(rang)', 'spip_articles_lies', 'id_article='. sql_quote($id_article_source));
			//on ajoute le lien vers l'article
			sql_insertq('spip_articles_lies', array(
				'id_article' => $id_article_source,
				'id_article_lie' => $id_article_cible,
				'rang' => ++$rang
				));
	}
	if(($type == 'both') && !sql_countsel('spip_articles_lies', array(
		'id_article=' . sql_quote($id_article_cible),
		'id_article_lie=' . sql_quote($id_article_source)))){
			//on recupere le rang le plus haut pour definir celui de l'article a lier
			$rang = sql_getfetsel('MAX(rang)', 'spip_articles_lies', 'id_article='. sql_quote($id_article_cible));
			//on ajoute le lien vers l'article
			sql_insertq('spip_articles_lies', array(
				'id_article' => $id_article_cible,
				'id_article_lie' => $id_article_source,
				'rang' => ++$rang
				));
	}
	return true;
}


function action_a2a_supprimer_lien_dist($id_article_cible, $id_article,$type=null){
	include_spip('inc/utils');

	$contexte = array();
	
	$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . 'AND id_article_lie=' . sql_quote($id_article_cible));

	// on recupere les articles lies dont le rang est superieur a celui a supprimer
	$res = sql_select('*', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . 'AND rang>' . sql_quote($rang));
	//on boucle sur le resultat et on met a jour le rang des articles lies avant suppression
	while($r = sql_fetch($res)){
		sql_update('spip_articles_lies', array('rang' => sql_quote(--$r["rang"])), 'id_article=' . sql_quote($r["id_article"]) . 'AND id_article_lie=' . sql_quote($r["id_article_lie"]));
	}
	
	sql_delete('spip_articles_lies',  array(
		'id_article = ' . sql_quote($id_article), 
		'id_article_lie = ' . sql_quote($id_article_cible)
		));
	
	if($type == 'both'){
		$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article_cible) . 'AND id_article_lie=' . sql_quote($id_article));

		// on recupere les articles lies dont le rang est superieur a celui a supprimer
		$res = sql_select('*', 'spip_articles_lies', 'id_article=' . sql_quote($id_article_cible) . 'AND rang>' . sql_quote($rang));
		//on boucle sur le resultat et on met a jour le rang des articles lies avant suppression
		while($r = sql_fetch($res)){
			sql_update('spip_articles_lies', array('rang' => sql_quote(--$r["rang"])), 'id_article=' . sql_quote($r["id_article"]) . 'AND id_article_lie=' . sql_quote($r["id_article_lie"]));
		}
	
		sql_delete('spip_articles_lies',  array(
			'id_article = ' . sql_quote($id_article_cible), 
			'id_article_lie = ' . sql_quote($id_article)
			));
	}
	return true;
}


function action_a2a_modifier_rang_dist($id_article_cible, $id_article, $type_modif){
	
	if ($type_modif == "plus"){
			//on recupere le rang de l'article à modifier
			$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . ' AND id_article_lie=' . sql_quote($id_article_cible));
			//on intervertit le rang de l'article suivant
			sql_update('spip_articles_lies', array('rang' => $rang), 'id_article=' . sql_quote($id_article) . ' AND rang=' . sql_quote($rang + 1));
			//on met à jour le rang de l'article à modifier
			sql_update('spip_articles_lies', array('rang' => ++$rang), 'id_article=' . sql_quote($id_article) . ' AND id_article_lie=' . sql_quote($id_article_cible));
	}
	if ($type_modif == "moins"){
			//on recupere le rang de l'article à modifier
			$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . ' AND id_article_lie=' . sql_quote($id_article_cible));
			//on intervertit le rang de l'article précédent
			sql_update('spip_articles_lies', array('rang' => $rang), 'id_article=' . sql_quote($id_article) . ' AND rang=' . sql_quote($rang - 1));
			//on met à jour le rang de l'article à modifier
			sql_update('spip_articles_lies', array('rang' => --$rang), 'id_article=' . sql_quote($id_article) . ' AND id_article_lie=' . sql_quote($id_article_cible));
	}
	
	return true;
}


?>

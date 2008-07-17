<?php

function action_supprimer_lien_a2a(){
	include_spip('inc/utils');
	include_spip('inc/headers');
	$contexte = array();

	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	list($id_article_cible, $id_article) = explode('/',$args);
	
	$rang = sql_getfetsel('rang', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . 'AND id_article_lie=' . sql_quote($id_article_cible));

	// on récupère les articles liés dont le rang est supérieur à celui à supprimer
	$res = sql_select('*', 'spip_articles_lies', 'id_article=' . sql_quote($id_article) . 'AND rang>' . sql_quote($rang));
	//on boucle sur le résultat et on met à jour le rang des articles lies avant suppression
	while($r = sql_fetch($res)){
		sql_update('spip_articles_lies', array('rang' => sql_quote(--$r["rang"])), 'id_article=' . sql_quote($r["id_article"]) . 'AND id_article_lie=' . sql_quote($r["id_article_lie"]));
	}
	
	sql_delete('spip_articles_lies',  array(
		'id_article = ' . sql_quote($id_article), 
		'id_article_lie = ' . sql_quote($id_article_cible)
		));

	if ($redirect = _request('redirect'))
		redirige_par_entete(str_replace('&amp;','&',$redirect));	
}

?>

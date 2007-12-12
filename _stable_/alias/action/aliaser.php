<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function alias_aliaser_objet($id_article) {
	include_spip('action/editer_article');
	$res = spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article="._q($id_article));
	$new = 0;
	if ($row = spip_fetch_array($res)) {
		$new = insert_article($row['id_rubrique']);
		articles_set($new,array(
		'surtitre' => "<article$id_article|surtitre>",
		'titre' => "<article$id_article|titre>",
		'soustitre' => "<article$id_article|soustitre>",
		'descriptif' => "<article$id_article|descriptif>",
		'chapo' => "<article$id_article|chapo>",
		'texte' => "<article$id_article|texte>",
		'ps' => "<article$id_article|ps>",
		));
	}
	return $new;
}

function action_aliaser_dist() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = explode('-',$arg);
	$type = 'article';
	if (preg_match(',^\w*$,',$arg[0]))
		$type = $arg[0];

	$id_article = alias_aliaser_objet(intval($arg[1]));
	$retour = parametre_url(urldecode(_request('redirect')),'id_article',$id_article,'&');
	include_spip('inc/headers');
	redirige_par_entete($retour);
}

?>
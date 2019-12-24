<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function alias_aliaser_objet($id_article) {
	if (version_compare($GLOBALS['spip_version_code'], '2', '<')) {
		$res = spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article=" . _q($id_article));
		$row = spip_fetch_array($res);
	} else {
		include_spip('base/abstract_sql');
		$row = sql_fetsel('id_rubrique', 'spip_articles', 'id_article=' . intval($id_article));
	}
	$c = array(
		'surtitre' => "<article$id_article|surtitre>",
		'titre' => "<article$id_article|titre>",
		'soustitre' => "<article$id_article|soustitre>",
		'descriptif' => "<article$id_article|descriptif>",
		'chapo' => "<article$id_article|chapo>",
		'texte' => "<article$id_article|texte>",
		'ps' => "<article$id_article|ps>",
	);
	$new = 0;
	if ($row) {
		include_spip('action/editer_article');
		$new = insert_article($row['id_rubrique']);
		if (version_compare($GLOBALS['spip_version_code'], '2', '<')) {
			articles_set($new, $c);
		} else {
			include_spip('inc/modifier');
			revision_article($new, $c);
			// et on peut meme aliaser le portfolio
			$rows = sql_allfetsel('id_document', 'spip_documents_liens', "objet='article' AND id_objet=" . intval($id_article));
			foreach ($rows as $k => $row) {
				$rows[$k] = array('id_objet' => $new, 'id_document' => $row['id_document'], 'objet' => 'article');
			}
			if (count($rows)) {
				sql_insertq_multi('spip_documents_liens', $rows);
			}
		}
	}

	return $new;
}

function action_aliaser_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = explode('-', $arg);
	$type = 'article';
	if (preg_match(',^\w*$,', $arg[0])) {
		$type = $arg[0];
	}

	$id_article = alias_aliaser_objet(intval($arg[1]));
	$retour = parametre_url(urldecode(_request('redirect')), 'id_article', $id_article, '&');
	include_spip('inc/headers');
	redirige_par_entete($retour);
}

?>

<?php

function metas_opengraph_insert_head($flux) {


/*
	$id_article = $GLOBALS["contexte"]["id_article"];

	if ($id_article > 0) {
		$contexte = array('id_article'=>$id_article);
		$page = evaluer_fond("metas_opengraph", $contexte);
		
		$flux .= $page["texte"];
	}
*/

	return $flux;
}


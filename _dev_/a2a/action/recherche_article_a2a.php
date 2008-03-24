<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_recherche_article_a2a(){
	include_spip('public/assembler');
	$contexte['recherche'] = _request('recherche');
	$contexte['id_article_orig'] = _request('id_article');
	$data = recuperer_fond('fonds/recherche_articles_a2a', $contexte);
	echo $data;
}

?>

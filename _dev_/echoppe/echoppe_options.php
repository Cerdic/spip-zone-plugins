<?php
	include_spip('base/echoppe');
	if (!isset($GLOBALS['auteur_session']['echoppe']['token_panier'])){
		$GLOBALS['auteur_session']['echoppe']['token_panier'] = md5(uniqid(rand(), true));
	}
	/*$tables_auxiliaires['spip_echoppe_categories'] = array(
	'field' => &$spip_echoppe_categories_descriptions,
	'key' => &$spip_documents_formations);*/
?>

<?php

function courtcircuit_url_redirection($id_rubrique) {
	$url = '';
	include_spip('inc/utils');
	include_spip('inc/headers');
	if (isset($GLOBALS['meta']['courtcircuit']))
		$config = unserialize($GLOBALS['meta']['courtcircuit']);
	else $config = array();
	$redirect_article = recuperer_fond(
		'courtcircuit_selection_article', 
		array_merge(array('id_rubrique' => $id_rubrique),$config)
		);
	if (intval($redirect_article))
		$url = generer_url_entite(intval($redirect_article), 'article', '', '', true);
	else {
		$redirect_rubrique = recuperer_fond(
			'courtcircuit_selection_rubrique', 
			array_merge(array('id_rubrique' => $id_rubrique),$config)
			);
		if (intval($redirect_rubrique))
			$url = generer_url_entite(intval($redirect_rubrique), 'rubrique', '', '', true);
	}
	return $url;
}

?>
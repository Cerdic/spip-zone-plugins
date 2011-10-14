<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function courtcircuit_url_redirection($id_rubrique) {
	$url = '';
	include_spip('inc/utils');
	include_spip('inc/headers');
	if (isset($GLOBALS['meta']['courtcircuit']))
		$config = unserialize($GLOBALS['meta']['courtcircuit']);
	else $config = array();
	// Tester d'abord les variantes de squelettes (si on ne les court-circuite pas)
	if (!isset($config['variantes_squelettes']) || $config['variantes_squelettes']=='oui') {
		$squelette_rubrique = substr(find_in_path('rubrique.html'),0,-5);
		$flux = array(
			'data' => $squelette_rubrique,
			'args' => array(
				'ext' => 'html',
				'id_rubrique' => $id_rubrique
			)
		);
		include_spip('public/styliser');
		$flux = styliser_par_rubrique($flux);
		if ($flux['data'] != $squelette_rubrique)
			return '';
	}
	// Tester ensuite si la rubrique a une composition (si on ne court-circuite pas les compositions)
	if ((!isset($config['composition_rubrique']) || $config['composition_rubrique']=='oui') && defined('_DIR_PLUGIN_COMPOSITIONS')) {
		if (strlen(compositions_determiner('rubrique', $id_rubrique)))
			return '';
	}
	// On teste si on doit rediriger
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
		if (intval($redirect_rubrique)) {
			// On applique à nouveau les règles de sélection à la sous-rubrique
			// Si pas de redirectio on pointe sur la sous-rubrique
			$redirection_sous_rubrique = courtcircuit_url_redirection(intval($redirect_rubrique));
			if ($redirection_sous_rubrique != '')
				$url = $redirection_sous_rubrique;
			else
				$url = generer_url_entite(intval($redirect_rubrique), 'rubrique', '', '', true);
		}
	}
	return $url;
}

?>
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
		if(function_exists('squelettes_par_rubrique_styliser_par_rubrique'))
			$flux = squelettes_par_rubrique_styliser_par_rubrique($flux);
		else {
			$flux = styliser_par_rubrique($flux);
		}
		if ($flux['data'] != $squelette_rubrique)
			return '';
	}
	// Tester ensuite si la rubrique a une composition (si on ne court-circuite pas les compositions)
	if ((!isset($config['composition_rubrique']) || $config['composition_rubrique']=='oui') && defined('_DIR_PLUGIN_COMPOSITIONS')) {
		if (strlen(compositions_determiner('rubrique', $id_rubrique)))
			return '';
	}
	// Déterminer le fond à utiliser
		if (isset($config['restreindre_langue']) && $config['restreindre_langue']=='oui')
			$fond = 'courtcircuit_selection_article_lang';
		else
			$fond = 'courtcircuit_selection_article';

	// On teste si on doit rediriger
	$redirect_article = recuperer_fond(
			$fond, 
			array_merge(array('id_rubrique' => $id_rubrique),$config)
		);
	if (intval($redirect_article))
		$url = generer_url_entite(intval($redirect_article), 'article', '', '', true);
	else {
		$redirect_rubrique = recuperer_fond(
			'courtcircuit_selection_rubrique', 
			array_merge(array('id_rubrique' => $id_rubrique,'id_parent' => $id_rubrique),$config)
			);
		if (intval($redirect_rubrique)) {
			// On applique a nouveau les regles de selection a la sous-rubrique
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
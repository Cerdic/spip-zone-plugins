<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * si on a configuré multilang pour s'insérer dans l'espace public
 *
 * @param string $flux Le contenu du head CSS
 * @return string $flux Le contenu du head CSS modifié
 */
function multilang_insert_head_css($flux) {
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}

	$multilang_public = lire_config('multilang/multilang_public', 'off');
	if ($multilang_public == 'on') {
		static $done = false;
		if (!$done) {
			$done = true;
			$css = timestamp(produire_fond_statique('multilang.css'));
			$flux .= '<link rel="stylesheet" href="'.$css.'" type="text/css" media="all" />';
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_prive (SPIP)
 * Ajoute css et javascript dans le <head> privé
 *
 * @param string $flux
 * 		Le contenu du head
 * @return string $flux
 * 		Le contenu du head modifié
 */
function multilang_insert_head_prive($flux) {
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}
	$config = lire_config('multilang', array());

	$flux .= multilang_inserer_head($config);

	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * si on a configuré multilang pour s'insérer dans l'espace public
 *
 * @param string $flux
 * 		Le contenu du head
 * @return string $flux
 * 		Le contenu du head modifié
 */
function multilang_insert_head($flux) {
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}
	$config = lire_config('multilang', array());

	if (isset($config['multilang_public']) and ($config['multilang_public'] == 'on')) {
		$flux .= multilang_insert_head_css(''); // au cas ou il n'est pas implemente
		$flux .= multilang_inserer_head($config);
	}
	return $flux;
}

/**
 * La fonction de modification du $flux pour l'insertion dans le head qu'il
 * soit privé ou public
 *
 * @param array $config La configuration du plugin
 * @return string $data Le contenu textuel qui sera inséré dans le head
 */
function multilang_inserer_head($config = array()) {
	/**
	 * N'activer multilang que si plus d'une langue dans le site
	 */
	if (count(explode(',', $GLOBALS['meta']['langues_multilingue'])) > 1) {
		$data = '
<script type="text/javascript" src="'.$css = timestamp(produire_fond_statique('multilang.js', array('lang' => $GLOBALS['spip_lang']))).'"></script>
';
	}
	return $data;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * On purge le cache js à chaque changement de la config de langue
 *
 * @param $flux array
 * 		Le contexte du pipeline
 * @return $flux array
 * 		Le contexte du pipeline modifié
 */
function multilang_formulaire_traiter($flux) {
	if ($flux['args']['form'] == 'configurer_multilinguisme') {
		include_spip('inc/invalideur');
		$rep_js = _DIR_VAR.'cache-js/';
		$rep_css = _DIR_VAR.'cache-css/';
		purger_repertoire($rep_js);
		purger_repertoire($rep_css);
		suivre_invalideur('1');
	}
	return $flux;
}

/**
 * Modifie le résultat de la compilation des squelettes
 *
 * Sur la page crayons.js, on insère également notre javascript pour être utilisable
 * dans les crayons
 *
 * @note
 * Pour crayons v2+
 * Pour les versions précédentes, voir dans affichage_final
 * On fait l'économie du test de version, car ce sont 2 pipelines différents selon les versions.
 *
 * @param string $flux Le contenu de la page
 * @return string $flux Le contenu de la page modifiée
 */
function multilang_recuperer_fond($flux) {
	if (
		$flux['args']['fond'] === 'crayons.js'
		and (count(explode(',', $GLOBALS['meta']['langues_multilingue'])) > 1)
		and include_spip('inc/config')
		and $config = lire_config('multilang', array())
		and ($config['multilang_public'] == 'on')
		and ($config['multilang_crayons'] == 'on')
	) {
		include_spip('javascript/multilang_crayons');
		$flux['data']['texte'] .= multilang_javascript_crayons($config);
	}

	return $flux;
}

/**
 * Insertion dans le pipeline affichage_final (SPIP)
 *
 * Sur la page crayons.js, on insère également notre javascript pour être utilisable
 * dans les crayons
 *
 * @note
 * Pour crayons < v2
 * Pour les versions ultérieures, voir dans recuperer_fond
 * On fait l'économie du test de version, car ce sont 2 pipelines différents selon les versions.
 *
 * @param string $flux Le contenu de la page
 * @return string $flux Le contenu de la page modifiée
 */
function multilang_affichage_final($flux) {
	if (
		isset($_REQUEST['page'])
		and $_REQUEST['page'] == 'crayons.js'
		and (count(explode(',', $GLOBALS['meta']['langues_multilingue'])) > 1)
		and include_spip('inc/config')
		and $config = lire_config('multilang', array())
		and ($config['multilang_public'] == 'on')
		and ($config['multilang_crayons'] == 'on')
	) {
		include_spip('javascript/multilang_crayons');
		$flux .= multilang_javascript_crayons($config);
	}
	return $flux;
}

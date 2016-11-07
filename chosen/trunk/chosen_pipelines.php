<?php
/**
 * Plugin Chosen
 * (c) 2015 Marcillaud Matthieu, kent1
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajoute Chosen aux plugins JS chargés
 *
 * @param array $flux
 *     Liste des js chargés
 * @return array
 *     Liste complétée des js chargés
**/
function chosen_jquery_plugins($flux) {
	include_spip('inc/config');
	$config = lire_config('chosen/active', 'non');
	if (test_espace_prive() || $config =='oui') {
		$flux[] = 'lib/chosen/chosen.jquery.js'; # lib originale
		$flux[] = 'javascript/spip_chosen.js';   # chargements SPIP automatiques
	}
	return $flux;
}

/**
 * Ajoute Chosen aux css chargées dans le privé
 *
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
 */
function chosen_header_prive($texte) {
	include_spip('inc/config');
	$texte .= '<script type="text/javascript">/* <![CDATA[ */
			var selecteur_chosen = "' . trim(lire_config('chosen/selecteur_commun')) . '";
			var langue_chosen = {
				placeholder_text_single : "'.texte_script(_T('chosen:lang_select_an_option')).'",
				placeholder_text_multiple : "'.texte_script(_T('chosen:lang_select_some_option')).'",
				no_results_text : "'.texte_script(_T('chosen:lang_no_result')).'"
			};
			var chosen_create_option = {
				create_option: function(term) {
					this.select_append_option( {value: "chosen_" + term, text: term} );
				},
				persistent_create_option: true,
				skip_no_results: true,
				create_option_text: "'.texte_script(_T('chosen:lang_create_option')).'"
				};
/* ]]> */</script>'."\n";

	return $texte;
}

/**
 * Ajoute Chosen aux css chargées dans le privé
 *
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
 */
function chosen_header_prive_css($texte) {

	$css = sinon(find_in_path('css/chosen.css'), find_in_path('lib/chosen/chosen.css'));
	$texte .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";
	$css = find_in_path('css/spip.chosen.css');
	$texte .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";

	return $texte;
}

/**
 * Ajoute Chosen aux css chargées dans le public
 *
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function chosen_insert_head_css($flux) {
	include_spip('inc/config');
	$config = lire_config('chosen', array());
	if (isset($config['active']) and $config['active']=='oui') {
		$css = sinon(find_in_path('css/chosen_public.css'), sinon(find_in_path('css/chosen.css'), find_in_path('lib/chosen/chosen.css')));
		$flux .= '<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />';
		$css = find_in_path('css/spip.chosen.css');
		$flux .= '<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />';
	}
	return $flux;
}

/**
 * Ajoute Chosen aux css chargées dans le public
 *
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function chosen_insert_head($flux) {
	include_spip('inc/config');
	$config = lire_config('chosen', array());
	if (isset($config['active']) and $config['active']=='oui') {
		$flux .= '<script type="text/javascript">/* <![CDATA[ */
			var selecteur_chosen = "' . trim(isset($config['selecteur_commun']) ? $config['selecteur_commun'] : '') . '";
			var langue_chosen = {
				placeholder_text_single : "'.texte_script(_T('chosen:lang_select_an_option')).'",
				placeholder_text_multiple : "'.texte_script(_T('chosen:lang_select_some_option')).'",
				no_results_text : "'.texte_script(_T('chosen:lang_no_result')).'"
			};
			var chosen_create_option = {
				create_option: function(term) {
					this.select_append_option( {value: "chosen_" + term, text: term} );
				},
				persistent_create_option: true,
				skip_no_results: true,
				create_option_text: "'.texte_script(_T('chosen:lang_create_option')).'"
			};
/* ]]> */</script>'."\n";
	}
	return $flux;
}

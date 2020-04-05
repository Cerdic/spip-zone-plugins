<?php
/**
 * Plugin Chosen
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajoute Chosen aux plugins JS chargés
 * 
 * @param array $flux
 *     Liste des js chargés
 * @return array
 *     Liste complétée des js chargés
**/
function chosen_jquery_plugins($flux) {
	$flux[] = 'lib/chosen/chosen.jquery.js'; # lib originale
	$flux[] = 'javascript/spip_chosen.js';   # chargements SPIP automatiques
	return $flux;
}

/**
 * Ajoute Chosen aux css chargées dans le privé
 * 
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
 */
function chosen_header_prive($texte) {
	$texte .= '<script type="text/javascript">/* <![CDATA[ */
			var langue_chosen = {
				placeholder_text_single : "'.texte_script(_T('chosen:lang_select_an_option')).'",
				placeholder_text_multiple : "'.texte_script(_T('chosen:lang_select_some_option')).'",
				no_results_text : "'.texte_script(_T('chosen:lang_no_result')).'"
			}
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

	$css = find_in_path('lib/chosen/chosen.css');
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
	$config = lire_config('chosen',array());
	if (isset($config['active']) and $config['active']=='oui'){
		$css = find_in_path('lib/chosen/chosen.css');
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
	$config = lire_config('chosen',array());
	if (isset($config['active']) and $config['active']=='oui') {
		$flux .= '<script type="text/javascript">/* <![CDATA[ */
			var selecteur_chosen = "' . trim($config['selecteur_commun']) . '";
			var langue_chosen = {
				placeholder_text_single : "'.texte_script(_T('chosen:lang_select_an_option')).'",
				placeholder_text_multiple : "'.texte_script(_T('chosen:lang_select_some_option')).'",
				no_results_text : "'.texte_script(_T('chosen:lang_no_result')).'",
			}
/* ]]> */</script>'."\n";
	}
	return $flux;
}

?>

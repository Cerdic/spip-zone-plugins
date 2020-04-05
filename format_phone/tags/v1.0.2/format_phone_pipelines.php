<?php
/**
 * Utilisations de pipelines par Formatteur de n° de téléphone
 *
 * @plugin     Formatteur de n° de téléphone
 * @copyright  2016
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 * @package    SPIP\Format_phone\Pipelines
 */

/**
 * Ajoute le js de la librairie
 *
 * @pipeline jquery_plugins
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function format_phone_jquery_plugins($scripts) {

	if (lire_config('auto_compress_js')) {
		$scripts[] = 'lib/phoneformat.js-master/dist/phone-format.min.js';
	} else {
		$scripts[] = 'lib/phoneformat.js-master/dist/phone-format.js';
	}

	return $scripts;
}

/**
 * Retourne le js dynamique à mettre dans le head
 */
function format_phone_head() {

	return recuperer_fond('inclure/head-format-phone');
}

function format_phone_header_prive($flux) {

	$flux .= format_phone_head();

	return $flux;
}

function format_phone_insert_head($flux) {

	$flux .= format_phone_head();

	return $flux;
}

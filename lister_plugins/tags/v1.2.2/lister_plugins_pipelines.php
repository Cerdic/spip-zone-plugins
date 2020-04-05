<?php
/**
 * Fichier de définition des pipelines
 *
 * @plugin     Lister les plugins nécessaires à votre site
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/ListerPlugins/Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function lister_plugins_listermenu($flux) {
	$flux['data']['lister_plugins'] = array(
		'titre' => _T('lister_plugins:titre_lister_plugins'),
		'icone' => 'prive/themes/spip/images/lister_plugins-16.png',
	);

	return $flux;
}

<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');


function formulaires_configurer_twidget_charger_dist() {
	$config = unserialize($GLOBALS['meta']['twidget']);

	if ($config['search'])
		$valeurs['search'] = $config['search'];
	else
		$valeurs['search'] = "#spip";

	if ($config['interval'])
		$valeurs['interval'] = $config['interval'];
	else
		$valeurs['interval'] = "30000";

	if ($config['title'])
		$valeurs['title'] = $config['title'];
	else
		$valeurs['title'] = "Du nouveau sur Twitter";

	if ($config['subject'])
		$valeurs['subject'] = $config['subject'];
	else
		$valeurs['subject'] = "SPIP";

	if ($config['width'])
		$valeurs['width'] = $config['width'];
	else
		$valeurs['width'] = "250";

	if ($config['height'])
		$valeurs['height'] = $config['height'];
	else
		$valeurs['height'] = "300";

	if ($config['shell_background'])
		$valeurs['shell_background'] = $config['shell_background'];
	else
		$valeurs['shell_background'] = "#8ec1da";

	if ($config['shell_color'])
		$valeurs['shell_color'] = $config['shell_color'];
	else
		$valeurs['shell_color'] = "#ffffff";

	if ($config['tweets_background'])
		$valeurs['tweets_background'] = $config['tweets_background'];
	else
		$valeurs['tweets_background'] = "#ffffff";


	if ($config['tweets_color'])
		$valeurs['tweets_color'] = $config['tweets_color'];
	else
		$valeurs['tweets_color'] = "#444444";


	if ($config['tweets_link'])
		$valeurs['tweets_link'] = $config['tweets_link'];
	else
		$valeurs['tweets_link'] = "#1986b5";

	if ($config['rpp'])
		$valeurs['rpp'] = $config['rpp'];
	else
		$valeurs['rpp'] = "4";


	if ($config['user'])
		$valeurs['user'] = $config['user'];
	else
		$valeurs['user'] = "spip";


	return $valeurs;
}

/**
 */
function formulaires_configurer_twidget_traiter_dist() {

	$config = array();
	$valeurs = formulaires_configurer_twidget_charger_dist();

	include_spip('inc/meta');
	foreach ($valeurs as $k=>$v){
		if (!is_null(_request($k))) {
				$config[$k] = _request($k);
		}
	}
	ecrire_meta('twidget',serialize($config));

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);

}

?>
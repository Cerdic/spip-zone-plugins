<?php
/**
 * Fonctions utiles au plugin MAE
 *
 * @plugin     Timezone
 * @copyright  2014
 * @author     kent1
 * @licence    GNU/GPL v3
 * @package    SPIP\Timezone\Options
 */


if (function_exists("date_default_timezone_set")){
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	if(($timezone = lire_config('timezone')) && $timezone != '')
		date_default_timezone_set($timezone);
}
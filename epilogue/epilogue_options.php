<?php
/**
 * Options au chargement du plugin epilogue
 *
 * @plugin     epilogue
 * @copyright  2018
 * @author     Amaury Adon
 * @licence    GNU/GPL
 * @package    SPIP\Epilogue\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
if (!isset($GLOBALS['z_blocs']))
	$GLOBALS['z_blocs'] = array('content','aside','extra','head','head_js','header','footer','breadcrumb');
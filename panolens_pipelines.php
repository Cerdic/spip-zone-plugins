<?php
/**
 * Utilisations de pipelines par Panolens
 *
 * @plugin     Panolens
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Panolens\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function panolens_instantiation(){

	$config = lire_config("panolens");

	$js = 'var panolens_options = {};';

	$controlButtons = "'".implode("','",$config["controlButtons"])."'";

	if (empty($config["controlBar"]))
		$js .= 'panolens_options.controlBar = false;';

	if (!empty($controlButtons))
		$js .= 'panolens_options.controlButtons = ['.$controlButtons.'];';

	if (!empty($config["autoHideControlBar"]))
		$js .= 'panolens_options.autoHideControlBar = true;';

	if (!empty($config["autoHideInfospot"]))
		$js .= 'panolens_options.autoHideInfospot = true;';

	if (!empty($config["cameraFov"]))
		$js .= 'panolens_options.cameraFov = '.$config["cameraFov"].';';

	if (!empty($config["reverseDragging"]))
		$js .= 'panolens_options.reverseDragging = true;';

	if (!empty($config["enableReticle"]))
		$js .= 'panolens_options.enableReticle = true;';

	if (!empty($config["dwellTime"]))
		$js .= 'panolens_options.dwellTime = '.$config["dwellTime"].';';

	if (!empty($config["autoReticleSelect"]))
		$js .= 'panolens_options.autoReticleSelect = true;';

	if (!empty($config["viewIndicator"]))
		$js .= 'panolens_options.viewIndicator = true;';

	if (!empty($config["indicatorSize"]))
		$js .= 'panolens_options.indicatorSize = '.$config["indicatorSize"].';';

	$js .= 'panolens_options.output = '.$config["output"].';';

	$js .=  '$(document).ready(function(){';
	$js .=  '$(".panorama-panolens").each(function(index,el){';
	$js .=	'	panolens_options.container=el;console.log(panolens_options);';
	$js .=  ' var image = $(el).data("src"), panorama = new PANOLENS.ImagePanorama(image), viewer = new PANOLENS.Viewer(panolens_options);';
	$js .=  ' viewer.add(panorama);';
	$js .=  '})';
	$js .=  '})';

	return $js;

}


function panolens_insert_head($flux) {

	$panolens = find_in_path('lib/Panolens/build/panolens.min.js');
	$three = find_in_path('lib/three.min.js');
	$flux .='<script src="'.$three.'"	type="text/javascript"></script>';
	$flux .='<script src="'.$panolens.'"	type="text/javascript"></script>';

	$flux .='<script type="text/javascript">'.panolens_instantiation().'</script>';

	return $flux;

}

function panolens_insert_head_css($flux) {

	$panolens_spip_css = find_in_path('panolens_spip.css');
	$flux .='<link rel="stylesheet" type="text/css" href="'.$panolens_spip_css.'">';

	return $flux;
}

function panolens_header_prive($flux) {

	$panolens = find_in_path('lib/Panolens/build/panolens.min.js');
	$three = find_in_path('lib/three.min.js');
	$flux .='<script src="'.$three.'"	type="text/javascript"></script>';
	$flux .='<script src="'.$panolens.'"	type="text/javascript"></script>';

	$flux .='<script type="text/javascript">'.panolens_instantiation().'</script>';

	$panolens_spip_css = find_in_path('panolens_spip.css');
	$flux .='<link rel="stylesheet" type="text/css" href="'.$panolens_spip_css.'">';

	return $flux;
}

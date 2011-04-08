<?php
/**
 * @name 		JavascriptPopup_pipelines
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function spipopup_insert_head($flux){
	spipopup_config();
	$flux .= "\n<script src='".find_in_path('javascript/spipopup.js')."' type='text/javascript'></script>"
		."\n<script type='text/javascript'>var popup_settings={default_popup_name:'".POPUP_TITRE."',default_popup_width:'".POPUP_WIDTH."',default_popup_height:'".POPUP_HEIGHT."'};</script>";
	return $flux;
}

?>
<?php
/**
 * @name 		JavascriptPopup_pipelines
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion du Javascript en en-tete
 * @necessite La balise #INSERT_HEAD en en-tete de squelettes
 * @utilise Pipeline 'insert_head'
 */
function spipopup_insert_head($flux){
	spipopup_config();
	$flux .= "\n<script src='".find_in_path('javascript/spipopup.js')
		."' type='text/javascript'></script>"
		."\n<script type='text/javascript'>var popup_settings={default_popup_name:'"
		.POPUP_TITRE."',default_popup_width:'".POPUP_WIDTH."',default_popup_height:'"
		.POPUP_HEIGHT."',default_popup_options:'".POPUP_OPTIONS_DEFAUT."'};</script>"
		."\n";
	return $flux;
}

function spipopup_porte_plume_barre_pre_charger($barres){
	$barre = &$barres['edition'];

	$link = $barre->get('link');
	$link["dropMenu"] = array(
			// poesie spip
			array(
				"id"          => 'popup_link',
				"name"        => _T('spipopup:inserer_lien_popup'),
				"className"   => "outil_barre_popup", 
				"replaceWith" => 'function(markitup) { zone_selection = markitup.textarea; window.open("?exec=popup_edit", "popup_editor","scrollbars=yes,resizable=yes,width=480,height=400"); }',
				"display"     => true,
			),
	);
	$barre->set('link', $link);	

/*
				"replaceWith" => 'function(markitup) { zone_selection = markitup.textarea; window.open("?exec=popup_edit", "popup_editor","scrollbars=yes,resizable=yes,width=400,height=400"); }',

				"selectionType" => "line",
				"forceMultiline" => true,

				"openWith"    => "<popup|texte=", 
				"closeWith"   => "->|lien=[!["._T('spipopup:barre_lien_input')."]!]>",
*/

	return $barres;
}

function spipopup_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_barre_popup' => array('popup.png','0'),
	));
}

?>
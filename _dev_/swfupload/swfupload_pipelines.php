<?php
function swfupload_ajouterBoutons($boutons_admin) {
		// si on est admin ou admin restreint
		if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
		//AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['swfupload_admin']= new Bouton(
			"../"._DIR_PLUGIN_SWFUPLOAD."/images/swfupload-24.png",  // icone
			_T('swfupload:SWFupload')	// titre
			);
		}
		return $boutons_admin;
}


function swfupload_header_prive($flux) {
global $connect_statut,$connect_toutes_rubriques;

if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
$session = session_id();

// si cfg dispo, on charge les valeurs
if (function_exists(lire_config))  {   
  $file_size_limit = lire_config('swfupload/file_size_limit');
  $file_types = lire_config('swfupload/file_types');
  $file_upload_limit = lire_config('swfupload/file_upload_limit');
  $debug = lire_config('swfupload/debug');
}

if (!$file_size_limit || $file_size_limit == '_' || $file_size_limit == '') $file_size_limit = '2048' ;
if (!$file_types || $file_types == '_' || $file_types == '') $file_types = "*.jpg;*.gif;*.png" ;
if (!$file_upload_limit || $file_upload_limit == '_' || $file_upload_limit == '') $file_upload_limit = '0' ;
if (!$debug || $debug == '_' || $debug == '') $debug = "false" ;

$flux .= '<!-- swfupload -->
	<link href="'._DIR_PLUGIN_SWFUPLOAD.'css/default.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'swfupload/swfupload.js"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'swfupload/js/swfupload.queue.js"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'swfupload/js/fileprogress.js"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'swfupload/js/handlers.js"></script>
	<script type="text/javascript">
		var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Flash Settings '._DIR_PLUGIN_SWFUPLOAD.'
				flash_url : "'.url_absolue(_DIR_PLUGIN_SWFUPLOAD).'swfupload/swfupload.swf",	// Relative to this file
				// Backend Settings - Relative to the SWF file, utiliser url absolue cest mieux					
				upload_url: "'.$GLOBALS['meta']["adresse_site"].'/?page=swfupload",

				post_params: {"PHPSESSID": "'.$session.'"},
				// File Upload Settings
				file_size_limit : "'.$file_size_limit.'",	// 2MB
				file_types : "'.$file_types.'",
				file_types_description : "Allowed Files",
				file_upload_limit : "'.$file_upload_limit.'",
	
				// Button Settings
				button_image_url : "'._DIR_PLUGIN_SWFUPLOAD.'images/XPButtonUploadText_61x22.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 61,
				button_height: 22,
				
				// Event Handler Settings (all my handlers are in the Handler.js file)
				file_dialog_start_handler : fileDialogStart,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete,	// Queue plugin event
				
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				
				// Debug Settings
				debug: '.$debug.'
			});
		}
	</script>';
}
	return $flux;
}
?>
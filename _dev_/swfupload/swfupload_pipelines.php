<?
/* public static */
	function swfupload_ajouterBoutons($boutons_admin) {
		// si on est admin ou admin restreint
		if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
		//AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['swfupload_admin']= new Bouton(
			"../"._DIR_PLUGIN_SWFUPLOAD."/image-24.gif",  // icone
			_T('jupload:SWFupload')	// titre
			);
		}
		return $boutons_admin;
	}

function swfupload_ajouterSWF($flux) {
$session = session_id();
$upload_dir = "../".determine_upload();

$file_size_limit = lire_config('swfupload/file_size_limit');
$file_types = lire_config('swfupload/file_types');
$file_upload_limit = lire_config('swfupload/file_upload_limit');
$debug = lire_config('swfupload/debug');

if (!$file_size_limit || $file_size_limit == '_' || $file_size_limit == '') $file_size_limit = '2048' ;
if (!$file_types || $file_types == '_' || $file_types == '') $file_types = "*.jpg;*.gif;*.png" ;
if (!$file_upload_limit || $file_upload_limit == '_' || $file_upload_limit == '') $file_upload_limit = '0' ;
if (!$debug || $debug == '_' || $debug == '') $debug = "false" ;

$flux .= '
<link href="';
$flux .= _DIR_PLUGIN_SWFUPLOAD.'/css/default.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'swfupload/swfupload.js"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'js/handlers.js"></script>
	<script type="text/javascript">
		var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: "../upload.php",	// ../upload.phpRelative to the SWF file
				post_params: {"PHPSESSID": "'.$session.'","UPLOAD_DIR": "'.$upload_dir.'"},
				// File Upload Settings
				file_size_limit : "'.$file_size_limit.'",	// 2MB
				file_types : "'.$file_types.'",
				file_types_description : "Allowed Files",
				file_upload_limit : "'.$file_upload_limit.'",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Flash Settings
				flash_url : "'._DIR_PLUGIN_SWFUPLOAD.'swfupload/swfupload_f9.swf",	// Relative to this file

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: '.$debug.'
			});
		}
	</script>';
	return $flux;
	//}
}
?>
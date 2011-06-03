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

$id_article=intval(_request('id_article'));

if ((_request('exec') == 'articles' || _request('exec') == 'swfupload_admin')  AND ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"])) {
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

				post_params: {
				"PHPSESSID": "'.$session.'",
				"id_article": "'.$id_article.'"
				},
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

function swfupload_affiche_milieu($flux){
global $connect_statut,$connect_toutes_rubriques;

 $args = $flux['args'];
 $out = "";
 $id_article = $args['id_article'];
if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
	if ($args['exec']=='articles') {
	$bouton = _T('swfupload:titre_swfupload');
	if(function_exists('bouton_block_depliable')) 
      $out .= debut_cadre_enfonce(_DIR_PLUGIN_SWFUPLOAD.'images/swfupload-24.png', true, "", 
			    bouton_block_depliable($bouton,$flag === 'ajax','swfupload'))
		    . debut_block_depliable($flag === 'ajax','swfupload');
  else 
  	  $out .= debut_cadre_enfonce(_DIR_PLUGIN_SWFUPLOAD.'images/swfupload-24.png', true, "", "");
	

$out .= '<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
		<div>'._T('swfupload:texte_swfupload_article').'</div>
<input type="hidden" value='.$id_article.' name="id_article" />
		<div class="content">
			<fieldset class="flash" id="fsUploadProgress">
				<legend>'._T('swfupload:texte_uploadqueue').'</legend>
			</fieldset>
			<div id="divStatus">0 '._T('swfupload:texte_filesupload').'</div>
			<div style="padding-left: 5px;">
				<span id="spanButtonPlaceholder"></span>
				<input id="btnCancel" type="button" value="'._T('swfupload:texte_cancelupload').'" onclick="cancelQueue(swfu);" disabled="disabled"  style="margin-left: 2px; height: 22px; font-size: 8pt;" /><br />
			</div>
		</div>
	</form>';
	
$out .= fin_block()
		. fin_cadre_enfonce(true);
	}
	$flux['data'].= $out;
	}
	return $flux;	
}
?>

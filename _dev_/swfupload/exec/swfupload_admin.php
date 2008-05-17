<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions'); // *action_auteur et determine_upload

session_start();
$_SESSION["file_info"] = array();

function exec_swfupload_admin_dist()
{
global $connect_statut, $connect_login, $connect_toutes_rubriques, $couleur_foncee, $flag_gz, $options,$supp;

if ($connect_statut != '0minirezo' ) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_swfupload'), "naviguer", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('swfupload:titre_swfupload'));
echo "<br />";
echo debut_gauche();
debut_boite_info();
echo "Le plugin SWFupload permet de t&eacute;l&eacute;charger des fichiers dans votre dossier ".determine_upload()." m&ecirc;me si vous n'avez pas d'acc&egrave;s ftp.<br/><br/>Vous pourrez alors acc&egrave;der &agrave; ces fichiers lors de l'ajout de documents ou images &agrave; un article.";
fin_boite_info();
echo "<br/>";
debut_boite_info();
echo "<a href='?exec=swfupload_vider'>Vider le dossier</a> ".determine_upload();
echo "<br/><h4>Attention en cliquant sur ce lien vous supprimerez tous les fichiers et dossiers.</h4>";
fin_boite_info();

echo debut_droite();
echo gros_titre(_T('swfupload:titre_swfupload'));
echo swfupload_SWF_js($flux);
echo '
<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
		<div>'._T('swfupload:texte_swfupload').'</div>

		<div class="content">
			<fieldset class="flash" id="fsUploadProgress">
				<legend>'._T('swfupload:texte_uploadqueue').'</legend>
			</fieldset>
			<div id="divStatus">0 '._T('swfupload:texte_filesupload').'</div>
			<div>
				<input type="button" value="'._T('swfupload:texte_boutonupload').'" onclick="swfu.selectFiles()" style="font-size: 8pt;" />
				<input id="btnCancel" type="button" value="'._T('swfupload:texte_cancelupload').'" onclick="swfu.cancelQueue();" disabled="disabled" style="font-size: 8pt;" /><br />

			</div>
		</div>
	</form>';
echo fin_gauche();
echo fin_page();
}

function swfupload_SWF_js($flux) {
$session = session_id();

//$upload_dir = "../../".determine_upload();
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
<link href="'._DIR_PLUGIN_SWFUPLOAD.'css/default.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'swfupload/swfupload.js"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'js/swfupload.queue.js"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'js/fileprogress.js"></script>
			<script type="text/javascript" src="'._DIR_PLUGIN_SWFUPLOAD.'js/handlers.js"></script>
	<script type="text/javascript">
		var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Flash Settings '._DIR_PLUGIN_SWFUPLOAD.'
				flash_url : "'._DIR_PLUGIN_SWFUPLOAD.'swfupload/swfupload_f9.swf",	// Relative to this file
				// Backend Settings
				upload_url: "../upload.php",	//http://127.0.0.1/clg-clement/ecrire?action= Relative to the SWF file
				post_params: {"UPLOAD_DIR": "'.$upload_dir.'","PHPSESSID": "'.$session.'"},
				// File Upload Settings
				file_size_limit : "'.$file_size_limit.'",	// 2MB
				file_types : "'.$file_types.'",
				file_types_description : "Allowed Files",
				file_upload_limit : "'.$file_upload_limit.'",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete,

				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel",

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
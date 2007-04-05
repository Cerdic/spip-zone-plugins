<?php

// détermination du chemin de base par rapport à la racine du serveur
$dir_relatif_array = split('/', $_SERVER["PHP_SELF"]);
$i = 0;
while($dir_relatif_array[$i] != 'ecrire') {
	$dir_relatif .= $dir_relatif_array[$i];
	$i++;
}
if($dir_relatif != '') $dir_relatif = "/".$dir_relatif;
define('_DIR_PLUGIN_ABS_FCKEDITOR',$dir_relatif.'/plugins/fckeditor');

function fckeditor_header_prive($flux) {
	global $exec;

	$code='';

	if($exec=='articles_edit') {

		$langue = ($GLOBALS['_COOKIE']['spip_lang_ecrire'] != '') ? $GLOBALS['_COOKIE']['spip_lang_ecrire'] : 'fr';
		
		$code='
			
			$("#text_area").after("<div id=\"fckeditor_div\"><input id=\"_BtnSwitchTextarea\" type=\"button\" value=\"'._T("fckeditor:texte_editeur_standard").'\" onclick=\"Toggle()\" /><textarea id=\"fckeditor_data\" cols=\"40\" rows=\"20\">"+$("#text_area").val()+"</textarea></div>");
			$(".spip_barre").before("<input type=\"button\" value=\"'._T("fckeditor:texte_editeur_avance").'\" id=\"fckeditor_switch\" onclick=\"Toggle()\" />");
			$("#text_area").css("display", "none");
			$("#fckeditor_switch").css("display", "none");
			$(document.forms["formulaire"]).bind("submit", PrepareSave);
			$(".spip_barre").css("display", "none");

			var oFCKeditor = new FCKeditor( "fckeditor_data" , "100%", "600", "Spip") ;
			oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/fckeditor/" ;
			oFCKeditor.Config["CustomConfigurationsPath"] = "'._DIR_PLUGIN_ABS_FCKEDITOR.'/spip_fck/fckconfig.php?path='._DIR_PLUGIN_ABS_FCKEDITOR.'&" + ( new Date() * 1 ) ;
			oFCKeditor.Config[ "AutoDetectLanguage" ] = false ;
			oFCKeditor.Config[ "DefaultLanguage" ] = "'.$langue.'" ;
			oFCKeditor.ReplaceTextarea();
			
		';

	}

	if(!empty($code)) {
		$code='
			<script type="text/javascript" src="'._DIR_PLUGIN_FCKEDITOR.'fckeditor/fckeditor.js"></script>
			<script type="text/javascript" src="'._DIR_PLUGIN_FCKEDITOR.'spip_fck/switch.js"></script>
			<script type="text/javascript"><!--
			$(document).ready(function () {
			'.$code.'
			});
			//-->
			
			</script>';
		return $flux.$code;
	}

	return $flux;
}

?>

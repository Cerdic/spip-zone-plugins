<?php

// détermination du chemin de base par rapport à la racine du serveur
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\',/*'*/'/',realpath(dirname(__FILE__))));
define('_DIR_RELATIF_PLUGIN_FCKEDITOR',str_replace('../','',(_DIR_PLUGINS.end($p))));

$dir_relatif_array = split('/', $_SERVER["PHP_SELF"]);
$i = 0;
while($dir_relatif_array[$i] != 'ecrire') 
{
	$dir_relatif .= $dir_relatif_array[$i]."/";
	$i++;
}
define('_DIR_PLUGIN_ABS_FCKEDITOR',$dir_relatif._DIR_RELATIF_PLUGIN_FCKEDITOR);

function fckeditor_header_prive($flux) {
	
	global $exec;
	
	$code = '';
	$langue = ($GLOBALS['_COOKIE']['spip_lang_ecrire'] != '') ? $GLOBALS['_COOKIE']['spip_lang_ecrire'] : 'fr';
	
	if($exec=='articles_edit' || $exec=='breves_edit' || $exec=='rubriques_edit') {
		$code='
			$("textarea[@name=texte]").after("<div id=\"fckeditor_div\"><input id=\"_BtnSwitchTextarea\" type=\"button\" value=\"'._T("fckeditor:texte_editeur_standard").'\" onclick=\"Toggle()\" /><textarea id=\"fckeditor_data\" cols=\"40\" rows=\"20\">"+$("textarea[@name=texte]").val()+"</textarea></div>");
			$(".spip_barre").before("<input type=\"button\" value=\"'._T("fckeditor:texte_editeur_avance").'\" id=\"fckeditor_switch\" onclick=\"Toggle()\" />");
			$("textarea[@name=texte]").css("display", "none");
			$("#fckeditor_switch").css("display", "none");
			$(document.forms["formulaire"]).bind("submit", PrepareSave);
			$(".spip_barre").css("display", "none");
			var oFCKeditor = new FCKeditor( "fckeditor_data" , "100%", "600", "Spip") ;
			oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/fckeditor/" ;
			oFCKeditor.Config["CustomConfigurationsPath"] = "'._DIR_PLUGIN_ABS_FCKEDITOR.'/spip_fck/fckconfig.php?path='._DIR_PLUGIN_ABS_FCKEDITOR.'&" + ( new Date() * 1 ) ;
			oFCKeditor.Config[ "AutoDetectLanguage" ] = false ;
			oFCKeditor.Config[ "DefaultLanguage" ] = "'.$langue.'" ;
		';
	}

	switch ($exec) {
		case 'articles_edit':
			$code.='
	        		oFCKeditor.ToolbarSet = "BarreArticle";
			';
		break;
		case 'breves_edit':
			$code.='
	         		oFCKeditor.ToolbarSet = "BarreBreve";
			';
		break;
		case 'rubriques_edit':
			$code.='
	         		oFCKeditor.ToolbarSet = "BarreRubrique";
			';
		break;
	}
	
	$code .= '
		oFCKeditor.ReplaceTextarea()';
   
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
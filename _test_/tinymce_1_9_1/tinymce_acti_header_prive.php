<?php

//$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\',/*'*/'/',realpath(dirname(__FILE__))));
//define('_DIR_PLUGIN_FCKEDITOR',(_DIR_PLUGINS.end($p)));

// determination du chemin de base par rapport a  la racine du serveur
/*$dir_relatif_array = split('/', $_SERVER['PHP_SELF']);
$i = 0;
while($dir_relatif_array[$i] != 'ecrire') {
	$dir_relatif .= $dir_relatif_array[$i];
	$i++;
}
if($dir_relatif != '') $dir_relatif = '/'.$dir_relatif;
define('_DIR_PLUGIN_TINYMCE', $dir_relatif.'/plugins/tinymce');*/

function tinymce_acti_header_prive($flux) {
	global $exec;

	$code='';

	if($exec=='articles' || $exec=='breves_voir') {
		$code = '
			<link href="'._DIR_PLUGIN_TINYMCE.'/config/tiny_mce_style.css" rel="stylesheet" type="text/css">
		';

	} elseif($exec=='articles_edit') {

		$code_js='
			$(".spip_barre").remove(); //supprime la barre de raccourcis typo de Spip
			$("textarea[@name=\'texte\']").addClass("mceEditor"); //change la classe de la zone de texte pour que tinyMCE se charge dedans
		';

	} elseif($exec=='breves_edit') {

		$code_js='
			$(".spip_barre").remove(); //supprime la barre de raccourcis typo de Spip
			$("textarea[@name=\'texte\']").addClass("mceEditor"); //change la classe de la zone de texte pour que tinyMCE se charge dedans
		';
	}

	if(!empty($code_js)) {
		$code .= '
			<script type="text/javascript" src="'._DIR_PLUGIN_TINYMCE.'/tiny_mce/tiny_mce.js">/* fichier de tinymce */</script>
			<script type="text/javascript" src="'._DIR_PLUGIN_TINYMCE.'/jquery-1.0.js">/* fichier de librairie javascript cf. http://jquery.com/docs/ */</script>
			<script type="text/javascript"><!--
			$(document).ready(function () {
			'.$code_js.'
			});
			//-->
			</script>
			<script type="text/javascript" src="'._DIR_PLUGIN_TINYMCE.'/config/tiny_mce_config.js">/* fichier de configuration de tinymce (fonction init avec tous les params) */</script>
			<link href="'._DIR_PLUGIN_TINYMCE.'/config/tiny_mce_style.css" rel="stylesheet" type="text/css">
		';
	}

	return $flux.$code;
}

?>
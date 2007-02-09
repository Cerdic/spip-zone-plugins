<?php


function tinymce_acti_header_prive($flux) {
	global $exec;
	global ${_PLUGIN_TINYMCE_LANGUAGES_PACK_VARNAME};
	
	//v�rifie que les fichiers de TinyMCE sont pr�sents, sinon les installe
	if (!is_file(_DIR_TINYMCE_FILES.'/tiny_mce.js')) {
		include_spip("inc/distant");
		include_spip("inc/flock");
		include_spip("inc/pclzip");
		
		//r�cup�re l'archive de TinyMCE sur le web et la met dans le r�pertoire du plugin
		$zip_content = spip_file_get_contents(_PLUGIN_TINYMCE_ARCHIVE_URL);
		if (!ecrire_fichier(_DIR_RACINE.'/IMG/tinymce_archive.zip', $zip_content, true, false))
			return;
		//d�zippe l'archive r�cup�r�e		
		$zip = new PclZip(_DIR_RACINE.'/IMG/tinymce_archive.zip');
		$zip->extract(_DIR_PLUGIN_TINYMCE);
		supprimer_fichier(_DIR_RACINE.'/IMG/tinymce_archive.zip');
		
		//d�zippe les plugins filemanager et ibrowser
		$zip = new PclZip(_DIR_RACINE.'/IMG/filemanager.zip');
		$zip->extract(_DIR_TINYMCE_FILES.'/plugins/');
		$zip = new PclZip(_DIR_RACINE.'/IMG/ibrowser.zip');
		$zip->extract(_DIR_TINYMCE_FILES.'/plugins/');
		
		//t�l�charge les packages de langues TinyMCE n�cessaires sur le web (si on veut autre chose que l'Anglais)
		if (!empty(${_PLUGIN_TINYMCE_LANGUAGES_PACK_VARNAME})) {
			$zip_content = spip_file_get_contents(_PLUGIN_TINYMCE_LANGUAGES_URL.'?dlang[]='.implode('&dlang[]=',${_PLUGIN_TINYMCE_LANGUAGES_PACK_VARNAME}).'&format=zip&submit=Download', 'force');
			if (!ecrire_fichier(_DIR_RACINE.'/IMG/tinymce_languages_archive.zip', $zip_content))
				return;
			//d�zippe l'archive de langues r�cup�r�e		
			$zip = new PclZip(_DIR_RACINE.'/IMG/tinymce_languages_archive.zip');
			$zip->extract(_DIR_PLUGIN_TINYMCE.'');
			supprimer_fichier(_DIR_RACINE.'/IMG/tinymce_languages_archive.zip');
		}
	}
	//fin de v�rification que TinyMCE est bien install�
	
	//code de header_prive � proprement parler

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
			<script type="text/javascript" src="'._DIR_TINYMCE_FILES.'/tiny_mce.js">/* fichier de tinymce */</script>
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
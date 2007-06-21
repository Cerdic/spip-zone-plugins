<?php


function tinymce_acti_header_prive($flux) {
	global $exec;
	global ${_PLUGIN_TINYMCE_LANGUAGES_PACK_VARNAME};
	
	//v�rifie que les fichiers de TinyMCE sont pr�sents, sinon les installe
	if (!is_file(_DIR_TINYMCE_FILES.'/tiny_mce.js')) {
		include_spip("inc/distant");
		include_spip("inc/flock");
		include_spip("inc/pclzip");
		include_spip('inc/dirtool');
		
		//r�cup�re l'archive de TinyMCE sur le web et la met dans le r�pertoire du plugin
		$zip_content = spip_file_get_contents(_PLUGIN_TINYMCE_ARCHIVE_URL);
		if (!ecrire_fichier(_DIR_RACINE.'/IMG/tinymce_archive.zip', $zip_content, true, false))
			return;
		//d�zippe l'archive r�cup�r�e		
		$zip = new PclZip(_DIR_RACINE.'/IMG/tinymce_archive.zip');
		$zip->extract(_DIR_PLUGIN_TINYMCE);
		supprimer_fichier(_DIR_RACINE.'/IMG/tinymce_archive.zip');
		
		//d�zippe les plugins filemanager et ibrowser
		/*$zip = new PclZip(_DIR_PLUGIN_TINYMCE.'/files/filemanager.zip');
		$zip->extract(_DIR_TINYMCE_FILES.'/plugins');
		$zip = new PclZip(_DIR_PLUGIN_TINYMCE.'/files/ibrowser.zip');
		$zip->extract(_DIR_TINYMCE_FILES.'/plugins');*/
		$dir = new dirtool(_DIR_PLUGIN_TINYMCE.'/files/filemanager');
		$dir->copy(_DIR_TINYMCE_FILES.'/plugins/filemanager', 0775);
		$dir = new dirtool(_DIR_PLUGIN_TINYMCE.'/files/ibrowser');
		$dir->copy(_DIR_TINYMCE_FILES.'/plugins/ibrowser', 0775);
		
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

	if($exec=='articles' || $exec=='breves_voir' || $exec=='articles_edit' || $exec=='breves_edit') {
		$code = '
			<link href="'._DIR_PLUGIN_TINYMCE.'/config/tiny_mce_style.css" rel="stylesheet" type="text/css">
		';
	}

	return $flux.$code;
}


function tinymce_acti_affiche_droite($flux) {
	global $exec;

	$code='';

	if($exec=='articles_edit') {
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
		if ($GLOBALS['spip_version_code']<1.92) {
			$code .= '<script type="text/javascript" src="'._DIR_PLUGIN_TINYMCE.'/jquery-1.0.js"></script>';
		}
		$code .= '<script type="text/javascript" src="'._DIR_TINYMCE_FILES.'/tiny_mce.js"></script>
			<script type="text/javascript"><!--
			$(document).ready(function () {
			'.$code_js.'
			});
			//-->
			</script>
			<script type="text/javascript" src="'._DIR_PLUGIN_TINYMCE.'/config/tiny_mce_config.js"></script>
		';
	}

	return $flux['data'].$code;
}

?>
<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */
include_spip('prive/squelettes/contenu/skeleditor_fonctions');

/**
 * Afficher l'arborescence du dossier squelette
 *
 * @param string $path_base
 * @param string $current_file
 * @return string
 */
function skeleditor_afficher_dir_skel($path_base,$current_file) {
	include_spip('inc/skeleditor');
	include_spip('inc/presentation');
	$file_list = skeleditor_files_editables($path_base);
	$current_file = substr($current_file,strlen($path_base));

  $output = "<div id='arbo'><div class='dir'>\n";
	$init_dir = $current_dir = "";
	foreach($file_list as $file){
		$dir = substr(dirname($file),strlen($path_base));
		$file = substr($file,strlen($path_base));

		if ($dir != $current_dir)
			$output .= skeleditor_tree_open_close_dir($current_dir,$dir,$current_file);

		$class="fichier";
		$readonly = false;
		if (!is_writable($path_base.$dir) OR !is_writable($path_base.$file)) {
			$readonly = true;
			$class .= " readonly";
		}

		$class .= ($file==$current_file?" on":'');

		$icon = "file";
		if (preg_match(',('._SE_EXTENSIONS_IMG.')$,',$file))
			$icon = "image";

		include_spip('inc/filtres_images_mini');
		$cadenas = ($readonly) ? "&nbsp;".inserer_attribut(image_reduire(chemin_image('cadenas-16.png'),12),'title',attribut_html(_T('texte_inc_meta_2'))) : "";

		$output .= "<a href='".generer_url_ecrire('skeleditor','f='.urlencode($f=$path_base.$file))."' class='$class'
			onclick=\"jQuery('#contenu > :first').ajaxReload({history:true,args:{f:'$f'}});return false;\">"
			. "<img src='"._DIR_PLUGIN_SKELEDITOR."images/se-$icon-16.png' alt='$icon' /> "
			.basename($file)
			.$cadenas
			. "</a>"
			;
	}
	$output .= skeleditor_tree_open_close_dir($current_dir,$init_dir,$current_file);
  $output .= "</div></div>\n";
  return $output;
}

?>

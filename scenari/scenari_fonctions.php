<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	/* *
	 * remplace le motif par le lien vers le scenari
	 * si le lien n'est pas correct efface le motif 
	 */

	function scenari_insert_projet($texte){
		while (preg_match("/scenari@([0-9a-zA-Z]+)@/i", $texte, $projetscenari)) { 
			$motif="scenari@".$projetscenari['1']."@";
			$lien=find_in_path(_DIR_IMG."scenari/".$projetscenari['1']);
			if(!empty($lien))
			$texte = str_replace($motif, '<a href="'.find_in_path(_DIR_IMG."scenari/".$projetscenari['1']).'/" target="scenari"><img src="'._DIR_PLUGIN_SCENARI.'images/scenari-32.png" alt="'.$projetscenari['1'].'"></a>', $texte);
			else
			$texte = str_replace($motif, '', $texte); //On remplace dans le texte
		}
		return $texte;
	}

	/* *
	 * Liste des scenari disponibles
	 */

	function liste_scenari($dir){
		$return=array();
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if (is_dir($dir.$file)&&$file!='.'&&$file!='..') {
					$return[] = $file;
				}
			}
			closedir($handle);
		}
		return $return;
	}

	/* *
	 * Another simple way to recursively delete a directory that is not empty
	 */

	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	} 

?>

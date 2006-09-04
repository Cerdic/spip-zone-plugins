<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ICONES_ADMIN',(_DIR_PLUGINS.end($p)));
	
// Fonction qui bidouille le fichier mes_options.php pour qu'il definisse
// le bon chemin d'img_pack. C'est pour l'instant le foutoir et ca ne
// marche pas.
function realistik_exec_init($ecriture) {
	$mes_options_file = "mes_options.php";
	$opening = fopen($mes_options_file, 'a+');
	$file_size = filesize ($mes_options_file);
	$searched_content = _DIR_PLUGIN_ICONES_ADMIN;
	$written_content = "define('_DIR_IMG_PACK', ('$searched_content/img_pack/'));";
	
	if ($mes_options_file AND $file_size != 0) {
		$read_file = fread ($opening, $file_size);
		$search_content = ereg ($searched_content, $read_file);
		
		if ($searched_content == FALSE) {
			$content_file = $written_content;
		}
		$write = fwrite($opening, $content_file);
	}
	else {
		$content_file = "<?php\n $written_content \n?>";
		$write = fwrite($opening, $content_file);
	}
	return $write;
}
	

function realistik_header_prive($flux){

	global $exec;
	
	$flux .= '<link rel="stylesheet" href="style.css" />'."\n";
	
	return $flux;
}
	
?>
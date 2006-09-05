<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ICONES_ADMIN',(_DIR_PLUGINS.end($p)));
	
// Fonction qui bidouille le fichier mes_options.php pour qu'il definisse
// le bon chemin d'img_pack. Devrait avoir moyen de simplifier encore tout ca.
function realistik_exec_init($write) {
	$mes_options_file = "mes_options.php";
	$opening = fopen($mes_options_file, 'a+');
	$file_size = filesize ($mes_options_file);
	$searched_content = _DIR_PLUGIN_ICONES_ADMIN;
	$written_content = "define('_DIR_IMG_PACK', ('$searched_content/img_pack/'));";
	
	if ($mes_options_file AND $file_size != 0) {
		$read_file = fread ($opening, $file_size);
		$search_content = ereg ($searched_content, $read_file);
		
		if ($search_content == FALSE) {
			fclose($opening);
			$old_file = "$mes_options_file.backup";
			rename($mes_options_file, $old_file);
			$opening_old_file = fopen($old_file, 'r');
			$old_file_size = filesize ($old_file);
			$read_old_file = fread ($opening_old_file, $old_file_size);
			$new_content = "$written_content \n?>";
			$insert_new_content = ereg_replace('\?>', $new_content, $read_old_file);
			$new_file = $mes_options_file;
			$opening_new_file = fopen($new_file, 'a+');
			$write = fwrite($opening_new_file, $insert_new_content);
			fclose($opening_new_file);
		}
	}
	else {
		$content_file = "<?php\n $written_content \n?>";
		$write = fwrite($opening, $content_file);
		fclose($opening);
	}
	return $write;
}
	

function realistik_header_prive($flux){

	global $exec;
	
	$flux .= '<link rel="stylesheet" href="../'._DIR_PLUGIN_ICONES_ADMIN.'/img_pack/style.css" />'."\n";
	
	return $flux;
}
	
?>
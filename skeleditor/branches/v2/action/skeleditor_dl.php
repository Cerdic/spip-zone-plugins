<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

function action_skeleditor_dl_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$file_name = $securiser_action();

	if (autoriser('download','squelette',$file_name)){
		if (lire_fichier($file_name, $contenu)) {
			$file_name_nopath = basename($file_name);
			//header("Content-type: text/plain"); // text/plain or binary ....
			header("Content-Disposition: attachment; filename=\"$file_name_nopath\"");
			echo $contenu;
			exit;
		}
	}
}

?>
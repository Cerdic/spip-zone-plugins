<?php
/**
 *
 */

function action_langonet_telecharger_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$file_name = $securiser_action();

	if (lire_fichier($file_name, $contenu)) {
		$file_name_nopath = basename($file_name);
		//header("Content-type: text/plain"); // text/plain or binary ....
		header("Content-Length: ".filesize($arg));
		header("Content-Disposition: attachment; filename=\"$file_name_nopath\"");
		echo $contenu;
		exit;
	}
}

?>
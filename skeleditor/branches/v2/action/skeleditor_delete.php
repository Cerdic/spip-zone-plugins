<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */


function action_skeleditor_delete_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$file_name = $securiser_action();

	if (autoriser('supprimer','squelette',$file_name)){
		spip_unlink($file_name);
	}
}

?>
<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

function action_skeleditor_ul_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	$target = rtrim(_request('target'),'/');
	$file_name = _request('filename');
	$file_name =  $target."/".$file_name;
	if (autoriser('creer','squelette',$file_name)){
		// FIXME: check if allowed extension ?
		if (is_file($file_name)) {  // security : ovewrite ?
			$log = "<span style='color:red'>"._T("skeleditor:erreur_overwrite")."</span>";
		} else {
			if (!$handle = fopen($file_name, 'w')) {
				$log = "<span style='color:red'>"._T("skeleditor:erreur_droits")."</span>";
			} else if (fwrite($handle, "...") === FALSE) {
				$log = "<span style='color:red'>"._T("skeleditor:erreur_droits")."</span>";
			} else {
				$log = "<span style='color:green'>"._T("skeleditor:fichier_sauvegarde_date").date('H:i')."</span>";
				fclose($handle);
			}
		}
	}
}

?>
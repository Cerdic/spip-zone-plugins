<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function action_editer_squelette_dist($file_name = null){
	if (is_null($file_name)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$file_name = $securiser_action();
	}

	$redirect = _request('redirect');
	$erreur = "";
	if (autoriser('modifier','squelette',$file_name)){
	}
}

function squelette_set($file_name, $c){
	$log = "";
	if (!is_null($editor = _request('editor',$c))) {
		$editor = str_replace("&lt;/textarea","</textarea",$editor); // exception: textarea closing tag
		if (is_writable($file_name) && check_file_allowed($file_name,$files_editable)) {
			if (!$handle = fopen($file_name, 'w')) {
				$log = "<span style='color:red'>"._T("skeleditor:erreur_ouverture_fichier")."</span>";
			} else if (fwrite($handle, $editor) === FALSE) {
				$log = "<span style='color:red'>"._T("skeleditor:erreur_ecriture_fichier")."</span>";
			} else {
				$log = "<span style='color:green'>"._T("skeleditor:fichier_sauvegarde_date").date('H:i')."</span>";
			fclose($handle);
			}
		}
		else {
			$log = "<span style='color:red'>"._T("skeleditor:erreur_edition_ecriture")."</span>";
		}
	}
}

?>
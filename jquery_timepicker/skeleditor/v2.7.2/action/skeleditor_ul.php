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
	$file_name = $securiser_action();

	if (autoriser('upload','squelette',$file_name)){
		// FILES request ?
		if (isset($_FILES['upf'])) {    // upload file ?
				$tmp_name = $_FILES['upf']['tmp_name'];
				if (isset($_POST['target'])) {
					 $target = ($_POST['target'])."/".$_FILES['upf']['name'];    // security
					 if (check_file_allowed($target,$files_editable,true)) {     // security
								 $_GET['f'] = $target;
								 $_GET['operation'] = 'preview';
								 if (file_exists($target)) {
										$log = "<span style='color:red'>"._T('skeleditor:erreur_overwrite')."</span>";
								 } else {
										$ok = @copy($tmp_name, $target);
										if (!$ok) $ok = @move_uploaded_file($tmp_name, $target);
										if (!$ok) $log = "<span style='color:red'>"._T('skeleditor:erreur_droits')."</span>";
												 else $log = "<span style='color:green'>"._T('skeleditor:fichier_upload_ok')."</span>";
								 }
					 } else {
								 $log = "<span style='color:red'>"._T('skeleditor:erreur_sansgene')."</span>";
					 }


				}
		}
	}
}

?>
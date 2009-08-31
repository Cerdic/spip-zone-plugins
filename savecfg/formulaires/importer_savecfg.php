<?php
function formulaires_importer_savecfg_charger_dist() {
	$valeurs = array(
		'fichier'=>$fichier,
	);
	return $valeurs;
}
function formulaires_importer_savecfg_verifier_dist(){
	$erreurs = array();
	if (strtolower(substr(strrchr($_FILES['fichier']['name'], '.'),1)) != 'cfg')
		$erreurs['message_erreur'] == 'mauvaise extension';
	$file = explode(':!;!:', file_get_contents($_FILES['fichier']['tmp_name']));
	foreach($file as $save=>$value) {
		$content = explode('!:;:!', $value);
	if ((!is_array(unserialize($content[2]))) OR (count($content) < 4))
		$erreurs['message_erreur'] = 'mauvais';
	}
	return $erreurs;
}
function formulaires_importer_savecfg_traiter_dist() {
	$message = importer_savecfg('fichier');
	return $message;
}
function importer_savecfg($fichier) {
	$file = explode(':!;!:', file_get_contents($_FILES[$fichier]['tmp_name']));
	foreach($file as $save=>$value) {
		$content = explode('!:;:!', $value);
		sql_insertq(
			'spip_savecfg', 
			array(
				'id_savecfg' => $content[0],
				'fond' => $content[1],
				'valeur' => $content[2],
				'titre' => $content[3],
				'date' => date('Y-m-d H:m:s')
			)
		);
	}
	return _T('savecfg:import_ok');
}
?>
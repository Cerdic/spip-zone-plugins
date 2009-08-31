<?php
function formulaires_exporter_savecfg_charger_dist() {
	$valeurs = array(
		'fichier'=>$fichier,
	);
	return $valeurs;
}
function formulaires_exporter_savecfg_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}
function formulaires_exporter_savecfg_traiter_dist() {
	$message = exporter_savecfg();
	return $message;
}
function exporter_savecfg() {
	$fichier = '';
	$i = 0;
	foreach (_request('export') as $key=>$value) {
		if ($value == 'on') {
		 $sfg = sql_fetsel(array('fond', 'valeur', 'titre'), 'spip_savecfg', 'id_savecfg='.sql_quote($key));
		if ($i > 0)
			$fichier .= ':!;!:';
		$fichier .= '!:;:!';
		$fichier .= $sfg['fond'];
		$fichier .= '!:;:!';
		$fichier .= $sfg['valeur'];
		$fichier .= '!:;:!';
		$fichier .= $sfg['titre'];
		$i++;
		}
	}
	header("Content-type: application/cfg");
	header("Content-disposition: attachment; filename=SaveCFG_" . date("Ymd").".cfg");
	echo ($fichier);
	exit;
	return true;
}
?>
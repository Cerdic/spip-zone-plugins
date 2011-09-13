<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// *********
// Config 
// *********


$nl = "\n";
$br = '<br />';
$hr = '<hr />';

// *********
// Fichier des selections
// *********

$fichier_selection = trouver_fichier_prefixe(SITRA_DIR,'('.SITRA_ID_SITE.')_Selections_');
	
if (!$fichier_selection) {
	message($nl.'Pas de fichier Selections','erreur');
	continue;
}

$fichier_selection = SITRA_DIR.$fichier_selection;

message($nl.'/// Fichier selections : '.$fichier_selection.' ///');
$xml = simplexml_load_file($fichier_selection);

// analyse
$selections = array();
$i = 0;
foreach ($xml -> Selection as $selection){
	$id_selection = $selection['code'];
	$nom_selection = normalise_nom($selection['nom']);
	message('Selection : '.$selection['nom'].' == '.$nom_selection);
	foreach($selection -> identifier as $val){
		$selections[$i]['id_sitra'] = $val;
		$selections[$i]['id_selection'] = $id_selection;
		$selections[$i]['selection'] = $nom_selection;
		$i++;
	}
}

if (SITRA_DEBUG) {
	echo $hr;
	sitra_debug('selections',$selections);
}

if (count($selections)){
		sql_delete('spip_sitra_selections');
		sql_insertq_multi('spip_sitra_selections',$selections);
	} else {
		message('Aucune selection', 'erreur');
	}

// si pas en mode debug on supprime le fichier importé
if (!SITRA_DEBUG) {
	unlink($fichier_selection);
	message('Suppression fichier '.$fichier_selection);
}

message('/// Fin traitement fichier Selections ///');


?>
<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_modal_images_dist() {
	include_spip('inc/documents');
	
	// Appel Tableau
	// groupe code et bouton <code>

	// Qui ?
	if(_request("id_objet")) {
		$id_objet = _request("id_objet");
		$objet = _request("objet");
	}

	$contexte = array("objet" => $objet,"id_".$objet => $id_objet,"id_objet" => $id_objet);
	
	$temp = recuperer_fond("images_associees_".$objet, $contexte);
	
	echo ($temp);
}
?>
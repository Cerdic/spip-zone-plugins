<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_modale_images_select_dist() {
	// Qui ?
	if(_request("id_objet")) {
		$id_objet = _request("id_objet");
		$objet = _request("objet");
	}

	$contexte = array("objet" => $objet,"id_".$objet => $id_objet,"id_objet" => $id_objet);
	
	$temp = recuperer_fond("modale_images_select", $contexte);

	echo ($temp);
}
?>
<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_modale_liste_images_dist() {
	$id_objet = _request("id_objet");
	$objet = _request("objet");
	$url = _request("url");

	$contexte = array("objet" => $objet,"id_".$objet => $id_objet,"id_objet" => $id_objet);
	
	$temp = recuperer_fond($url, $contexte, array("ajax"=>true));
	
	echo ($temp);
}
?>
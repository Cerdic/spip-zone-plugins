<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_modale_images_parametres_dist() {
	include_spip('inc/documents');

	// Qui ?
	if(_request("image_sel")) {
		$id_document = _request("image_sel");
	}

	$contexte = array("id_document" => $id_document);
	
	$temp = recuperer_fond("edit_parametres_images", $contexte);

	if(!$temp)
	{
		$erreur = recuperer_fond("modale_images_select", array("objet" => _request("objet"),"id_"._request("objet") => _request("id_objet"),"id_objet" => _request("id_objet")));
		echo("<div class=\"inserer_modeles_erreur\">Pas d'image sélectionnée : soit l'image n'est pas correcte, soit il y a eu un problème pendant le dialogue avec le serveur.</div>".$erreur);
	}

	echo ($temp);
}
?>
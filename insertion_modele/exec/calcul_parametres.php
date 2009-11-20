<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_calcul_parametres_dist() {
	$id_document = _request("id_document");
	(int)$hauteur=_request("hauteur");
	(int)$largeur=_request("largeur");
	$dzoom=_request("dzoom");
	$titre_descri=_request("titre_descri");
	$image_align=_request("image_align");

	if(!$id_document)
		return '';

	$return = "<image".$id_document;

	if(!$image_align)
		$image_align = "centre";

	if($image_align)
		$return.="|align=".$image_align;

	if($dzoom)
		$return.="|dzoom=1";

	if($titre_descri)
		$return.="|titre_descri=1";
		
	if($largeur)
		$return.="|largeur=".$largeur;

	if($hauteur)
		$return.="|hauteur=".$hauteur;

	echo $return.">";
}
?>
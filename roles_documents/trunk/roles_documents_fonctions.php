<?php

function document_vu($id_objet, $objet, $id_document) {
	include_spip('action/editer_liens');
	$objets_lies=array($objet=>$id_objet);
	$objets_source=array('document'=>$id_document);
	$qualif = array('vu'=>'oui');
	objet_associer($objets_source, $objets_lies, $qualif);
}

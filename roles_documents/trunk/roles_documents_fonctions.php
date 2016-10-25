<?php
/**
 * Plugin Rôles de documents
 * (c) 2015
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function document_vu($id_objet, $objet, $id_document) {
	include_spip('action/editer_liens');
	$objets_lies=array($objet=>$id_objet);
	$objets_source=array('document'=>$id_document);
	$qualif = array('vu'=>'oui');
	objet_associer($objets_source, $objets_lies, $qualif);
}

<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/autoriser');

function formulaires_tagger_charger_dist($objet, $id_objet, $type_mot="tags"){
	$valeurs = array(
		'tags' => array(),
		'_objet' => $objet,
		'_id_objet' => $id_objet,
		'_type_mot' => $type_mot,
		'_supprimable' => autoriser('supprimertags',$objet,$id_objet),
		'_ajoutable' => autoriser('ajoutertags',$objet,$id_objet),
	);

	$valeurs['editable'] = (($valeurs['_supprimable'] OR $valeurs['_ajoutable'])?true:false);

	return $valeurs;
}

function formulaires_tagger_verifier_dist($objet, $id_objet, $type_mot="tags"){
}

function formulaires_tagger_traiter_dist($objet, $id_objet, $type_mot="tags"){

	$tags = _request('tags');
	include_spip('inc/tags');
	$tags = tags_decouper_tags($tags);
	// reinjecter tel quel
	set_request('tags',$tags);

	$t = tags_tagger(
		$objet,
		$id_objet,
		$tags,
		$type_mot,
		autoriser('ajoutertags',$objet,$id_objet),
		autoriser('supprimertags',$objet,$id_objet)
	);

	return array('message_ok'=>_T('etiquettes:tag_ajoutes'));
}

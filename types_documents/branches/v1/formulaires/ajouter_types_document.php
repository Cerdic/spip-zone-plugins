<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("base/abstract_sql");

function formulaires_ajouter_types_document_charger_dist(){

	$valeurs['extension']	= (_request('extension')) ? _request('extension') : 'ext';
	$valeurs['titre'] 		= (_request('titre')) ? _request('titre') : 'Sans titre';
	$valeurs['descriptif'] 	= (_request('descriptif')) ? _request('descriptif') : '';
	$valeurs['mime_type'] 	= (_request('mime_type')) ? _request('mime_type') : '';
	$valeurs['inclus'] 		= (_request('inclus')) ? _request('inclus') : 'non';
	$valeurs['upload'] 		= (_request('upload')) ? _request('upload') : 'oui';
	$valeurs['media'] 		= (_request('upload')) ? _request('upload') : 'file';
	$valeurs['interdit'] 	= (_request('interdit')) ? _request('interdit') : 'non';

	return $valeurs;
}

function formulaires_ajouter_types_document_verifier_dist(){
	$erreurs = array();

	// On stocke les données saisies :
	$extension	= (_request('extension')) ? _request('extension') : 'ext';
	$titre 		= (_request('titre')) ? _request('titre') : 'Sans titre';
	$descriptif = (_request('descriptif')) ? _request('descriptif') : '';
	$mime_type 	= (_request('mime_type')) ? _request('mime_type') : '';
	$inclus 	= (_request('inclus')) ? _request('inclus') : 'non';
	$upload 	= (_request('upload')) ? _request('upload') : 'oui';
	$media 		= (_request('upload')) ? _request('upload') : 'file';
	$interdit 	= (_request('interdit')) ? _request('interdit') : 'non';

	// On verifie que les champs obligatoires sont bien la :
	foreach(array('titre','extension','mime_type','inclus','upload') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('info_obligatoire');

	// On verifie que les champs obligatoires ne sont pas vide (espace(s) uniquement) :
	$reg = '/^\s+$/';
	foreach(array('titre','extension','mime_type','inclus','upload') as $nonvide)
		if(preg_match( $reg, _request($nonvide))) $erreurs[$nonvide] = _T('info_obligatoire');

	if ($rows = sql_allfetsel('*','spip_types_documents', 'extension=' .sql_quote(_request('extension'))) 
		AND count($rows) > 0) {
		if (isset($erreurs['extension'])) {
			$erreurs['extension'] .= '<br/>Cette extension existe déjà.';
		} else {
			$erreurs['extension'] = 'Cette extension existe déjà.';
		}
	}

	return $erreurs;
}

function formulaires_ajouter_types_document_traiter_dist(){
	$res = array();

	$champs['extension']	= _request('extension') ;
	$champs['titre']		= _request('titre') ;
	$champs['descriptif']	= _request('descriptif') ;
	$champs['mime_type']	= _request('mime_type') ;
	$champs['inclus']		= _request('inclus') ;
	$champs['upload']		= _request('upload') ;
	$champs['media']		= _request('media') ;
	$champs['interdit']		= _request('interdit') ;

	// Comme la clé primaire n'est pas un interger, ça nous renverra un 'true'
	sql_insertq('spip_types_documents',array(
		'extension' => $champs['extension'],
		'titre' => $champs['titre'],
		'descriptif' => $champs['descriptif'],
		'mime_type' => $champs['mime_type'],
		'inclus' => $champs['inclus'],
		'upload' => $champs['upload'],
		'media' => $champs['media'],
		'interdit' => $champs['interdit']
	));
	$types_document = sql_fetsel('extension','spip_types_documents', 'extension=' . sql_quote($champs['extension']));
	$url = generer_url_ecrire('types_document',"extension=" . $types_document['extension']);

	if (isset($types_document['extension'])) {
		$res['message_ok'] = _T('enregistrement_ok');
		$res['redirect'] = $url;
	} else {
		$res['message_erreur'] = _T('types_documents:enregistrement_ko');
	}
	return $res;
}

?>
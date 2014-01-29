<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("base/abstract_sql");

function formulaires_editer_types_document_charger_dist($extension){

	if(!isset($extension)) {
		$valeurs = array();
		$valeurs['extension']	= $extension;
		$valeurs['titre'] 		= 'Sans titre';
		$valeurs['descriptif'] 	= '';
		$valeurs['mime_type'] 	= '';
		$valeurs['inclus'] 		= 'non';
		$valeurs['upload'] 		= 'oui';
		$valeurs['media'] 		= 'file';
		$valeurs['interdit'] 		= 'non';
	} else {
		$row = sql_fetsel('*','spip_types_documents', "extension=" . sql_quote($extension));
		$valeurs['extension']		= (_request('extension')) ? _request('extension') : $row['extension'];
		$valeurs['titre']			= (_request('titre')) ? _request('titre') : $row['titre'];
		$valeurs['descriptif']		= (_request('descriptif')) ? _request('descriptif') : $row['descriptif'];
		$valeurs['mime_type']		= (_request('mime_type')) ? _request('mime_type') : $row['mime_type'];
		$valeurs['inclus']			= (_request('inclus')) ? _request('inclus') : $row['inclus'];
		$valeurs['upload']			= (_request('upload')) ? _request('upload') : $row['upload'];
		$valeurs['media']			= (_request('media')) ? _request('media') : $row['media'];
		$valeurs['interdit']		= (_request('interdit')) ? _request('interdit') : $row['interdit'];
		$valeurs['_hidden'] = "<input type='hidden' name='extension_origine' value='$extension' />";
	}
	
	return $valeurs;
}

function formulaires_editer_types_document_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_types_document_traiter_dist(){
	$res = array();

	$extension_origine		= _request('extension_origine') ;

	$champs['extension']	= _request('extension') ;
	$champs['titre']		= _request('titre') ;
	$champs['descriptif']	= _request('descriptif') ;
	$champs['mime_type']	= _request('mime_type') ;
	$champs['inclus']		= _request('inclus') ;
	$champs['upload']		= _request('upload') ;
	$champs['media']		= _request('media') ;
	$champs['interdit']		= _request('interdit') ;

	$update = sql_updateq('spip_types_documents',$champs,"extension='$extension_origine'");

	$url = generer_url_ecrire('types_document',"extension=" . $champs['extension']);

	if ($update) {
		$res['message_ok'] = _T('enregistrement_ok');
		$res['redirect'] = $url;
	} else {
		$res['message_erreur'] = _T('types_documents:enregistrement_ko');
	}
	return $res;
}

?>
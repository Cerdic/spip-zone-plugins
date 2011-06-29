<?php
function formulaires_configurer_document_fulltext_charger_dist(){
	//Recuperation de la configuration
	$fulltext = sql_fetsel('valeur', 'spip_meta', 'nom = "fulltext"');
	$fulltext = unserialize($fulltext['valeur']);
	//Valeurs prealablement saisie ou par defaut/d'exemple 
	$valeur = array(
		'taille_index' => $fulltext['taille_index'] ? $fulltext['taille_index'] : 50000,
		
		'pdf_index' => $fulltext['pdf_index'] ? $fulltext['pdf_index'] : 'off',
		'pdf_bin' => $fulltext['pdf_bin'] ? $fulltext['pdf_bin'] : '/usr/bin/pdftotext',
		'pdf_opt' => $fulltext['pdf_opt'] ? $fulltext['pdf_opt'] : '-enc Latin1',
		
		'odt_index' => $fulltext['odt_index'] ? $fulltext['odt_index'] : 'off',
		
		'doc_index' => $fulltext['doc_index'] ? $fulltext['doc_index'] : 'off',
		'doc_bin' => $fulltext['doc_bin'] ? $fulltext['doc_bin'] : '/usr/bin/catdoc',
		'doc_opt' => $fulltext['doc_opt'] ? $fulltext['doc_opt'] : '-s cp1252 -d 8859-1',
		
		'docx_index' => $fulltext['docx_index'] ? $fulltext['docx_index'] : 'off',
		
		'ppt_index' => $fulltext['ppt_index'] ? $fulltext['ppt_index'] : 'off',
		'ppt_bin' => $fulltext['ppt_bin'] ? $fulltext['ppt_bin'] : '/usr/bin/catppt',
		'ppt_opt' => $fulltext['ppt_opt'] ? $fulltext['ppt_opt'] : '',
		
		'pptx_index' => $fulltext['pptx_index'] ? $fulltext['pptx_index'] : 'off',
		
		'xls_index' => $fulltext['xls_index'] ? $fulltext['xls_index'] : 'off',
		'xls_bin' => $fulltext['xls_bin'] ? $fulltext['xls_bin'] : '/usr/bin/xls2csvt',
		'xls_opt' => $fulltext['xls_opt'] ? $fulltext['xls_opt'] : '-s cp1252 -d 8859-1',
		
		'xlsx_index' => $fulltext['xlsx_index'] ? $fulltext['xlsx_index'] : 'off',
	);

	return $valeur;
}
function formulaires_configurer_document_fulltext_verifier_dist(){
	$erreurs = array();
	//Il faut au moins indexer un caractere
	if((!_request('taille_index'))||(_request('taille_index') < 1)){
		$erreurs['taille_index'] = _T('fulltext:erreur_taille_index');
	}
	//Si on a choisit d'indexer un type de document on doit renseigner le binaire correspondant
	if(_request('pdf_index') == 'on'){
		if(!_request('pdf_bin')){
			$erreurs['pdf_bin'] = _T('fulltext:erreur_pdf_bin');
		}
	}
	if(_request('doc_index') == 'on'){
		if(!_request('doc_bin')){
			$erreurs['doc_bin'] = _T('fulltext:erreur_doc_bin');
		}
	}
	if(_request('ppt_index') == 'on'){
		if(!_request('ppt_bin')){
			$erreurs['ppt_bin'] = _T('fulltext:erreur_ppt_bin');
		}
	}
	if(_request('xls_index') == 'on'){
		if(!_request('xls_bin')){
			$erreurs['xls_bin'] = _T('fulltext:erreur_xls_bin');
		}
	}
	
	//TODO : verifier si on a bien une version PHP superieur a 5.2 avec option Zip si on indexe les odt,docx,xlsx,pptx
	
	return $erreurs;
}

function formulaires_configurer_document_fulltext_traiter_dist(){
	//Recuperation de la configuration et serialization
	$fulltext = serialize(array(
		'taille_index' => _request('taille_index'),
		'pdf_index' => _request('pdf_index'),
		'pdf_bin' => _request('pdf_bin'),
		'pdf_opt' => _request('pdf_opt'),
		
		'odt_index' => _request('odt_index'),
		
		'doc_index' => _request('doc_index'),
		'doc_bin' => _request('doc_bin'),
		'doc_opt' => _request('doc_opt'),
		
		'docx_index' => _request('docx_index'),
		
		'ppt_index' => _request('ppt_index'),
		'ppt_bin' => _request('ppt_bin'),
		'ppt_opt' => _request('ppt_opt'),
		
		'pptx_index' => _request('pptx_index'),
		
		'xls_index' => _request('xls_index'),
		'xls_bin' => _request('xls_bin'),
		'xls_opt' => _request('xls_opt'),
		
		'xlsx_index' => _request('xlsx_index'),	
	));
	//Insere ou update ?
	if($fulltext_meta = sql_fetsel('valeur', 'spip_meta', 'nom = "fulltext"')){
		//On update
		sql_updateq('spip_meta', array('valeur' => $fulltext, 'impt' => 'oui'), 'nom="fulltext"');
		$res = array('message_ok'=> _T('fulltext:message_ok_update_configuration'));
	}else{
		//On insere
		$id = sql_insertq('spip_meta', array('nom'=>'fulltext','valeur' => $fulltext, 'impt' => 'oui'));
		$res = array('message_ok'=>_T('fulltext:message_ok_configuration'));
	}
	return $res;
	
}
?>
<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_document_fulltext_charger_dist(){
	//Recuperation de la configuration
	$fulltext = @unserialize($GLOBALS['meta']['fulltext']);
	if(!is_array($fulltext)){
		$fulltext = array();
	}
	//Valeurs prealablement saisie ou par defaut/d'exemple 
	$valeur = array(
		'intervalle_cron' =>  $fulltext['intervalle_cron'] ? $fulltext['intervalle_cron'] : 600,
		'nb_docs' =>  $fulltext['nb_docs'] ? $fulltext['nb_docs'] : 5,
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
		'xls_bin' => $fulltext['xls_bin'] ? $fulltext['xls_bin'] : '/usr/bin/xls2csv',
		'xls_opt' => $fulltext['xls_opt'] ? $fulltext['xls_opt'] : '-s cp1252 -d 8859-1',
		
		'xlsx_index' => $fulltext['xlsx_index'] ? $fulltext['xlsx_index'] : 'off',
	);
	if(defined('_FULLTEXT_DOC_EXE')){
		$valeur['doc_bin'] = _FULLTEXT_DOC_EXE;
		$valeur['doc_bin_readonly'] = true;
	}
	if(defined('_FULLTEXT_DOC_CMD_OPTIONS')){
		$valeur['doc_opt'] = _FULLTEXT_DOC_CMD_OPTIONS;
		$valeur['doc_opt_readonly'] = true;
	}
	if(defined('_FULLTEXT_PDF_EXE')){
		$valeur['pdf_bin'] = _FULLTEXT_PDF_EXE;
		$valeur['pdf_bin_readonly'] = true;	
	}
	if(defined('_FULLTEXT_PDF_CMD_OPTIONS')){
		$valeur['pdf_opt'] = _FULLTEXT_PDF_CMD_OPTIONS;
		$valeur['pdf_opt_readonly'] = true;
	}
	if(defined('_FULLTEXT_PPT_EXE')){
		$valeur['ppt_bin'] = _FULLTEXT_PPT_EXE;
		$valeur['ppt_bin_readonly'] = true;	
	}
	if(defined('_FULLTEXT_PPT_CMD_OPTIONS')){
		$valeur['ppt_opt'] = _FULLTEXT_PPT_CMD_OPTIONS;
		$valeur['ppt_opt_readonly'] = true;
	}
	if(defined('_FULLTEXT_XLS_EXE')){
		$valeur['xls_bin'] = _FULLTEXT_XLS_EXE;
		$valeur['xls_bin_readonly'] = true;	
	}
	if(defined('_FULLTEXT_XLS_CMD_OPTIONS')){
		$valeur['xls_opt'] = _FULLTEXT_XLS_CMD_OPTIONS;
		$valeur['xls_opt_readonly'] = true;	
	}

	return $valeur;
}
function formulaires_configurer_document_fulltext_verifier_dist(){
	$erreurs = array();
	//Il faut au moins une seconde
	if((!_request('intervalle_cron'))||(_request('intervalle_cron') < 1)){
		$erreurs['intervalle_cron'] = _T('fulltext:erreur_intervalle_cron');
	}
	//Il faut au moins une documents a la fois
	if((!_request('nb_docs'))||(_request('nb_docs') < 1)){
		$erreurs['nb_docs'] = _T('fulltext:erreur_nb_docs');
	}	
	//Il faut au moins indexer un caractere
	if((!_request('taille_index'))||(_request('taille_index') < 1)){
		$erreurs['taille_index'] = _T('fulltext:erreur_taille_index');
	}
	//Si on a choisit d'indexer un type de document on doit renseigner le binaire correspondant
	if(_request('pdf_index') == 'on' && !defined('_FULLTEXT_PDF_EXE')){
		if(!_request('pdf_bin')){
			$erreurs['pdf_bin'] = _T('fulltext:erreur_pdf_bin');
		}else{
			@exec(_request('pdf_bin'),$retour_pdfbin,$retour_pdfbin_int);
			if($retour_pdfbin_int != 0){
				$erreurs['pdf_bin'] = _T('fulltext:erreur_binaire_indisponible');
			}
		}
	}
	
	if(_request('doc_index') == 'on' && !defined('_FULLTEXT_DOC_EXE')){
		if(!_request('doc_bin')){
			$erreurs['doc_bin'] = _T('fulltext:erreur_doc_bin');
		}else{
			@exec(_request('doc_bin'),$retour_doc_bin,$retour_doc_bin_int);
			if($retour_doc_bin_int != 0){
				$erreurs['doc_bin'] = _T('fulltext:erreur_binaire_indisponible');
			}
		}
	}
	
	if(_request('ppt_index') == 'on' && !defined('_FULLTEXT_PPT_EXE')){
		if(!_request('ppt_bin')){
			$erreurs['ppt_bin'] = _T('fulltext:erreur_ppt_bin');
		}else{
			@exec(_request('ppt_bin'),$retour_ppt_bin,$retour_ppt_bin_int);
			if($retour_ppt_bin_int != 0){
				$erreurs['ppt_bin'] = _T('fulltext:erreur_binaire_indisponible');
			}
		}
	}
	
	if(_request('xls_index') == 'on' && !defined('_FULLTEXT_XLS_EXE')){
		if(!_request('xls_bin')){
			$erreurs['xls_bin'] = _T('fulltext:erreur_xls_bin');
		}else{
			@exec(_request('xls_bin'),$retour_xls_bin,$retour_xls_bin_int);
			if($retour_xls_bin_int != 0 && $retour_xls_bin_int != 139){
				$erreurs['xls_bin'] = _T('fulltext:erreur_binaire_indisponible');
			}
		}
	}
	
	//TODO : verifier si on a bien une version PHP superieur a 5.2 avec option Zip si on indexe les odt,docx,xlsx,pptx
	
	if(count($erreurs) > 0){
		$erreurs['message_erreur'] = _T('fulltext:erreur_verifier_configuration');
	}
	return $erreurs;
}

function formulaires_configurer_document_fulltext_traiter_dist(){
	//Recuperation de la configuration et serialization
	$fulltext = serialize(array(
		'intervalle_cron' => intval(_request('intervalle_cron')),
		'nb_docs' => intval(_request('nb_docs')),
		'taille_index' => intval(_request('taille_index')),
		
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
	ecrire_meta('fulltext',$fulltext);
	$res = array('message_ok'=>_T('fulltext:message_ok_configuration'));
	return $res;
	
}
?>
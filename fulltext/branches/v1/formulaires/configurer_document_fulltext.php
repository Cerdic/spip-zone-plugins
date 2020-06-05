<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_document_fulltext_charger_dist() {
	include_spip('inc/config');

	//Valeurs prealablement saisie ou par defaut/d'exemple
	$valeur = array(
		'intervalle_cron' => lire_config('fulltext/intervalle_cron', 600),
		'nb_docs'         => lire_config('fulltext/nb_docs', 5),
		'taille_index'    => lire_config('fulltext/taille_index', 50000),

		'pdf_index' => lire_config('fulltext/pdf_index', 'off'),
		'pdf_bin'   => lire_config('fulltext/pdf_bin', '/usr/bin/pdftotext'),
		'pdf_opt'   => lire_config('fulltext/pdf_opt', '-enc Latin1'),

		'odt_index' => lire_config('fulltext/odt_index', 'off'),

		'doc_index' => lire_config('fulltext/doc_index', 'off'),
		'doc_bin'   => lire_config('fulltext/doc_bin', '/usr/bin/catdoc'),
		'doc_opt'   => lire_config('fulltext/doc_opt', '-s cp1252 -d 8859-1'),

		'docx_index' => lire_config('fulltext/docx_index', 'off'),

		'ppt_index' => lire_config('fulltext/ppt_index', 'off'),
		'ppt_bin'   => lire_config('fulltext/ppt_bin', '/usr/bin/catppt'),
		'ppt_opt'   => lire_config('fulltext/ppt_opt', ''),

		'pptx_index' => lire_config('fulltext/pptx_index', 'off'),

		'xls_index' => lire_config('fulltext/xls_index', 'off'),
		'xls_bin'   => lire_config('fulltext/xls_bin', '/usr/bin/xls2csv'),
		'xls_opt'   => lire_config('fulltext/xls_opt', '-s cp1252 -d 8859-1'),

		'xlsx_index' => lire_config('fulltext/xlsx_index', 'off'),
	);

	if (defined('_FULLTEXT_DOC_EXE')) {
		$valeur['doc_bin'] = _FULLTEXT_DOC_EXE;
		$valeur['doc_bin_readonly'] = true;
	}
	if (defined('_FULLTEXT_DOC_CMD_OPTIONS')) {
		$valeur['doc_opt'] = _FULLTEXT_DOC_CMD_OPTIONS;
		$valeur['doc_opt_readonly'] = true;
	}
	if (defined('_FULLTEXT_PDF_EXE')) {
		$valeur['pdf_bin'] = _FULLTEXT_PDF_EXE;
		$valeur['pdf_bin_readonly'] = true;
	}
	if (defined('_FULLTEXT_PDF_CMD_OPTIONS')) {
		$valeur['pdf_opt'] = _FULLTEXT_PDF_CMD_OPTIONS;
		$valeur['pdf_opt_readonly'] = true;
	}
	if (defined('_FULLTEXT_PPT_EXE')) {
		$valeur['ppt_bin'] = _FULLTEXT_PPT_EXE;
		$valeur['ppt_bin_readonly'] = true;
	}
	if (defined('_FULLTEXT_PPT_CMD_OPTIONS')) {
		$valeur['ppt_opt'] = _FULLTEXT_PPT_CMD_OPTIONS;
		$valeur['ppt_opt_readonly'] = true;
	}
	if (defined('_FULLTEXT_XLS_EXE')) {
		$valeur['xls_bin'] = _FULLTEXT_XLS_EXE;
		$valeur['xls_bin_readonly'] = true;
	}
	if (defined('_FULLTEXT_XLS_CMD_OPTIONS')) {
		$valeur['xls_opt'] = _FULLTEXT_XLS_CMD_OPTIONS;
		$valeur['xls_opt_readonly'] = true;
	}

	return $valeur;
}


function formulaires_configurer_document_fulltext_verifier_dist() {
	$erreurs = array();
	//Il faut au moins une seconde
	if ((!_request('intervalle_cron'))||(_request('intervalle_cron') < 1)) {
		$erreurs['intervalle_cron'] = _T('fulltext:erreur_intervalle_cron');
	}
	//Il faut au moins une documents a la fois
	if ((!_request('nb_docs'))||(_request('nb_docs') < 1)) {
		$erreurs['nb_docs'] = _T('fulltext:erreur_nb_docs');
	}
	//Il faut au moins indexer un caractere
	if ((!_request('taille_index'))||(_request('taille_index') < 1)) {
		$erreurs['taille_index'] = _T('fulltext:erreur_taille_index');
	}

	/**
	 * On teste les binaires
	 */
	$binaires = array('pdf_bin' => array('pdf_index', '_FULLTEXT_PDF_EXE'), 'doc_bin' => array('doc_index','_FULLTEXT_DOC_EXE'), 'ppt_bin' => array('ppt_index','_FULLTEXT_PPT_EXE'), 'xls_bin' => array('xls_index','_FULLTEXT_XLS_EXE'));
	foreach ($binaires as $binaire => $index) {
		/**
		 * On ne teste l'exécutable que si on index sinon ça ne sert à rien
		 */
		if (_request($index[0]) == 'on' && !defined($index[1])) {
			/**
			 * Pas de binaire => on doit en avoir un pour récupérer le contenu
			 */
			if (!_request($binaire)) {
				$erreurs[$binaire] = _T('fulltext:erreur_pdf_bin');
			} else {
				/**
				 * On teste avec la commande de base ...
				 * Le code de retour normal doit être 0
				 */
				@exec(_request($binaire), $retour_bin, $retour_bin_int);
				if ($retour_bin_int != 0) {
					/**
					 * Si cela retourne un mauvais code d'erreur
					 * on teste avec l'option -V (catdoc et catppt)
					 */
					@exec(_request($binaire).' -V', $retour_bin, $retour_bin_int);
					if ($retour_bin_int != 0) {
						/**
						 * Sinon on fait un test que le binaire est executable
						 * Cela nécessite un chemin complet du binaire
						 */
						@exec('test -x '._request($binaire), $retour_bin, $retour_bin_int);
						if ($retour_bin_int != 0) {
							$erreurs[$binaire] = _T('fulltext:erreur_binaire_indisponible');
						}
					}
				}
			}
		}
	}

	//TODO : verifier si on a bien une version PHP superieur a 5.2 avec option Zip si on indexe les odt,docx,xlsx,pptx
	if (count($erreurs) > 0) {
		$erreurs['message_erreur'] = _T('fulltext:erreur_verifier_configuration');
	}
	return $erreurs;
}

function formulaires_configurer_document_fulltext_traiter_dist() {
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
	ecrire_meta('fulltext', $fulltext);
	$res = array('message_ok'=>_T('fulltext:message_ok_configuration'));
	return $res;
}

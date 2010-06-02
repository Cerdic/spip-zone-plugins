<?php
/**
 * Plugin Stockage S3
 * Licence GPL (c) 2010 Natxo, Cedric
 *
 */

function stockageS3_actif(){
	$cfg = @unserialize($GLOBALS['meta']['stockage']);

	$providers = array('' => 'Amazon S3', 's3' => 'Amazon S3', 'gs' => 'Google Storage');

	if (strlen($cfg['s3publickey'])
	AND strlen($cfg['s3secretkey'])
	AND strlen($cfg['s3bucket'])
	)
		return
			$providers[$cfg['provider']];
	return false;
}

function stockageS3_document_desc_actions($flux){
	if ($id_document = intval($flux['args']['id_document'])
		AND $s = stockageS3_actif()){
		$flux['data'] .= recuperer_fond('modeles/stockageS3_actions', array('id_document'=>$id_document, 'provider' => $s));
	}
	return $flux;
}

function stockageS3_editer_document_actions($flux){
	if ($id_document = intval($flux['args']['id_document'])
		AND $s = stockageS3_actif()
		AND $distant = sql_getfetsel('distant', 'spip_documents', "id_document=".intval($id_document))
		AND $distant == 'non'){
		$flux['data'] .=
			"<input type='submit' class='submit' name='stockageS3_envoyer' value='".attribut_html(_T('stockageS3:envoyer_s3', array('provider' => $s)))."' />";
	}
	return $flux;
}

function stockageS3_formulaire_traiter($flux){
	if ($flux['args']['form']=='editer_document'
		AND _request('stockageS3_envoyer')
	  AND $id_document = $flux['data']['id_document']
		AND $flux['data']['message_ok']){
		$stockageS3_envoyer = charger_fonction('stockageS3_envoyer','action');
		$stockageS3_envoyer($id_document);
	}
	return $flux;
}
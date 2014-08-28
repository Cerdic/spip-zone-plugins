<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function formulaires_configurer_links_charger_dist(){
	$links = sql_fetsel('valeur', 'spip_meta', 'nom = "links"');
	$links = unserialize($links['valeur']);

	$valeur = array(
		'style' => $links['style'] ? $links['style'] : '',
		'external' => $links['external'] ? $links['external'] : 'off',
		'download' => $links['download'] ? $links['download'] : 'off',
		'window' => $links['window'] ? $links['window'] : 'off',
		'doc_list' => $links['doc_list'] ? $links['doc_list'] : '.pdf,.ppt,.xls,.doc'
	);

	return $valeur;
}
function formulaires_configurer_links_verifier_dist(){
	$erreurs = array();
	//Cas ou l'on veut des liens ouvrants sans rien choisir
	if((_request('window') == 'on')&&(!_request('external'))&&(!_request('download'))){
		$erreurs['window_new'] = _T('links:erreur_choisir_liens_ouvrants');
	}
	//Cas ou l'on veut des liens ouvrants sur les documents sans avoir specifier d'extension
	if((_request('download'))&&(!_request('doc_list'))){
		$erreurs['doc_list'] = _T('links:erreur_choisir_extensions');
	}
	return $erreurs;
}

function formulaires_configurer_links_traiter_dist(){
	$links = serialize(array('style' => _request('style'), 'window' => _request('window') ,'external' => _request('external'),'download' => _request('download'),'doc_list' => _request('doc_list')));
	//Insere ou update ?
	if($links_doc = sql_fetsel('valeur', 'spip_meta', 'nom = "links"')){
		//On update
		sql_updateq('spip_meta', array('valeur' => $links, 'impt' => 'oui'), 'nom="links"');
		$res = array('message_ok'=> _T('links:message_ok_update_configuration'));
	}else{
		//On insere
		$id = sql_insertq('spip_meta', array('nom'=>'links','valeur' => $links, 'impt' => 'oui'));
		$res = array('message_ok'=>_T('links:message_ok_configuration'));
	}
	return $res;
}
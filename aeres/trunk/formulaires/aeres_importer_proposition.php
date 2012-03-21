<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_aeres_importer_proposition_charger_dist($id_ticket){
	$contexte = array('id_ticket'=>$id_ticket);
	return $contexte;
}

function formulaires_aeres_importer_proposition_traiter_dist($id_ticket){
	include_spip('inc/zotspip');
	include_spip('inc/distant');
	include_spip('inc/hack-distant');
	include_spip('inc/config');
	include_spip('inc/abstract_sql');
	
	$zitem_json = sql_getfetsel('zitem_json','spip_tickets','id_ticket='.intval($id_ticket));
	$item = json_decode($zitem_json,true);
	$items = array('items' => array($item));
	$items = json_encode($items);
	
	$datas = "Content-Type: application/json\n";
	$datas .= "X-Zotero-Write-Token: ".md5($items)."\n\n";
	$datas .= $items;
	$ret = zotero_poster('items',$datas);
	
	if (intval($ret['headers'])==201) {
		if (preg_match('#<zapi:key>(.*)</zapi:key>#U',$ret['result'],$matches)) {
			$id_zitem = $matches[1];
			$toto = sql_updateq('spip_tickets',array('id_zitem'=>$id_zitem),'id_ticket='.intval($id_ticket));
			zotspip_maj_items();
		}
		$message = array('message_ok'=>'La référence a été correctement importée.');
	}
	
	if (intval($ret['headers'])==400)
		$message = array('message_erreur'=>'Import impossible : un ou plusieurs champs sont incorrects. Veuillez importer cette référence manuellement.');
	if (intval($ret['headers'])==409)
		$message = array('message_erreur'=>'Import impossible : la librairie est verrouillée.');
	if (intval($ret['headers'])==412)
		$message = array('message_erreur'=>'Import impossible : cette référence a déjà été importée dans Zotero.');
	
	return $message;
}

?>
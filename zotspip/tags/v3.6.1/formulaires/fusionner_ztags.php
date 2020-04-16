<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_fusionner_ztags_charger_dist($tag){
	return array('tag' => $auteur, 'dest' => '');
}

function formulaires_fusionner_ztags_verifier_dist($tag){
	include_spip('inc/autoriser');
	if (!autoriser('modifier','zotero')) return array('message_erreur'=>_T('zotspip:droits_insuffisants'));
}

function formulaires_fusionner_ztags_traiter_dist($tag){
	if (_request('remplacer'))
		return array('message_ok' => 'confirmer');
	if (_request('confirmer')) {
		include_spip('inc/zotspip');
		include_spip('base/abstract_sql');
		$dest = _request('dest');
		
		$zitems = sql_allfetsel('id_zitem','spip_ztags','tag='._q($tag));
		$itemKey = array();
		foreach($zitems as $zitem)
			$itemKey[] = $zitem['id_zitem'];
		$itemKey = implode(',',$itemKey);
		$actuel = zotero_get("items/?format=atom&content=json&itemKey=$itemKey");
		if (!$actuel)
			return array('message_erreur' => _T('zotspip:erreur_connexion'));
		if (preg_match_all('#<zapi:key>(.*)</zapi:key>(.*)<content zapi:type="json" zapi:etag="(.*)">(.*)</content>#Uis',$actuel,$matches,PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$key = $match[1];
				$etag = $match[3];
				$json = json_decode($match[4],true);
				foreach ($json['tags'] as $cle => $ltag) {
					if (isset($ltag['tag']) && $ltag['tag']==$tag)
						$json['tags'][$cle]['tag'] = $dest;
				}
				$json = json_encode($json);
				$datas = "Content-Type: application/json\n";
				$datas .= "If-Match: \"$etag\"\n\n";
				$datas .= $json;
				$ret = zotero_poster("items/$key",$datas,'PUT');
				if (!is_array($ret['headers'])) // Note : si tout vas bien, ça renvoie l'item et les headers ne sont pas égals à un nombre
					return array('message_erreur' => _T('zotspip:probleme_survenu_lors_du_remplacement',array('code'=>$ret['headers'])));
			}
		}
		// Si arrivé jusque là, pas de problème
		zotspip_maj_items();
		return array('redirect'=>generer_url_ecrire('ztags'));
	}
}

?>
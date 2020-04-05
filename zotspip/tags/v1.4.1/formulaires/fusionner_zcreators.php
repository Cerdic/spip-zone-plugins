<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_fusionner_zcreators_charger_dist($auteur){
	return array('auteur' => $auteur, 'dest' => '');
}

function formulaires_fusionner_zcreators_verifier_dist($auteur){
	include_spip('inc/autoriser');
	if (!autoriser('modifier','zotero')) return array('message_erreur'=>_T('zotspip:droits_insuffisants'));
}

function formulaires_fusionner_zcreators_traiter_dist($auteur){
	if (_request('remplacer'))
		return array('message_ok' => 'confirmer');
	if (_request('confirmer')) {
		include_spip('inc/zotspip');
		include_spip('base/abstract_sql');
		$auteur_decompose = explode(',',$auteur);
		$nom = trim($auteur_decompose[0]);
		$prenom = trim($auteur_decompose[1]);
		$dest_decompose = explode(',',_request('dest'));
		$nom_dest = trim($dest_decompose[0]);
		$prenom_dest = trim($dest_decompose[1]);
		
		$zitems = sql_allfetsel('id_zitem','spip_zcreators','auteur='._q($auteur));
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
				foreach ($json['creators'] as $cle => $creator) {
					if (isset($creator['name']) && $creator['name']==trim($auteur))
						$json['creators'][$cle]['name'] = _request('dest');
					if (isset($creator['lastName']) && $creator['lastName']==$nom) {
						$json['creators'][$cle]['lastName'] = $nom_dest;
						$json['creators'][$cle]['firstName'] = $prenom_dest;
					}
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
		return array('redirect'=>generer_url_ecrire('zcreators'));
	}
}

?>
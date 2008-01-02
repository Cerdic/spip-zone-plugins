<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function party_install($action) {

	$url = 'http://www.spip-party.net/calendrier.xml';
	$id = isset($GLOBALS['meta']['id_syndic_party']) ? intval($GLOBALS['meta']['id_syndic_party']) : false;

	switch ($action) {
		case 'test':
			return $id;
		break;
		case 'install':
			return party_installation($url);
		break;
		case 'uninstall':
			return party_desinstallation($id);
		break;
		default:
			return true;
		break;
	}
}

function party_installation($url) {
	//syndication auto si necessaire
	if(!function_exists('sql_getfetsel')) {
		$r = spip_fetch_array(spip_query("SELECT id_syndic FROM spip_syndic WHERE url_syndic='".$url."' AND statut='publie'"));
		$id = $r['id_syndic'];
	}
	else
		$id = sql_getfetsel('id_syndic', 'spip_syndic', array('url_syndic = "' . $url . '"', 'statut="publie"'));

	if(!$id) {
		include_spip('action/editer_site');
		$site = analyser_site($url);
		$id = insert_syndic(0);
		$c = array (
			'nom_site' => $site['nom_site'],
			'url_site' => $site['url_site'],
			'statut' => 'publie',
			'url_syndic' => $url,
			'syndication' => 'oui');
		revisions_sites($id, $c);
		if(!function_exists('sql_updateq')) {
			spip_query("UPDATE spip_syndic SET resume='oui', oubli='oui' WHERE id_syndic=".$id);
			include_spip('inc/syndic');
			syndic_a_jour($id);
		}
		else {
			sql_updateq('spip_syndic', array('resume' => 'oui', 'oubli' => 'oui'), 'id_syndic = ' . $id);
			define('_GENIE_SYNDIC_NOW', $id);
			cron(0, array('syndic' => -91));
		}
	}
	//memorisation du site cree dans le meta #CONFIG{id_syndic_party}
	ecrire_meta('id_syndic_party', $id);
	ecrire_metas();
	return true;
}

function party_desinstallation($id) {
	//supprimer le site et les articles syndiques
	if(!function_exists('sql_delete')) {
		spip_query("DELETE FROM spip_syndic WHERE id_syndic=".intval($id));
		spip_query("DELETE FROM spip_syndic_articles WHERE id_syndic=".intval($id));
	}
	else {
		$r = sql_delete('spip_syndic', 'id_syndic = ' . intval($id));
		$r = sql_delete('spip_syndic_articles', 'id_syndic = ' . intval($id) );
	}
	//effacer le meta #CONFIG{id_syndic_party}
	effacer_meta('id_syndic_party');
	return true;
}

?>
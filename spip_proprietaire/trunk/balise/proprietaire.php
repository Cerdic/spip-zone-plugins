<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_PROPRIETAIRE($p) {
	spip_proprio_charger_toutes_les_langues();

	return calculer_balise_dynamique($p, 'PROPRIETAIRE', array());
}

function balise_PROPRIETAIRE_dyn($wich = '', $who = '', $separator = '<br />') {
	include_spip('inc/presentation');
	$conf = spip_proprio_recuperer_config();
	static $spip_proprio_no_config = false;
	if (is_null($conf)) {
		include_spip('inc/autoriser');
		if ($spip_proprio_no_config === false && autoriser('ecrire')) {
			$div = propre(_T('proprietaire:pas_config', array(
				'url_config' => generer_url_ecrire('spip_proprio'),
			)));
			echo $div;
			$spip_proprio_no_config = true;
		}

		return;
	}

	$nb = '&nbsp;';
	$div = '';

	if (isset($conf[$wich])) {
		$div = $conf[$wich];
	} else {
		switch ($wich) {
		case 'footer':
		case 'copyright':
			$nom_site = typo($conf['proprietaire_nom'])
				.((isset($conf['adresse_pays']) and strlen($conf['adresse_pays'])) ? ' - '.$conf['adresse_pays'] : '');
			if ($wich == 'footer') {
				$div .= '<small>';
			}
			$div .= _T('proprietaire:copyright_info', array(
				'nom_site' => $nom_site,
				'date' => ((isset($conf['copyright_annee']) and strlen($conf['copyright_annee'])) ? $conf['copyright_annee'].'-' : '').date('Y'),
			));
			if (isset($conf['copyright_complement']) and strlen($conf['copyright_complement'])) {
				$div .= $separator.typo($conf['copyright_complement']);
			}
			if (isset($conf['copyright_comment']) and strlen($conf['copyright_comment'])) {
				$div .= $separator.typo($conf['copyright_comment']);
			}
			if ($wich == 'footer') {
				$div .= '</small>';
			}
			break;
		case 'googlemap_string':
			if ($google = make_google_map_proprietaire($conf)) {
				$div .= $google;
			}
			break;
		case 'vcard' :
			$div .= propre(_T('proprietaire:vcard_info', array(
				'vcard_url' => url_absolue(generer_url_public('vcard')),
				'vcard_url_download' => url_absolue(generer_url_public('vcard', 'telechargement=oui')),
			)));
			break;
		case 'cartes_visite' :
			$cartes_visite_urls = array(
				'classique' => url_absolue(generer_url_public('carte_visite')),
				'complete' => url_absolue(generer_url_public('carte_visite', 'type=site')),
				'responsable' => url_absolue(generer_url_public('carte_visite', 'type=chef')),
				'administrateur' => url_absolue(generer_url_public('carte_visite', 'type=admin')),
			);
			if (isset($GLOBALS['meta']['email_webmaster']) and strlen($GLOBALS['meta']['email_webmaster'])) {
				$cartes_visite_urls['webmaster'] = url_absolue(generer_url_public('carte_visite', 'type=webmaster'));
			}
			$div .= propre(_T('proprietaire:carte_visite_info', $cartes_visite_urls));
			break;
		case 'business_cards' :
			$div .= propre(_T('proprietaire:business_cards'));
			$cartes_visite_urls = array(
				'classique' => url_absolue(generer_url_public('carte_visite')),
				'complete' => url_absolue(generer_url_public('carte_visite', 'type=site')),
				'responsable' => url_absolue(generer_url_public('carte_visite', 'type=chef')),
				'administrateur' => url_absolue(generer_url_public('carte_visite', 'type=admin')),
			);
			if (isset($GLOBALS['meta']['email_webmaster']) and strlen($GLOBALS['meta']['email_webmaster'])) {
				$cartes_visite_urls['webmaster'] = url_absolue(generer_url_public('carte_visite', 'type=webmaster'));
			}
			$div .= propre(_T('proprietaire:carte_visite_info', $cartes_visite_urls));
			$div .= propre(_T('proprietaire:vcard_info', array(
				'vcard_url' => url_absolue(generer_url_public('vcard')),
				'vcard_url_download' => url_absolue(generer_url_public('vcard', 'telechargement=oui')),
			)));
			break;
		case 'carte_visite' :
		case 'carte_visite_image' :
			$contexte = $conf;
			if (strlen($who)) {
				if (in_array(trim($who), array('admin', 'administrateur', 'administration'))) {
					$who = 'admin';
				} elseif (in_array(trim($who), array('webmaster', 'webmestre'))) {
					$who = 'webmaster';
				} elseif (in_array(trim($who), array('responsable', 'boss', 'chef'))) {
					$who = 'chef';
				}
			}
			$contexte['who'] = $who;
			if ($wich == 'carte_visite_image') {
				$contexte['type'] = 'image';
			}
			$div .= recuperer_fond('modeles/carte_visite', $contexte);
			break;
		case 'googlemap' :
			if (!strlen($who)) {
				$who = 'proprietaire';
			}
			$contexte['googlemap_string'] = make_google_map_proprietaire($conf, $who);
			$div .= recuperer_fond('modeles/noisette_googlemap', $contexte);
			break;
		case 'logo' :
		default :
			$contexte = $conf;
			$contexte['separator'] = $separator;
			if ($wich == 'logo') {
				$contexte['logo'] = 'oui';
			}
			$div .= recuperer_fond('modeles/noisette_proprietaire', $contexte);
			break;
	}
	}

	echo $div;
}

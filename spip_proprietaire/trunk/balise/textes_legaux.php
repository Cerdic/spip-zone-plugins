<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_TEXTES_LEGAUX($p) {
	spip_proprio_charger_toutes_les_langues();

	return calculer_balise_dynamique($p, 'TEXTES_LEGAUX', array());
}

function balise_TEXTES_LEGAUX_dyn($chaine = '', $who = '', $separator = '<br />') {
	include_spip('inc/presentation');
	include_spip('spip_proprio_fonctions');
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

	$conf['nom_site'] = $GLOBALS['meta']['nom_site'];
	$conf['url_site'] = $GLOBALS['meta']['adresse_site'];
	$conf['descriptif_site'] = textebrut($GLOBALS['meta']['descriptif_site']);
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$logo_site = $chercher_logo(0, 'id_syndic', 'on');
	$conf['logo_site'] = $logo_site[0];

	$entries = array('proprietaire', 'hebergeur', 'createur');
	foreach ($entries as $entry) {
		if (isset($conf[$entry.'_legal_forme']) and strlen($conf[$entry.'_legal_forme'])) {
			$article = ($conf[$entry.'_legal_genre'] == 'fem') ? _T('spipproprio:la') : _T('spipproprio:le');
			$conf[$entry.'_forme'] =
				($conf[$entry.'_legal_abbrev'] and strlen($conf[$entry.'_legal_abbrev'])) ?
					apostrophe($conf[$entry.'_legal_abbrev'], $article) :
						apostrophe($conf[$entry.'_legal_forme'], $article);
		} else {
			$conf[$entry.'_forme'] = '';
		}

		$conf[$entry.'_web'] =
			(isset($conf[$entry.'_site_web']) and strlen($conf[$entry.'_site_web']) > 7) ?
				_T('texteslegaux:reportez_vous_au_site', array('site' => $conf[$entry.'_site_web'])) : '';

		$conf[$entry.'_mail_texte'] =
			(isset($conf[$entry.'_mail']) and strlen($conf[$entry.'_mail'])) ?
				$separator._T('texteslegaux:pour_les_contacter', array('mail' => $conf[$entry.'_mail'])) : '';

		$conf[$entry.'_fonction_responsable_texte'] =
			(isset($conf[$entry.'_fonction_responsable']) and strlen($conf[$entry.'_fonction_responsable'])) ?
				' ('.$conf[$entry.'_fonction_responsable'].')' : '';
	}

	$conf['type_serveur_texte'] =
		(isset($conf['type_serveur']) and strlen($conf['type_serveur'])) ?
			_T('texteslegaux:sur_un_serveur', array('serveur' => $conf['type_serveur'])) : '';

	$conf['os_serveur_texte'] =
		(isset($conf['os_serveur']) and strlen($conf['os_serveur'])) ?
			_T('texteslegaux:os_du_serveur', array(
				'os_serveur' => ($conf['os_serveur_web'] and strlen($conf['os_serveur_web'])) ?
					'<a href="'.$conf['os_serveur_web'].'" class="spip_out">'.$conf['os_serveur'].'</a>' : $conf['os_serveur'],
			)) : '';

	$conf['cnil_texte'] =
		(isset($conf['numero_cnil']) and strlen($conf['numero_cnil'])) ?
			'<br />'._T('texteslegaux:mention_cnil', $conf) : '';

	$conf['createur_administrateur_texte'] =
		(isset($conf['createur_administrateur']) and $conf['createur_administrateur'] == 'oui') ?
			_T('texteslegaux:egalement_administrateur') : '';

//	$div = propre( _T('texteslegaux:'.$chaine, $conf) );
	$div = propre(_T('proprietaire:'.$chaine, $conf));
	echo $div;
}

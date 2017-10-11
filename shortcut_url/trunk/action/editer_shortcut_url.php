<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/autoriser');

/**
 * Supprimer définitivement un URL
 *
 * @param int $id_shortcut_url identifiant numérique du url
 * @return int|false 0 si réussite, false dans le cas ou l'url n'existe pas
 */
function shortcut_url_supprimer($id_shortcut_url) {
	$valide = sql_getfetsel('id_shortcut_url', 'spip_shortcut_urls', 'id_shortcut_url='.intval($id_shortcut_url));
	if ($valide && autoriser('supprimer', 'shortcut_url', $valide)) {
		sql_delete('spip_shortcut_urls', 'id_shortcut_url='.intval($id_shortcut_url));
		sql_delete('spip_auteurs_liens', 'objet="shortcut_url" AND id_objet='.intval($id_shortcut_url));
		sql_delete('spip_urls', 'id_objet=' . intval($id_shortcut_url) . ' AND type=' . sql_quote('shortcut_url'));
		$id_shortcut_url = 0;
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_shortcut_url/$id_shortcut_url'");
		return $id_shortcut_url;
	}
	return false;
}


function shortcut_url_inserer($set = null) {

	$champs = array(
		'date_modif' => date('Y-m-d H:i:s'),
		'ip_address' => $GLOBALS['ip']
	);

	if ($set) {
		$champs = array_merge($champs, $set);
	}

	// Envoyer aux plugins
	$champs = pipeline(
		'pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_shortcut_urls',
			),
			'data' => $champs
		)
	);

	$id_shortcut_url = sql_insertq('spip_shortcut_urls', $champs);

	if ($id_shortcut_url) {
		pipeline(
			'post_insertion',
			array(
				'args' => array(
					'table' => 'spip_shortcut_urls',
					'id_objet' => $id_shortcut_url
				),
				'data' => $champs
			)
		);
	}

	return $id_shortcut_url;
}

function shortcut_url_modifier($id_shortcut_url, $set = null) {
	include_spip('inc/modifier');
	include_spip('inc/filtres');

	$c = collecter_requests(
		objet_info('shortcut_url', 'champs_editables'),
		array(),
		$set
	);

	include_spip('inc/actions');
	include_spip('inc/editer');
	include_spip('action/editer_objet');
	include_spip('inc/distant');

	// On supprime ?var_mode=recalcul et autres var_mode
	$c['url'] = parametre_url(urldecode($c['url']), 'var_mode', '', '&');
	$recup = recuperer_page($c['url'], true);
	if (preg_match(',<title[^>]*>(.*),i', $recup, $regs)) {
		$c['description'] = filtrer_entites(
			supprimer_tags(preg_replace(',</title>.*,i', '', $regs[1]))
		);
	}

	if (defined('_TAILLE_RACCOURCI')) {
		if (_TAILLE_RACCOURCI >= 5) {
			$taille_raccourci = _TAILLE_RACCOURCI;
		} else {
			$taille_raccourci = 8;
		}
	} else {
		$taille_raccourci = 8;
	}

	if (!isset($c['titre']) or $c['titre'] == '') {
		$c['titre'] = generer_chaine_aleatoire($taille_raccourci);
	}

	$invalideur = "id='shortcut_url/$id_shortcut_url'";
	$indexation = true;

	if ($err = objet_modifier_champs(
		'shortcut_url',
		$id_shortcut_url,
		array(
			'data' => $set,
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			'date_modif' => 'date_modif'
		),
		$c
	)) {
			return $err;
	}

	return $err;
}

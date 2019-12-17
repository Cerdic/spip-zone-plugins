<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_MOTSCREER_DEUX_POINTS_SUBSTITUT', '%%DEUXPOINTS%%');

function formulaires_motscreer_charger_dist() {
	$valeurs = array(
		'id_groupe'              => '',
		'mots'                   => '',
		'necessite_confirmation' => false,
	);

	$valeurs['mots_explications'] = test_plugin_actif('motsar') ? 'mots_explications_arbo' : 'mots_explications';

	return $valeurs;
}

function formulaires_motscreer_verifier_dist() {
	$retour = array();
	if (!_request('mots')) {
		$retour['mots'] = _T('info_obligatoire');
	}
	if (!$id_groupe = _request('id_groupe')) {
		$retour['id_groupe'] = _T('info_obligatoire');
	}
	if (!autoriser('modifier', 'groupemots', $id_groupe)) {
		$retour['id_groupe'] = _T('motscreer:pas_autorise');
	}

	if (!$retour && test_plugin_actif('motsar')) {

		// on parse les mots clés fournis
		//les données calculées sont un tableau ? l'analyse yaml a réussi
		$mots = _motscreer_parse_texte(_request('mots'));
		if (is_array($mots)) {
			$mots_arborescents = false;
			foreach ($mots as $mot) {
				if (is_array($mot)) {
					$mots_arborescents = true;
				}
			}
			// si les mots à créer sont arborescents, on vérifie un peu plus
			// et on demande une confirmation
			if ($mots_arborescents) {
				set_request('necessite_confirmation', true);
				if (sql_getfetsel('mots_arborescents', 'spip_groupes_mots', 'id_groupe=' . $id_groupe) != 'oui') {
					$retour['id_groupe'] = _T('motscreer:groupe_pas_arborescent');
				} else {
					// confirmation intermédiaire
					if (_request('confirmer') != 'on') {

						$previsu = '{{{' . _T('motscreer:apercu_import') . '}}}';
						$previsu .= _T('motscreer:dans_le groupe') . ' {{' . sql_getfetsel('titre', 'spip_groupes_mots', 'id_groupe=' . $id_groupe) . '}}';

						$previsu                  .= _motscreer_traiter_mot($mots, $id_groupe, 0, 'previsu', 0, '');
						$retour['previsu']        = $previsu;
						$retour['message_erreur'] = _T('motscreer:confirmer_import');

					}
				}
			}
		} // sinon, on retourne le message d'erreur
		else {
			$retour['erreur_analyse'] = _T('motscreer:erreur_analyse') . "\n\n" . '{{' . $mots . '}}';
			$retour['message_erreur'] = _T('motscreer:erreur_analyse');
		}

	}

	return $retour;
}

function formulaires_motscreer_traiter_dist() {
	include_spip('action/editer_mot');
	
	$id_groupe = intval(_request('id_groupe'));
	$mots      = _motscreer_parse_texte(_request('mots'));

	$erreur = _motscreer_traiter_mot($mots, $id_groupe);
	
	if($erreur){
		$retour = array(
			'message_erreur' => $erreur,
			'editable'   => true,
		);
	} else {
		$retour = array(
			'message_ok' => _T('motscreer:mots_crees'),
			'editable'   => false,
		);
	}
	return $retour;
}

function _motscreer_traiter_mot($mots, $id_groupe, $id_parent = 0, $mode = 'creer', $profondeur = 0, $retour = '') {
	static $titre_groupe;
	if (!is_array($mots)) {
		return;
	}
	if (!$titre_groupe) {
		$titre_groupe = sql_getfetsel('titre', 'spip_groupes_mots', 'id_groupe=' . $id_groupe);
	}

	foreach ($mots as $key => $value) {
		$titre = str_replace(_MOTSCREER_DEUX_POINTS_SUBSTITUT, ':', $key);
		if ($mode == 'creer') {
			$data = array(
				'titre'      => $titre,
				'id_groupe'  => $id_groupe,
				'type'       => $titre_groupe,
			);
			if(test_plugin_actif('motsar')) {
				$data['profondeur'] = $profondeur;
				$data['id_parent'] = $id_parent;
			}
			if (!$id_mot = sql_insertq('spip_mots', $data)) {
				return _T('erreur_technique_enregistrement_impossible');
			}
		} else {
			$retour .= "\n" . '-' . str_repeat('*', $profondeur) . '* <span class="mot">' . $titre . '</span>';
		}
		$retour .= _motscreer_traiter_mot($value, $id_groupe, $id_mot, $mode, $profondeur + 1, '');
	}

	return $retour;
}

function _motscreer_parse_texte($texte) {
	require_spip('inc/yaml');

	// transformer le texte en YAML pour pouvoir le décoder 

	// remplacer les : des titres par un marqueur qu'on supprimera ensuite
	$yaml = str_replace(':', _MOTSCREER_DEUX_POINTS_SUBSTITUT, $texte);
	// ajouter : à la fin de chaque ligne pour indiquer les sous rubriques
	$yaml = preg_replace('#^(\s*)([^\r\n]+).*$#m', '$1$2:', $yaml);

	// retourner un tableau en cas de succès, une chaine en cas d'erreur
	try {
		$retour = yaml_decode($yaml);
	} catch (Exception $e) {
		$retour = $e->getMessage();
	}

	return $retour;

}
<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_RUBRIQUEUR_DEUX_POINTS_SUBSTITUT', '%%DEUXPOINTS%%');

function formulaires_rubriqueur_charger_dist() {

	$langues_utilisees = liste_options_langues('var_lang');
	$langues = array();
	foreach ($langues_utilisees as $langue) {
		$langues[$langue] = spip_ucfirst(html_entity_decode($GLOBALS['codes_langues'][$langue]));
	}

	return array(
		'rubrique_racine' => '',
		'rubriques'       => '',
		'langue'          => _request('langue') ? _request('langue') : lire_config('langue_site'),
		'langues'         => $langues,
	);
}

function formulaires_rubriqueur_verifier_dist() {
	$retour = array();
	if (!_request('rubriques')) {
		$retour['rubriques'] = _T('champ_obligatoire');
	}
	$rubrique_racine = picker_selected(_request('rubrique_racine'), 'rubrique');
	$rubrique_racine = array_pop($rubrique_racine);
	if (!autoriser('creerrubriquedans', 'rubrique', $rubrique_racine)) {
		$retour['message_erreur'] = _T('rubriqueur:pas_autorise');
	}

	// confirmation intermédiaire
	if (!$retour && _request('confirmer') != 'on') {
		$data = _rubriqueur_parse_texte(_request('rubriques'), 'previsu');
		// les données calculées sont un tableau ? l'analyse yaml a réussi
		if (is_array($data)) {
			$previsu = '{{{' . _T('rubriqueur:apercu_import') . '}}}';
			if ((int)_request('rubrique_racine')) {
				$previsu .= _T('rubriqueur:dans_la_rubrique') . ' {{' . sql_getfetsel('titre', 'spip_rubriques',
						'id_rubrique=' . $rubrique_racine) . '}}';
			} else {
				$previsu .= _T('rubriqueur:a_la_racine');
			}
			$langue    = _request('langue');
			if(!$langue){
				$langue = lire_config('langue_site');
			}
			$previsu                  .= _rubriqueur_traiter_rubrique($data, $rubrique_racine, 'previsu', 0, '', $langue);
			$retour['previsu']        = $previsu;
			$retour['message_erreur'] = _T('rubriqueur:confirmer_import');
		} 
		// sinon, on retourne le message d'erreur
		else {
			$retour['erreur_analyse'] = _T('rubriqueur:erreur_analyse') . "\n\n" . '{{'.$data.'}}';
			$retour['message_erreur'] = _T('rubriqueur:erreur_analyse');
		}
	}

	return $retour;
}

function formulaires_rubriqueur_traiter_dist() {
	$rubrique_racine = 0;
	if(_request('rubrique_racine')) {
		$rubrique_racine = array_pop(picker_selected(_request('rubrique_racine'), 'rubrique'));
	}
	$rubriques = _rubriqueur_parse_texte(_request('rubriques'));
	$langue    = _request('langue');
	if(!$langue){
		$langue = lire_config('langue_site');
	}
	
	_rubriqueur_traiter_rubrique($rubriques, $rubrique_racine, 'creer', 0, '', $langue);

	// mettre à jour les status, id_secteur et profondeur
	include_spip('inc/rubriques');
	calculer_rubriques();
	propager_les_secteurs();

	return array(
		'message_ok' => _T('rubriqueur:rubriques_creees'),
		'editable'   => false,
	);
}

function _rubriqueur_traiter_rubrique($rubriques, $id_parent = 0, $mode = 'creer', $profondeur = 0, $retour = '', $langue = '') {
	if(!is_array($rubriques)) {
		return;
	}
	foreach ($rubriques as $key => $value) {
		if (is_numeric($key)) {
			$titre = str_replace(_RUBRIQUEUR_DEUX_POINTS_SUBSTITUT, ':', $value);
			if ($mode == 'creer') {
				sql_insertq('spip_articles', array(
					'titre'       => $titre,
					'id_rubrique' => $id_parent,
					'statut'      => 'publie',
					'lang'        => $langue,
					'date'        => date('Y-m-d H:i:s'),
				));
			} else {
				$retour .= "\n" . '-' . str_repeat('*', $profondeur) . '* <span class="article">' . $titre . '</span>';
			}
		} else {
			$titre = str_replace(_RUBRIQUEUR_DEUX_POINTS_SUBSTITUT, ':', $key);
			if ($mode == 'creer') {
				$id_rubrique = sql_insertq('spip_rubriques', array(
					'titre'     => $titre,
					'id_parent' => $id_parent,
					'statut'    => 'publie',
					'lang'      => $langue,
					'date'      => date('Y-m-d H:i:s'),
				));
			} else {
				$retour .= "\n" . '-' . str_repeat('*', $profondeur) . '* <span class="rubrique">' . $titre . '</span>';
			}
			$retour .= _rubriqueur_traiter_rubrique($value, $id_rubrique, $mode, $profondeur + 1, '', $langue);
		}
	}

	return $retour;
}

function _rubriqueur_parse_texte($texte, $mode = 'creer', $indentation = '  ') {
	require_spip('inc/yaml');

	// transformer le texte en YAML pour pouvoir le décoder 

	// remplacer les : des titres par un marqueur qu'on supprimera ensuite
	$yaml = str_replace(':', _RUBRIQUEUR_DEUX_POINTS_SUBSTITUT, $texte);
	// ajouter : à la fin de chaque ligne pour indiquer les sous rubriques
	$yaml = preg_replace('#^(\s*)([^\r\n]+).*$#m', '$1$2:', $yaml);
	// supprimer les : sur les lignes d'articles (pas d'enfants)
	$yaml = preg_replace('#^(\s*)(\-\s)([^:\r\n]+)(:).*$#m', '$1- $3', $yaml);

	// retourner un tableau en cas de succès, une chaine en cas d'erreur
	try {
		$retour = yaml_decode($yaml);
	} catch (Exception $e) {
		$retour = $e->getMessage();
	}

	return $retour;

}

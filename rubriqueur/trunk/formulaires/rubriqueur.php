<?php

if( !defined('_ECRIRE_INC_VERSION') ){
	return;
}

function formulaires_rubriqueur_charger_dist() {

	return array(
		'rubrique_racine' => '',
		'rubriques'       => '',
	);
}

function formulaires_rubriqueur_verifier_dist() {
	$retour = array();
	if( !_request('rubriques') ){
		$retour['rubriques'] = _T('champ_obligatoire');
	}
	$rubrique_racine = array_pop(picker_selected(_request('rubrique_racine'), 'rubrique'));
	if( !autoriser('creerrubriquedans','rubrique',$rubrique_racine)){
		$retour['message_erreur'] = _T('rubriqueur:pas_autorise');
	}
	
	// confirmation interméfiaire
	if( !$retour && _request('confirmer') != 'on' ){
		$data = rubriqueur_parse_texte(_request('rubriques'), 'previsu');
		if( (int)_request('rubrique_racine') ){
			$previsu = _T('rubriqueur:dans_la_rubrique') . ' ' . sql_getfetsel('titre', 'spip_rubriques', 'id_rubrique=' . $rubrique_racine);
		}
		else {
			$previsu = _T('rubriqueur:a_la_racine');
		}
		$previsu .= "\n" . join("\n", $data);
		$retour['previsu'] = $previsu;
	}

	return $retour;
}

function formulaires_rubriqueur_traiter_dist() {
	$rubrique_racine = array_pop(picker_selected(_request('rubrique_racine'), 'rubrique'));
	$rubriques       = rubriqueur_parse_texte(_request('rubriques'));
	include_spip('inc/rubriques');
	foreach( $rubriques as $rubrique ) {
		creer_rubrique_nommee($rubrique, $rubrique_racine);
	}
	return array(
		'message_ok' => _T('rubriqueur:rubriques_creees'),
		'editable'   => false,
	);
}

function rubriqueur_parse_texte($texte, $mode = 'creer', $indentation = '  ') {
	$retour            = array();
	$rappel_profondeur = 0;
	$chemin            = array();
	$lignes            = explode("\n", $texte);
	foreach( $lignes as $ligne ) {
		if( !trim($ligne) ){
			continue;
		}
		$profondeur = 0;
		while( substr($ligne, 0, strlen($indentation)) === $indentation ) {
			$profondeur += 1;
			$ligne = substr($ligne, strlen($indentation));
		}
		if( $rappel_profondeur > $profondeur ){
			array_splice($chemin, $profondeur);
		}
		$chemin[$profondeur] = trim($ligne);
		if( $mode == 'previsu' ){
			$retour[] = '-' . str_repeat('*', $profondeur) . '* ' . $ligne;
		}
		else {
			$retour[] = join('/', $chemin);
		}
		$rappel_profondeur = $profondeur;
	}

	return $retour;
}

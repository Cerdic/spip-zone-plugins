<?php

if( !defined('_ECRIRE_INC_VERSION') ){
	return;
}

define('_RUBRIQUEUR_SEPARATEUR', '%%SLASH%%');

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
	$rubrique_racine = picker_selected(_request('rubrique_racine'), 'rubrique');
	$rubrique_racine = array_pop($rubrique_racine);
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
	$rubrique_racine = picker_selected(_request('rubrique_racine'), 'rubrique');
	$rubrique_racine = array_pop($rubrique_racine);
	$rubriques       = rubriqueur_parse_texte(_request('rubriques'));
	include_spip('inc/rubriques');
	foreach( $rubriques as $rubrique ) {
		rubriqueur_creer_rubrique_nommee($rubrique, $rubrique_racine, _RUBRIQUEUR_SEPARATEUR);
	}
  // mettre à jour les status, id_secteur et profondeur
  include_spip('inc/rubriques');
  calculer_rubriques();
  propager_les_secteurs();
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
			$retour[] = join(_RUBRIQUEUR_SEPARATEUR, $chemin);
		}
		$rappel_profondeur = $profondeur;
	}

	return $retour;
}

/**
 * Crée une arborescence de rubrique
 *
 * Copie modifiée de creer_rubrique_nommee() depuis /ecrire/inc/rubriques,
 * pour ne pas modifier la signature de la fonction originale.
 * Ajout du séparateur en paramètre.
 * 
 * @param string $titre
 *     Titre des rubriques, séparés par des $separateur
 * @param int $id_parent
 *     Identifiant de la rubrique parente
 * @param string $separateur
 *     Séparateur du chemin
 * @param string $serveur
 *     Nom du connecteur à la base de données
 * 
*@return int
 *     Identifiant de la rubrique la plus profonde.
 */
function rubriqueur_creer_rubrique_nommee($titre, $id_parent=0, $separateur='/', $serveur='') {

	// eclater l'arborescence demandee
	// echapper les </multi> et autres balises fermantes html
	$titre = preg_replace(",</([a-z][^>]*)>,ims","<@\\1>",$titre);
	$arbo = explode($separateur, preg_replace(',^/,', '', $titre));
	include_spip('base/abstract_sql');
	foreach ($arbo as $titre) {
		// retablir les </multi> et autres balises fermantes html
		$titre = preg_replace(",<@([a-z][^>]*)>,ims","</\\1>",$titre);
		$r = sql_getfetsel("id_rubrique", "spip_rubriques", "titre = ".sql_quote($titre)." AND id_parent=".intval($id_parent),
			$groupby = array(), $orderby = array(), $limit = '', $having = array(), $serveur);
		if ($r !== NULL) {
			$id_parent = $r;
		} else {
			$id_rubrique = sql_insertq('spip_rubriques', array(
					'titre' => $titre,
					'id_parent' => $id_parent,
					'statut' => 'prive')
				,$desc=array(), $serveur);
			if ($id_parent > 0) {
				$data = sql_fetsel("id_secteur,lang", "spip_rubriques", "id_rubrique=$id_parent",
					$groupby = array(), $orderby = array(), $limit = '', $having = array(), $serveur);
				$id_secteur = $data['id_secteur'];
				$lang = $data['lang'];
			} else {
				$id_secteur = $id_rubrique;
				$lang = $GLOBALS['meta']['langue_site'];
			}

			sql_updateq('spip_rubriques', array('id_secteur'=>$id_secteur, "lang"=>$lang), "id_rubrique=$id_rubrique", $desc='', $serveur);

			// pour la recursion
			$id_parent = $id_rubrique;
		}
	}

	return intval($id_parent);
}
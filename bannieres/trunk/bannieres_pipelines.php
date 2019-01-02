<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Champ extra : objet extensibles
*/
function bannieres_objets_extensibles($objets){
	return array_merge($objets, array('banniere' => _T('bannieres:bannieres')));
}

/**
 * JqueryUi.datePicker
*/
function bannieres_jqueryui_plugins($scripts){
	$scripts[] = "jquery.ui.datepicker";
	return $scripts;
}

/**
 * banniere_encart()
 *
 * affiche les documents sur la page edition d'une banniere
*/
function bannieres_encart($flux) {

	$id_banniere = $flux;

	// pour charger une banniere au moment de la création, on fait comme dans de core
	// inspiré de articles_edit.php
	if ($id_banniere != 'oui') {
		$bloc_doc = afficher_documents_colonne($id_banniere, 'banniere');
	} else {
		$bloc_doc = afficher_documents_colonne(0-$GLOBALS['visiteur_session']['id_auteur'], 'banniere');
	}

	// affiche le resultat obtenu
	$navigation = $bloc_doc . pipeline('affiche_milieu',array('args' => array('exec' => 'bannieres', 'id_banniere' => $id_banniere), 'data' => ''));

	return $navigation;
}

<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_ajouter_un_document_charger_dist($objet, $id_objet){
	$res = array(
		'editable' => ' ',
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'fichier'=>'',
		'titre'=>'',
		'descriptif'=>''
	);
	
	// si l'on vien juste de poster le formlaire et qu'il a ete valide
	// on veut pouvoir recommencer a poster 
	// on ne prend du coup pas les anciennes valeurs dans l'environnement
	// pour ne pas polluer le nouveau formulaire
	if (_request('ajouter_document_enregistre')){
		unset ($res['titre'], $res['descriptif']);
	}
	return $res;
}

function formulaires_ajouter_un_document_verifier_dist($objet, $id_objet){
	$erreurs = array();	
	
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
	if (!is_array($_FILES) or !isset($_FILES['fichier'])) 
		$erreurs['message_erreur'] = _T('ajaxform:aucun_fichier_recu');
	if ($_FILES['fichier']['error'] != 0)
		$erreurs['fichier'] = _T('ajaxform:mauvaise_reception');
	
	// gerer les erreurs (calculees dans ajouter_document, mais qui font
	// echo puis exit; pas top pour les utiliser...
	
	return $erreurs;
}

/*
 * La fonction de traitement pour l'instant ne prend pas en compte :
 * - les documents distants
 * - les autres modes de documents ('choix' par defaut)
 */
function formulaires_ajouter_un_document_traiter_dist($objet, $id_objet){
	$res = array('editable'=>' ');

	// parametres de ajouter_documents()
	$mode='choix';
	$id_document = ''; // parent des vignettes - inutile ici
	$actifs = array(); // seront ajoutes les fichiers actifs dans le tableau - inutile ici...
	
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];

	$arg = $_FILES['fichier'];
	// verifier l'extension du fichier en fonction de son type mime
	$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
	list($extension,$arg['name']) = fixer_extension_document($arg);
	$id = $ajouter_documents($arg['tmp_name'], $arg['name'], 
				objet_type($objet), $id_objet, $mode, $id_document, $actifs);
	
	if ($id) {
		// signaler que l'on vient de soumettre le formulaire
		// pour que charger ne remette pas les anciennes valeurs
		// puisqu'on propose sa reedition dans la foulee
		set_request('ajouter_document_enregistre',true);
		
		$res['id_document'] = $id;
		$res['message_ok'] = _T('ajaxform:document_ajoute');
		
		// inserer texte et descriptif
		$titre = _request('titre');
		$descriptif = _request('descriptif');
		if ($texte OR $descriptif) {
			$modifs = array('titre'=>$titre,'descriptif'=>$descriptif);
			include_spip('inc/modifier');
			revision_document($id, $modifs);
		}
	} else {
		$res['message_erreur'] = _T('ajaxform:erreur_ajout_document');
	}
	return $res;
}


?>

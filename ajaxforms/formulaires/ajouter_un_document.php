<?php

/**
 * Formulaire #AJOUTER_UN_DOCUMENT
 *
 * Ce formulaire permet de telecharger UN document (un zip ne sera pas decompresse).
 * - En dehors d'une boucle, le document est simplement ajoute a la table spip_document
 * - Dans une boucle, il sera ajoute et lie a l'objet de la boucle.
 * - il est possible de passer les parametres objet et id : #FORMULAIRE_AJOUTER_UN_DOCUMENT{article,1}
 *
 * Note 1) document ou image ?
 * 	Les documents sont ajoutes en mode 'choix', c'est a dire que c'est SPIP qui
 * 	place le document en type 'image' ou 'document' selon le profil du fichier envoye.
 * 	Par defaut, une image de plus de 400px sera consideree comme un document.
 *
 * Note 2) afficher les documents
 * 	Pour afficher une liste de documents qui s'actualise quand on ajoute un element,
 * 	il faut soit ne pas utiliser l'ajax, soit inserer la liste des documents
 * 	a la suite du formulaire en utilisant le pipeline charger des
 * 	formulaires CVT pour inserer ce contenu.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire
 *
 *
 * @param string $objet        Objet SPIP auquel sera lie le document (ex. article)
 * @param integer $id_objet    Identifiant de l'objet
 * @param string $mode         Voir la note 1) ci-dessus
 * @return Array
 */
function formulaires_ajouter_un_document_charger_dist($objet, $id_objet, $mode='choix'){
	$res = array(
		'editable' => ' ',
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'fichier'=>'',
		'titre'=>'',
		'descriptif'=>'',
		'mode'=>$mode
	);

	// si l'on vient juste de poster le formulaire et qu'il a ete valide
	// on veut pouvoir recommencer a poster 
	// on ne prend du coup pas les anciennes valeurs dans l'environnement
	// pour ne pas polluer le nouveau formulaire
	if (_request('ajouter_document_enregistre')){
		unset ($res['titre'], $res['descriptif']);
	}
	return $res;
}

/**
 * Verification de l'upload
 *
 * Il s'agit uniquement de signaler si une erreur d'upload a eu lieu
 * Pas de controle sur le type et les autorisations qui sont geres par ajouter_document
 *
 * @param string $objet
 * @param integer $id_objet
 * @param string $mode
 * @return Array
 */
function formulaires_ajouter_un_document_verifier_dist($objet, $id_objet, $mode='choix'){
	$erreurs = array();	
	
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
	if (!is_array($_FILES) or !isset($_FILES['fichier']) or $_FILES['fichier']['error'] == 4 ) 
		$erreurs['message_erreur'] = _T('ajaxform:aucun_fichier_recu');
	if ($_FILES['fichier']['error'] != 0)
		$erreurs['fichier'] = _T('ajaxform:mauvaise_reception');
	
	// gerer les erreurs (calculees dans ajouter_document, mais qui font
	// echo puis exit; pas top pour les utiliser...
	
	return $erreurs;
}

/**
 * Traitement de l'upload
 *
 * La fonction de traitement pour l'instant ne prend pas en compte :
 * - les documents distants
 * - les autres modes de documents ('choix' par defaut)
 *
 * @param string $objet
 * @param integer $id_objet
 * @param string $mode
 * @return Array
 */
function formulaires_ajouter_un_document_traiter_dist($objet, $id_objet, $mode='choix'){
	$res = array('editable'=>' ', 'message_ok'=>'');

	// parametres de ajouter_documents()
	$id_document = ''; // parent des vignettes - inutile ici
	$actifs = array(); // seront ajoutes les fichiers actifs dans le tableau - inutile ici...
	
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
	
	include_spip('inc/ajaxform_documents');
	$id = ajaxform_creer_document($_FILES['fichier'],$objet,$id_objet,$mode);
	
	if ($id) {
		// signaler que l'on vient de soumettre le formulaire
		// pour que charger ne remette pas les anciennes valeurs
		// puisqu'on propose sa reedition dans la foulee
		set_request('ajouter_document_enregistre',true);
		
		$res['id_document'] = $id;
		$res['message_ok'] = _T('ajaxform:document_ajoute');
		
		ajaxform_modifier_document($id, array('titre','descriptif'));
		
		include_spip('inc/invalideur');
		suivre_invalideur("$objet/$id_objet");
		
	} else {
		$res['message_erreur'] = _T('ajaxform:erreur_ajout_document');
	}
	return $res;
}


?>

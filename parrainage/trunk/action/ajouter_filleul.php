<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création d'un filleul
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_ajouter_filleul_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_parrain n'est pas un nombre correct, on prend l'auteur en cours
	if (!($id_parrain = intval($arg)) > 0) {
		include_spip('inc/session');
		$id_parrain = intval(session_get('id_auteur'));
	}
	
	$id_filleul = 0;
	
	// Ensuite on fait quelque chose seulement si on a bien id_parrain et au moins l'email
	if ($id_parrain > 0 AND $email = _request('email')){
		$id_filleul = ajouter_filleul($id_parrain, $email,_request('nom'));
	}

	return $id_filleul;
}

function ajouter_filleul($id_parrain,$email,$nom){
	// Est-ce qu'il y a un nom, sinon le login de l'email
	if (!$nom){
		$decoupe = explode('@', $email);
		$nom = $decoupe[0];
	}

	// Si l'email est déjà inscrit sur le site on récupère les infos
	if ($id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email = '.sql_quote($email))){
		$statut = 'deja_inscrit';
	}
	else{
		$statut = 'contact';
		$id_auteur = 0;
	}

	// On insère le filleul seulement si le mail n'existe pas déjà dans les contacts de ce parrain
	// Sinon du coup on renvoie l'id_filleul déjà existant
	if (!$id_filleul = intval(sql_getfetsel('id_filleul', 'spip_filleuls', array('email = '.sql_quote($email), 'id_parrain = '.$id_parrain))))
		$id_filleul = insert_filleul(array(
			'id_parrain' => $id_parrain,
			'email' => $email,
			'nom' => $nom,
			'statut' => $statut,
			'id_auteur' => $id_auteur,
			'code_invitation' => md5($nom.$email.rand())
		));

	return $id_filleul;
}

/**
 * Crée un nouveau filleul et retourne son ID
 *
 * @param array $champs 
 * 		Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_filleul
 * 		Identifiant numérique du filleul
 */
function insert_filleul($champs=array()) {
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_filleuls',
			),
			'data' => $champs
		)
	);
	
	// Insérer l'objet
	$id_filleul = sql_insertq("spip_filleuls", $champs);
	
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_filleuls',
			),
			'data' => $champs
		)
	);

	return $id_filleul;
}

?>

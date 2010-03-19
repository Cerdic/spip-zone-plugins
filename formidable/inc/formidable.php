<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Génère le nom du cookie qui sera utilisé par le plugin lors d'une réponse
 * par un visiteur non-identifié.
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @return string Retourne le nom du cookie
 */
function formidable_generer_nom_cookie($id_formulaire){
	return $GLOBALS['cookie_prefix'].'cookie_formidable_'.$id_formulaire;
}

/*
 * Vérifie si le visiteur a déjà répondu à un formulaire
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @return unknown_type Retourne un tableau contenant les id des réponses si elles existent, sinon false
 */
function formidable_verifier_reponse_formulaire($id_formulaire){
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	$cookie = $_COOKIE[formidable_generer_nom_cookie($id_formulaire)];
	
	if ($cookie)
		$where = '(cookie='.sql_quote($cookie).($id_auteur ? ' OR id_auteur='.intval($id_auteur).')' : ')');
	elseif ($id_auteur)
		$where = 'id_auteur='.intval($id_auteur);
	else
		return false;
	
	$reponses = sql_allfetsel(
		'id_formulaires_reponse',
		'spip_formulaires_reponses',
		array(
			array('=', 'id_formulaire', intval($id_formulaire)),
			array('=', 'statut', sql_quote('publie')),
			$where
		),
		'',
		'date'
	);
	
	if (is_array($reponses))
		return array_map('reset', $reponses);
	else
		return false;
}

?>

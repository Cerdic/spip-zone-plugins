<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Chargement du fichier pour les autorisations
function api_syntaxe_autoriser($flux) { return $flux; }

// API de syntaxe SPIP seulement pour les comptes utilisateurices
function autoriser_spipsyntaxe_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	include_spip('inc/session');
	
	if ($id_auteur = session_get('id_auteur') and $id_auteur > 0) {
		return true;
	}
	
	return false;
}
function autoriser_propre_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	return autoriser($faire, '_spip_syntaxe', $id, $qui, $options);
}
function autoriser_typo_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	return autoriser($faire, '_spip_syntaxe', $id, $qui, $options);
}
function autoriser_T_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	return autoriser($faire, '_spip_syntaxe', $id, $qui, $options);
}
function autoriser_T_ou_typo_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	return autoriser($faire, '_spip_syntaxe', $id, $qui, $options);
}

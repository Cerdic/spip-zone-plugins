<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Chargement du fichier pour les autorisations
function api_syntaxe_autoriser($flux) { return $flux; }

// API de syntaxe SPIP seulement pour les comptes utilisateurices
function autoriser_spip_syntaxe_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	include_spip('inc/session');
	
	if ($id_auteur = session_get('id_auteur') and $id_auteur > 0) {
		return true;
	}
	
	return false;
}

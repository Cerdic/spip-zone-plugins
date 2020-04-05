<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Juste l'appel du pipeline
function collectionjson_autoriser() {
}

// Pour les auteurs, on autorise par défaut, afin de pouvoir aussi s'inscrire
function autoriser_auteur_post_collection_dist($faire, $quoi, $id, $qui, $options) {
	return true;
}

// Pour tester les infos de connexion, tout le monde peut le faire aussi
function autoriser_credential_get_collection_dist($faire, $quoi, $id, $qui, $options) {
	return true;
}

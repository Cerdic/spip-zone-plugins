<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Pipeline
function http_autoriser($flux){
	return $flux;
}

// Voir l'index, contenant à priori les collections disponibles : tout le monde peut voir l'index par défaut
function autoriser_get_index_dist($faire, $quoi, $id, $qui, $options){
	return true;
}

// Voir une liste d'objet par HTTP : tout le monde a le droit de voir des listes
function autoriser_get_collection_dist($faire, $quoi, $id, $qui, $options){
	return true;
}

// Voir un objet par HTTP : on redirige vers la fonction pour voir l'objet
function autoriser_get_ressource_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('voir', $quoi, $id, $qui, $options);
}

// Ajouter un objet par HTTP : on redirige vers la création de l'objet
function autoriser_post_collection_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('creer', $quoi, $id, $qui, $options);
}

// Modifier un objet par HTTP : on redirige vers la modification
function autoriser_put_ressource_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('modifier', $quoi, $id, $qui, $options);
}

// Supprimer un objet par HTTP : soit il existe une autorisation de suppression soit sinon l'institution
function autoriser_delete_ressource_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('supprimer', $quoi, $id, $qui, $options) or autoriser('instituer', $quoi, $id, $qui, $options);
}


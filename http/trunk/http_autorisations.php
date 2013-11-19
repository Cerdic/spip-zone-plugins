<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function http_autoriser($flux){return $flux;}

// Voir une liste d'articles par HTTP : tout le monde a le droit de voir des listes d'articles
function autoriser_article_get_collection_dist($faire, $quoi, $id, $qui, $options){
	return true;
}
// Voir un article par HTTP : on redirige vers la fonction pour voir un article
function autoriser_article_get_ressource_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('voir', 'article', $id, $qui, $options);
}
// Ajouter un article par HTTP : on redirige vers la création d'article
function autoriser_article_post_collection_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('creer', 'article', $id, $qui, $options);
}
// Modifier un article par HTTP : on redirige vers la modification
function autoriser_article_put_ressource_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('modifier', 'article', $id, $qui, $options);
}
// Supprimer un article par HTTP : on redirige vers l'institution de l'article
function autoriser_article_delete_ressource_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('instituer', 'article', $id, $qui, $options);
}

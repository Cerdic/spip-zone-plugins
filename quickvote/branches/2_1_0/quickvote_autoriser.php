<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// pipeline
function quickvote_autoriser(){}

// ---------------------------
// autorisation des boutons 
// ---------------------------
// Qui peut voir la liste des quickvotes : tout le monde
function autoriser_quickvotes_bouton_dist($faire, $quoi, $id, $qui, $options){
	return true;
  //return autoriser('bouton', 'quickvote_edition');
}
function autoriser_quickvotes_edition_bouton_dist($faire, $quoi, $id, $qui, $options){
	return true;
}

// ---------------------------
// autorisation CRUD
// ---------------------------

// Qui peut créer un quickvote : ceux qui peuvent configurer le site
function autoriser_quickvote_creer_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer');
}
// Qui peut modifier un quickvote : ceux qui peuvent configurer le site
function autoriser_quickvote_modifier_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer');
}


?>
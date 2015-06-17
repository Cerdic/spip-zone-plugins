<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function chats_autoriser(){}

// declarations d'autorisations
function autoriser_chats_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'chats', $id, $qui, $opt);
}

function autoriser_chats_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

function autoriser_chat_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'chat', $id, $qui, $opt);
}

function autoriser_chat_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}
<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Appel du pipeline
function contacts_autoriser(){}

// Qui peut modifier une organisation : les admins et l'auteur lié s'il existe
function autoriser_organisation_modifier_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer')
		or (
			$id_auteur = sql_getfetsel('id_auteur', 'spip_organisations', 'id_organisation = '.intval($id))
			and $id_auteur > 0
			and $id_auteur == $qui['id_auteur']
		);
}

// Qui peut modifier un contact : les admins et l'auteur lié s'il existe
function autoriser_contact_modifier_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer')
		or (
			$id_auteur = sql_getfetsel('id_auteur', 'spip_contacts', 'id_contact = '.intval($id))
			and $id_auteur > 0
			and $id_auteur == $qui['id_auteur']
		);
}

?>

<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Pour le pipeline
function commandes_autoriser($flux){ return $flux; }

// Qui peut passer une commande : un client (auteur+contact)
function autoriser_commander_dist($faire, $quoi, $id, $qui, $options){
	if (
		$id_auteur = $qui['id_auteur'] > 0
		and $contact = sql_getfetsel('id_contact', 'spip_contacts_liens', 'objet = '.sql_quote('auteur').' and id_objet = '.sql_quote($id_auteur))
	)
		return true;
	else
		return false;
}

?>

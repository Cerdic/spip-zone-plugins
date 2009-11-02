<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function contact_header_prive($flux){
	$flux .= "\n<script type=\"text/javascript\" src=\"".find_in_path('javascript/contact_sortable.js', false)."\"></script>";
	return $flux;
}

function contact_ajouter_boutons($boutons_admin) {
	// On vérifie s'il faut enregistrer les contacts.
	if (lire_config('contact/sauvegarder_contacts')) {
		$boutons_admin['forum']->sousmenu['messages_contact'] = new Bouton(
			find_in_path('contact-24.png', 'images/', false),
			_T('contact:msg_messagerie'),
			generer_url_ecrire('contact_messages')
		);
	}

	return ($boutons_admin);
}
?>

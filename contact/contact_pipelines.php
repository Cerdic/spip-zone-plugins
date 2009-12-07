<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function contact_header_prive($flux){
	$flux .= "\n<script type=\"text/javascript\" src=\"".find_in_path('javascript/contact_sortable.js', false)."\"></script>";
	return $flux;
}

function contact_ajouter_boutons($boutons_admin) {
	// On vérifie s'il faut enregistrer les contacts.
	if (lire_config('contact/sauvegarder_contacts')) {
		$menu='auteurs';
		$icone='contact-24.png';
		if (isset($boutons_admin['outils_collaboratifs'])){
			$menu = "outils_collaboratifs";
			$icone = "contact-20.png";
			}
		$boutons_admin[$menu]->sousmenu['contact_messages'] = new Bouton(
			_DIR_PLUGIN_CONTACT.'images/'.$icone,
			_T('contact:msg_messagerie')
		);
	}

	return ($boutons_admin);
}
?>
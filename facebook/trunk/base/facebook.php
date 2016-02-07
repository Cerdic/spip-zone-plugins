<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function facebook_declarer_tables_objets_sql($tables) {

	// Ajouter un champs pour stocker les tockens des utilisateurs
	$tables['spip_auteurs']['field']['facebook_token'] = "text NOT NULL DEFAULT ''";

	return $tables;
}

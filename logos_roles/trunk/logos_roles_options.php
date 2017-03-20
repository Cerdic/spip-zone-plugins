<?php

// On donne des logos et logos de survol à tous les objets éditoriaux.
if (! isset($GLOBALS['roles_logos']['logo'])) {
	$GLOBALS['roles_logos']['logo'] = array(
		'label' => 'Logo',
		'objets' => array_map('table_objet', array_keys(lister_tables_objets_sql())),
	);
}

if (! isset($GLOBALS['roles_logos']['logo_survol'])) {
	$GLOBALS['roles_logos']['logo_survol'] = array(
		'label' => 'Logo de survol',
		'objets' => array_map('table_objet', array_keys(lister_tables_objets_sql())),
	);
}

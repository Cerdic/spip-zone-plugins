<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Ajout des raccourcis dans la liste des wheels
$GLOBALS['spip_wheels']['raccourcis'][] = 'todo.yaml';

// Définition des statuts de tâche
$GLOBALS['todo_statuts'] = array(
	'+' => 'afaire',
	'o' => 'encours',
	'-' => 'termine',
	'x' => 'abandonne',
	'=' => 'arrete',
	'!' => 'alerte',
	'?' => 'inconnu'
);

?>

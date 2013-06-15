<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Ajout des raccourcis dans la liste des wheels
$GLOBALS['spip_wheels']['raccourcis'][] = 'todo.yaml';

// Définition des statuts de tâche
$GLOBALS['todo_statuts'] = array(
	'+' => array(
			'id' => 'afaire',
			'final' => false,
			'alerte' => ''),
	'o' => array(
			'id' => 'encours',
			'final' => false,
			'alerte' => ''),
	'-' => array(
			'id' => 'termine',
			'final' => true,
			'alerte' => ''),
	'x' => array(
			'id' => 'abandonne',
			'final' => true,
			'alerte' => ''),
	'=' => array(
			'id' => 'arrete',
			'final' => false,
			'alerte' => 'mineure'),
	'!' => array(
			'id' => 'alerte',
			'final' => false,
			'alerte' => 'majeure'),
	'?' => array(
			'id' => 'inconnu',
			'final' => false,
			'alerte' => 'majeure')
);

?>

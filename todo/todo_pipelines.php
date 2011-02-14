<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function todo_insert_head_css($flux){
	$flux.= '<link rel="stylesheet" href="'.find_in_path('css/todo.css').'" />';
	return $flux;
}

function todo_header_prive($flux){
	$flux.= '<link rel="stylesheet" href="'.find_in_path('css/todo.css').'" />';
	return $flux;
}

?>

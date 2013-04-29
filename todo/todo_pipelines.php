<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function todo_insert_head_css($flux){
	$flux.= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/todo.css').'" />';
	return $flux;
}

function todo_header_prive($flux){
	$flux.= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/todo.css').'" />';
	return $flux;
}

function todo_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans la barre d'édition seulement
	foreach (array('edition') as $nom) {
		$barre = &$barres[$nom];
		$barre->ajouterPlusieursApres('grpCaracteres', array(
			array(
				"id" => "sepTodo",
				"separator" => "---------------",
				"display"   => true,
			),
			array(
				"id"          => 'todo',
				"name"        => _T('todo:outil_inserer_todo'),
				"className"   => 'outil_todo', 
				"openWith" => "<todo>\n+ ",
				"closeWith" => "\n</todo>\n",
				"display"     => true,
				"dropMenu" => array(
					// bouton +
					array(
						"id"          => 'todo_plus',
						"name"        => _T('todo:outil_inserer_todo_plus'),
						"className"   => 'outil_todo_plus', 
						"openWith" => "+ ",
						"closeWith" => "\n",
						"display"     => true,
					),
					// bouton -
					array(
						"id"          => 'todo_moins',
						"name"        => _T('todo:outil_inserer_todo_moins'),
						"className"   => 'outil_todo_moins', 
						"openWith" => "- ",
						"closeWith" => "\n",
						"display"     => true,
					),
					// bouton o
					array(
						"id"          => 'todo_o',
						"name"        => _T('todo:outil_inserer_todo_o'),
						"className"   => 'outil_todo_o', 
						"openWith" => "o ",
						"closeWith" => "\n",
						"display"     => true,
					)
				)
			)
		));
	}
	return $barres;
}


function todo_porte_plume_barre_charger($barres){
	if (isset($barres['edition'])) {
		$barres['edition']->afficher(array('todo'));
	}
	return $barres;
}


function todo_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_todo'=>'todo.png',
		'outil_todo_plus'=>'todo-afaire-16.png',
		'outil_todo_moins'=>'todo-termine-16.png',
		'outil_todo_o'=>'todo-encours-16.png'
	));
}
?>

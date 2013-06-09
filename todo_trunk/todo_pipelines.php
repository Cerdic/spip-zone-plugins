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
				"openBlockWith" => "<todo>\n",
				"closeBlockWith" => "\n</todo>",
				"replaceWith" => "function(h){ return outil_todo(h, '+',true);}",
				"selectionType" => "line",
				"display"     => true,
				"dropMenu" => array(
					// bouton +
					array(
						"id"          => 'todo_plus',
						"name"        => _T('todo:outil_inserer_todo_plus'),
						"className"   => 'outil_todo_plus',
						"replaceWith" => "function(h){ return outil_todo(h, '+');}",
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
					// bouton -
					array(
						"id"          => 'todo_moins',
						"name"        => _T('todo:outil_inserer_todo_moins'),
						"className"   => 'outil_todo_moins', 
						"replaceWith" => "function(h){ return outil_todo(h, '-');}", 
						"selectionType" => "line",
						"forceMultiline" => true, 
						"display"     => true,
					),
					// bouton o
					array(
						"id"          => 'todo_o',
						"name"        => _T('todo:outil_inserer_todo_o'),
						"replaceWith" => "function(h){ return outil_todo(h, 'o');}", 
						"className"   => 'outil_todo_o', 
						"selectionType" => "line",
						"forceMultiline" => true, 
						"display"     => true,
					),
					// bouton x
					array(
						"id"          => 'todo_x',
						"name"        => _T('todo:outil_inserer_todo_x'),
						"replaceWith" => "function(h){ return outil_todo(h, 'x');}",
						"className"   => 'outil_todo_x',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
					// bouton -
					array(
						"id"          => 'todo_egal',
						"name"        => _T('todo:outil_inserer_todo_egal'),
						"replaceWith" => "function(h){ return outil_todo(h, '=');}",
						"className"   => 'outil_todo_egal',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
					// bouton !
					array(
						"id"          => 'todo_exclamation',
						"name"        => _T('todo:outil_inserer_todo_exclamation'),
						"replaceWith" => "function(h){ return outil_todo(h, '!');}",
						"className"   => 'outil_todo_exclamation',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					),
					// bouton ?
					array(
						"id"          => 'todo_interrogation',
						"name"        => _T('todo:outil_inserer_todo_interrogation'),
						"replaceWith" => "function(h){ return outil_todo(h, '?');}",
						"className"   => 'outil_todo_interrogation',
						"selectionType" => "line",
						"forceMultiline" => true,
						"display"     => true,
					)
				)
			)
		));
		$barre->ajouterFonction("function outil_todo(h, c,recursif) {
					if(recursif){
						// Cas de la sélection de click sur le bouton de création de todo complète
						s = h.selection;
						lines = h.selection.split(/\\r?\\n/);
						var lines_final = [];
						for (j = 0, n = lines.length, i = 0; i < n; i++) {
							// si une seule ligne, on se fiche de savoir qu'elle est vide,
							// c'est volontaire si on clique le bouton
							if (n == 1 || $.trim(lines[i]) !== '') {
								if(r = lines[i].match(/^([+-o]) (.*)$/)){
									r[1] = r[1].replace(/[+-o]/g, c);
									lines_final[j] = r[1]+' '+r[2];
									j++;
								} else {
									lines_final[j] = c + ' '+lines[i];
									j++;
								}
							}
						}
						return lines_final.join('\\n');
					}
					// Click sur les autres boutons
					if ((s = h.selection) && (r = s.match(/^([+-o]) (.*)$/))){
						r[1] = r[1].replace(/[+-o]/g, c);
						s = r[1]+' '+r[2];
					} else {
						s = c + ' '+s;
					}
					return s;
				}");
	}
	return $barres;
}

function todo_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_todo'=>'todo.png',
		'outil_todo_plus'=>'todo-afaire-16.png',
		'outil_todo_moins'=>'todo-termine-16.png',
		'outil_todo_o'=>'todo-encours-16.png',
		'outil_todo_x'=>'todo-abandonne-16.png',
		'outil_todo_egal'=>'todo-arrete-16.png',
		'outil_todo_exclamation'=>'todo-alerte-16.png',
		'outil_todo_interrogation'=>'todo-inconnu-16.png'
	));
}
?>

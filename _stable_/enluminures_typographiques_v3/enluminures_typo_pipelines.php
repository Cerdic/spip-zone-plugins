<?php
function typoenluminee_porte_plume_barre_pre_charger($barres){
	$barre = &$barres['edition'];
	
	$barre->cacher('header1');
	$barre->cacher('stroke_through');
	
	$barre->ajouterApres('header1', array(
		// groupe code et bouton <code>
		"id"          => 'grpavances',
		"name"        => _T('barre_intertitre'),
		"className"   => 'outil_intertitre1',
		"openWith" => "\n{{{",
		"closeWith" => "}}}\n",
		"display"     => true,
		"selectionType" => "line",
		"dropMenu"    => array(
			// bouton <cadre>
			array(
				"id"          => 'intertitre',
				"name"        => _T('barre_intertitre'),
				"className"   => 'outil_intertitre1', 
				"openWith" => "\n{{{",
				"closeWith" => "}}}\n",
				"display"     => true,
				"selectionType" => "line",
			),
			array(
				"id"          => 'intertitre2',
				"name"        => _T('enlumtypo:barre_intertitre2'),
				"className"   => 'outil_intertitre2', 
				"openWith" => "\n{2{",
				"closeWith" => "}2}\n",
				"display"     => true,
				"selectionType" => "line",
			),
			array(
				"id"          => 'intertitre3',
				"name"        => _T('enlumtypo:barre_intertitre3'),
				"className"   => 'outil_intertitre3', 
				"openWith" => "\n{3{",
				"closeWith" => "}4}\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// montrer une suppression
			array(
				"id"        => 'stroke_through_et',
				"name"      => _T('enlumtypo:barre_barre'), // :-)
				"className" => "outil_stroke_through_et", 
				"openWith" => "<del>", 
				"closeWith" => "</del>",
				"display"   => true,
				"selectionType" => "word",
			),
			// Mise en évidence (gras + couleur)
			array(
				"id"          => 'miseenevidence',
				"name"        => _T('enlumtypo:barre_miseenevidence'),
				"className"   => "outil_miseenevidence",
				"openWith"    => "[*", 
				"closeWith"   => "*]",
				"display"     => true,
				"selectionType" => "word",
			),
			// Mise en évidence2 (gras + autre couleur)
			array(
				"id"          => 'miseenevidence2',
				"name"        => _T('enlumtypo:barre_miseenevidence2'),
				"className"   => "outil_miseenevidence2",
				"openWith"    => "[**", 
				"closeWith"   => "*]",
				"display"     => true,
				"selectionType" => "word",
			),
			// Mise en exposant
			array(
				"id"          => 'exposant',
				"name"        => _T('enlumtypo:barre_exposant'),
				"className"   => "outil_exposant",
				"openWith"    => "<sup>", 
				"closeWith"   => "</sup>",
				"display"     => true,
				"selectionType" => "word",
			),
			// Mise en indice
			array(
				"id"          => 'indice',
				"name"        => _T('enlumtypo:barre_indice'),
				"className"   => "outil_indice",
				"openWith"    => "<sub>", 
				"closeWith"   => "</sub>",
				"display"     => true,
				"selectionType" => "word",
			),
			array(
				"id"          => 'alignerdroite',
				"name"        => _T('enlumtypo:barre_alignerdroite'),
				"className"   => 'outil_alignerdroite', 
				"openWith" => "\n[/",
				"closeWith" => "/]\n",
				"display"     => true,
				"selectionType" => "line",
			),
			array(
				"id"          => 'cadretexte',
				"name"        => _T('enlumtypo:barre_encadrer'),
				"className"   => 'outil_cadretexte', 
				"openWith" => "\n[(",
				"closeWith" => ")]\n",
				"display"     => true,
				"selectionType" => "line",
			),
		),
	));
	
	
	
	
	// Petites capitales
	$barre->ajouterApres('italic', array(
		"id"          => 'petitescapitales',
		"name"        => _T('enlumtypo:barre_petitescapitales'),
		"className"   => "outil_petitescapitales",
		"openWith"    => "<sc>", 
		"closeWith"   => "</sc>",
		"display"     => true,
		"selectionType" => "word",
	));
	// E majsucule accent grave
	$barre->ajouterApres('E_aigu', array(
		"id"          => 'E_grave',
		"name"        => _T('enlumtypo:barre_e_accent_grave'),
		"className"   => "outil_e_maj_grave",
		"replaceWith" => "&Egrave;",
		"display"     => true,
		"lang"        => array('fr','eo','cpf'),
	));
	// e dans le a
	$barre->ajouterApres('E_grave', array(
		"id"          => 'aelig',
		"name"        => _T('enlumtypo:barre_ea'),
		"className"   => "outil_aelig",
		"replaceWith" => "&aelig;",
		"display"     => true,
		"lang"        => array('fr','eo','cpf'),
	));
	// e dans le a majuscule
	$barre->ajouterApres('aelig', array(
		"id"          => 'AElig',
		"name"        => _T('enlumtypo:barre_ea_maj'),
		"className"   => "outil_aelig_maj",
		"replaceWith" => "&AElig;",
		"display"     => true,
		"lang"        => array('fr','eo','cpf'),
	));
	// c cedille majuscule
	$barre->ajouterApres('OE', array(
		"id"          => 'Ccedil',
		"name"        => _T('enlumtypo:barre_c_cedille_maj'),
		"className"   => "outil_ccedil_maj",
		"replaceWith" => "&Ccedil;",
		"display"     => true,
		"lang"        => array('fr','eo','cpf'),
	));
	// c cedille majuscule
	$barre->ajouterApres('Ccedil', array(
		"id"          => 'euro',
		"name"        => _T('enlumtypo:barre_euro'),
		"className"   => "outil_euro",
		"replaceWith" => "&euro;",
		"display"     => true,
		"lang"        => array('fr','eo','cpf'),
	));
	
	// Transformation en majuscule
	$barre->ajouterApres('euro', array(
		"id"          => 'uppercase',
		"name"        => _T('enlumtypo:barre_gestion_cr_changercassemajuscules'),
		"className"   => "outil_uppercase",
		"replaceWith" => 'function(markitup) { return markitup.selection.toUpperCase() }',
		"display"     => true,
	));
	// Transformation en minuscule
	$barre->ajouterApres('uppercase', array(
		"id"          => 'lowercase',
		"name"        => _T('enlumtypo:barre_gestion_cr_changercasseminuscules'),
		"className"   => "outil_lowercase",
		"replaceWith" => 'function(markitup) { return markitup.selection.toLowerCase() }',
		"display"     => true,
	));
	
	return $barres;
}

function typoenluminee_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_intertitre1' => 'intertitre.png',
		'outil_intertitre2' => 'intertitre2.png',
		'outil_intertitre3' => 'intertitre3.png',
		'outil_alignerdroite' => 'right.png',
		'outil_stroke_through_et' => 'text_strikethrough.png',
		'outil_cadretexte' => 'cadretexte.png',

		'outil_miseenevidence' => 'miseenevidence.png',
		'outil_miseenevidence2' => 'miseenevidence2.png',
		'outil_exposant' => 'exposant.png',
		'outil_indice' => 'indice.png',
		'outil_petitescapitales' => 'petitescapitales.png',
		
		'outil_e_maj_grave' => 'eagrave-maj.png',
		'outil_aelig' => 'aelig.png',
		'outil_aelig_maj' => 'aelig-maj.png',
		'outil_ccedil_maj' => 'ccedil-maj.png',
		'outil_euro' => 'euro.png',
		'outil_uppercase' => 'text_uppercase.png',
		'outil_lowercase' => 'text_lowercase.png',
	));
}

?>
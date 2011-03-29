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
				"openWith" => "\n{{{**",
				"closeWith" => "}}}\n",
				"display"     => true,
				"selectionType" => "line",
			),
			array(
				"id"          => 'intertitre3',
				"name"        => _T('enlumtypo:barre_intertitre3'),
				"className"   => 'outil_intertitre3', 
				"openWith" => "\n{{{***",
				"closeWith" => "}}}\n",
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
				"id"          => 'alignergauche',
				"name"        => _T('enlumtypo:barre_alignergauche'),
				"className"   => 'outil_alignergauche', 
				"openWith" => "\n[!",
				"closeWith" => "!]\n",
				"display"     => true,
				"lang"        => array('ar','fa'),
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
	
	// Appel Tableau
	$barre->ajouterApres('notes', array(
		"id"          => 'barre_tableau',
		"name"        => _T('enlumtypo:barre_tableau'),
		"className"   => "outil_barre_tableau",
		"replaceWith" => 'function(markitup) { zone_selection = markitup.textarea; window.open("?exec=tableau_edit", "","scrollbars=yes,resizable=yes,width=700,height=600") }',
		"display"     => true,
		"selectionType" => "line",
	));
	
	$barre->ajouterApres('liste_ul', array(
		// groupe code et bouton <code>
		"id"          => 'speciaux',
		"name"        => _T('enlumtypo:barre_formatages_speciaux'),
		"className"   => 'outil_speciaux',
		"openWith" => "",
		"closeWith" => "",
		"display"     => true,
		"selectionType" => "",
		"dropMenu"    => array(
			// code spip
			array(
				"id"          => 'barre_cadre',
				"name"        => _T('barre_outils:barre_cadre'),
				"className"   => "outil_barre_cadre", 
				"openWith"    => "\n&lt;cadre&gt;", 
				"closeWith"   => "&lt;/cadre&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// cadre spip
			array(
				"id"          => 'barre_code',
				"name"        => _T('barre_outils:barre_code'),
				"className"   => "outil_barre_code", 
				"openWith"    => "&lt;code&gt;", 
				"closeWith"   => "&lt;/code&gt;",
				"display"     => true,
				"selectionType" => "word",
			),
			// poesie spip
			array(
				"id"          => 'barre_poesie',
				"name"        => _T('barre_outils:barre_poesie'),
				"className"   => "outil_barre_poesie", 
				"openWith"    => "\n&lt;poesie&gt;", 
				"closeWith"   => "&lt;/poesie&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// Gestion de la <boitedepliante>
			array(
				"id"          => 'boite_depliante',
				"name"        => _T('barre_outils:boite_depliante'),
				"className"   => "outil_boite_depliante", 
				"openWith"    => "\n&lt;boitedepliante&gt;", 
				"closeWith"   => "&lt;/boitedepliante&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// Gestion de la <boitejaune>
			array(
				"id"          => 'boite_jaune',
				"name"        => _T('barre_outils:boite_jaune'),
				"className"   => "outil_boite_jaune", 
				"openWith"    => "\n&lt;boitejaune&gt;", 
				"closeWith"   => "&lt;/boitejaune&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// Gestion de la <bonasavoir>
			array(
				"id"          => 'bonasavoir',
				"name"        => _T('barre_outils:bonasavoir'),
				"className"   => "outil_bonasavoir", 
				"openWith"    => "\n&lt;bonasavoir&gt;", 
				"closeWith"   => "&lt;/bonasavoir&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// Gestion de la <attention>
			array(
				"id"          => 'attention',
				"name"        => _T('barre_outils:attention'),
				"className"   => "outil_attention", 
				"openWith"    => "\n&lt;attention&gt;", 
				"closeWith"   => "&lt;/attention&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// Gestion de la <exemple>
			array(
				"id"          => 'exemple',
				"name"        => _T('barre_outils:exemple'),
				"className"   => "outil_exemple", 
				"openWith"    => "\n&lt;exemple&gt;", 
				"closeWith"   => "&lt;/exemple&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
			// Gestion de la <lettrine>
			array(
				"id"          => 'lettrine',
				"name"        => _T('barre_outils:lettrine'),
				"className"   => "outil_lettrine", 
				"openWith"    => "\n&lt;lettrine&gt;", 
				"closeWith"   => "&lt;/lettrine&gt;\n",
				"display"     => true,
				"selectionType" => "word",
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
	
	return $barres;
}

function typoenluminee_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_intertitre1' => 'intertitre.png',
		'outil_intertitre2' => 'intertitre2.png',
		'outil_intertitre3' => 'intertitre3.png',
		'outil_alignerdroite' => 'right.png',
		'outil_alignergauche' => 'left.png',
		'outil_stroke_through_et' => 'text_strikethrough.png',
		'outil_cadretexte' => 'cadretexte.png',
		'outil_speciaux' => 'tag.png',
		'outil_barre_code' => 'page_white_code_red.png',
		'outil_barre_cadre' => 'page_white_code.png',
		'outil_barre_poesie' => 'poesie.png',

		'outil_boite_depliante' => 'note.png',
		'outil_boite_jaune' => 'note.png',
		'outil_bonasavoir' => 'bonasavoir-a.png',
		'outil_attention' => 'attention-a.png',
		'outil_exemple' => 'note.png',
		'outil_lettrine' => 'lettrine.png',

		'outil_miseenevidence' => 'miseenevidence.png',
		'outil_miseenevidence2' => 'miseenevidence2.png',
		'outil_exposant' => 'exposant.png',
		'outil_indice' => 'indice.png',
		'outil_petitescapitales' => 'petitescapitales.png',
		
		'outil_barre_tableau' => 'barre-tableau.png',
	));
}

?>

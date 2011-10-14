<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function typoenluminee_porte_plume_barre_pre_charger($barres){
	$barre = &$barres['edition'];
	
	$barre->cacher('stroke_through');

	$module_barre = "barre_outils";
	if (intval($GLOBALS['spip_version_branche'])>2)
		$module_barre = "barreoutils";
	
	$barre->set('header1', array(
		// groupe formatage paragraphe
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
			// code spip
			array(
				"id"          => 'barre_cadre',
				"name"        => _T($module_barre.':barre_cadre'),
				"className"   => "outil_barre_cadre", 
				"openWith"    => "\n&lt;cadre&gt;", 
				"closeWith"   => "&lt;/cadre&gt;\n",
				"display"     => true,
				"selectionType" => "line",
			),
		),
	));
	
	$barre->set('bold', array(
		// groupe formatage texte
		"dropMenu"    => array(
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
			// cadre spip
			array(
				"id"          => 'barre_code',
				"name"        => _T($module_barre.':barre_code'),
				"className"   => "outil_barre_code", 
				"openWith"    => "&lt;code&gt;", 
				"closeWith"   => "&lt;/code&gt;",
				"display"     => true,
				"selectionType" => "word",
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
	
	$barre->set('quote', array(
		"dropMenu"    => array(
			// poesie spip
			array(
				"id"          => 'barre_poesie',
				"name"        => _T($module_barre.':barre_poesie'),
				"className"   => "outil_barre_poesie", 
				"openWith"    => "\n&lt;poesie&gt;", 
				"closeWith"   => "&lt;/poesie&gt;\n",
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
	
	return $barres;
}

function typoenluminee_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_intertitre1' => array('spt-v1.png','-10px -226px'), //'intertitre.png'
		'outil_intertitre2' => array('intertitre2.png','0'),
		'outil_intertitre3' => array('intertitre3.png','0'),
		'outil_alignerdroite' => array('right.png','0'),
		'outil_alignergauche' => array('left.png','0'),
		'outil_stroke_through_et' => array('spt-v1.png','-10px -946px'), //'text_strikethrough.png'
		'outil_cadretexte' => array('cadretexte.png','0'),
		'outil_speciaux' => array('tag.png','0'),
		'outil_barre_code' => array('page_white_code_red.png','0'),
		'outil_barre_cadre' => array('page_white_code.png','0'),

		'outil_miseenevidence' => array('miseenevidence.png','0'),
		'outil_miseenevidence2' => array('miseenevidence2.png','0'),
		'outil_exposant' => array('exposant.png','0'),
		'outil_indice' => array('indice.png','0'),
		'outil_petitescapitales' => array('petitescapitales.png','0'),
		'outil_barre_poesie' => array('poesie.png','0'),
		
		'outil_barre_tableau' => array('barre-tableau.png','0'),
	));
}

?>

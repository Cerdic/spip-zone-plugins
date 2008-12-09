<?php
/*
 * Plugin Porte Plume pour SPIP 2
 * Licence GPL
 * Auteur Matthieu Marcillaud
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Definition de la barre 'spip' pour markitup
 */
function barre_outils_spip(){
	include_spip('inc/barre_outils');
	$set = new Barre_outils(array(
		'nameSpace'         => 'spip',
		'previewAutoRefresh'=> true,
		'previewParserPath' => url_absolue(generer_url_public('preview')),
		'onShiftEnter'      => array('keepDefault'=>false, 'replaceWith'=>"\n_ "),
		'onCtrlEnter'       => array('keepDefault'=>false, 'replaceWith'=>"\n\n"),
		// garder les listes si on appuie sur entree
		'onEnter'           => array('keepDefault'=>false, 'selectionType'=>'return', 'replaceWith'=>"\n"),
		'onTab'             => array('keepDefault'=>false, 'replaceWith'=>"\t"),
		'markupSet'         => array(
			// H1 - {{{
			array(
				"id"        => 'header1', 
				"name"      => _T('barre_outils:barre_intertitre'), 
				"key"       => "H", 
				"className" => "outil_header1", 
				"openWith" => "\n\n{{{", 
				"closeWith" => "}}}\n",
				"display"   => true,
				"selectionType" => "line",
			),
			// Bold - {{
			array(
				"id"        => 'bold',
				"name"      => _T('barre_outils:barre_gras'), 
				"key"       => "B", 
				"className" => "outil_bold", 
				"openWith" => "{{", 
				"closeWith" => "}}",
				"display"   => true,
				"selectionType" => "word",
			),
			// Italic - {
			array(
				"id"        => 'italic',
				"name"      => _T('barre_outils:barre_italic'), 
				"key"       => "I", 
				"className" => "outil_italic", 
				"openWith" => "{", 
				"closeWith" => "}",
				"display"   => true,
				"selectionType" => "word",
			),
				
			// montrer une suppression
			array(
				"id"        => 'stroke_through',
				"name"      => _T('barre_outils:barre_barre'), // :-)
				"key"       => "S", 
				"className" => "outil_stroke_through", 
				"openWith" => "<del>", 
				"closeWith" => "</del>",
				"display"   => true,
				"selectionType" => "word",
			),
			
			// listes -*
			array(
				"id"        => 'liste_ul',
				"name"      => _T('barre_outils:barre_liste_ul'), 
				"className" => "outil_liste_ul", 
				"replaceWith" => "function(h){ return outil_liste(h, '*');}", 
				"display"   => true,
				"selectionType" => "line",
				"forceMultiline" => true,
			),	
			// liste -#		
			array(
				"id"        => 'liste_ol',
				"name"      => _T('barre_outils:barre_liste_ol'), 
				"className" => "outil_liste_ol", 
				"replaceWith" => "function(h){ return outil_liste(h, '#');}", 
				"display"   => true,
				"selectionType" => "line",
				"forceMultiline" => true,
			),
			// indenter		
			array(
				"id"        => 'indenter',
				"name"      => _T('barre_outils:barre_indenter'), 
				"className" => "outil_indenter", 
				"replaceWith" => "function(h){return outil_indenter(h);}",  
				"display"   => true,
				"selectionType" => "line",
				"forceMultiline" => true,
			),
			// desindenter	
			array(
				"id"        => 'desindenter',
				"name"      => _T('barre_outils:barre_desindenter'), 
				"className" => "outil_desindenter", 
				"replaceWith" => "function(h){return outil_desindenter(h);}", 
				"display"   => true,
				"selectionType" => "line",
				"forceMultiline" => true,
			),
						
			
			// separation
			array(
				"id" => "sepLink", // trouver un nom correct !
				"separator" => "---------------",
				"display"   => true,
			),
			// lien spip
			array(
				"id"          => 'link',
				"name"        => _T('barre_outils:barre_lien'),
				"key"         => "L", 
				"className"   => "outil_link", 
				"openWith"    => "[", 
				"closeWith"   => "->[!["._T('barre_outils:barre_lien_input')."]!]]",
				"display"     => true,
			),
			// note en bas de page spip
			array(
				"id"          => 'notes',
				"name"        => _T('barre_outils:barre_note'),
				"className"   => "outil_notes", 
				"openWith"    => "[[", 
				"closeWith"   => "]]",
				"display"     => true,
				"selectionType" => "word",
			),
					
			// separation
			// (affichee dans forum)
			array(
				"id" => "sepCitations", // trouver un nom correct !
				"separator" => "---------------",
				"display"   => false,
			),
			// quote spip
			// (affichee dans forum)
			array(
				"id"          => 'quote',
				"name"        => _T('barre_outils:barre_quote'),
				"key"         => "Q", 
				"className"   => "outil_quote", 
				"openWith"    => "\n<quote>", 
				"closeWith"   => "</quote>\n",
				"display"     => false,
				"selectionType" => "word",
			),
			
			// separation
			array(
				"id" => "sepGuillemets",
				"separator" => "---------------",
				"display"   => true,
			),
			// guillemets
			array(
				"id"          => 'guillemets',
				"name"        => _T('barre_outils:barre_guillemets'),
				"className"   => "outil_guillemets", 
				"openWith"    => "&laquo;", 
				"closeWith"   => "&raquo;",
				"display"     => true,
				"lang"        => array('fr','eo','cpf','ar','es'),
				"selectionType" => "word",
			),
			// guillemets internes
			array(
				"id"          => 'guillemets_simples',
				"name"        => _T('barre_outils:barre_guillemets_simples'),
				"className"   => "outil_guillemets_simples", 
				"openWith"    => "&ldquo;", 
				"closeWith"   => "&rdquo;",
				"display"     => true,
				"lang"        => array('fr','eo','cpf','ar','es'),
				"selectionType" => "word",
			),
			// guillemets de
			array(
				"id"          => 'guillemets_de',
				"name"        => _T('barre_outils:barre_guillemets'),
				"className"   => "outil_guillemets_de", 
				"openWith"    => "&bdquo;", 
				"closeWith"   => "&ldquo;",
				"display"     => true,
				"lang"        => array('bg','de','pl','hr','src'),
				"selectionType" => "word",
			),
			// guillemets de, simples
			array(
				"id"          => 'guillemets_de_simples',
				"name"        => _T('barre_outils:barre_guillemets_simples'),
				"className"   => "outil_guillemets_de_simples", 
				"openWith"    => "&sbquo;", 
				"closeWith"   => "&lsquo;",
				"display"     => true,
				"lang"        => array('bg','de','pl','hr','src'),
				"selectionType" => "word",
			),
			// guillemets autres langues
			array(
				"id"          => 'guillemets_autres',
				"name"        => _T('barre_outils:barre_guillemets'),
				"className"   => "outil_guillemets_simples", 
				"openWith"    => "&ldquo;", 
				"closeWith"   => "&rdquo;",
				"display"     => true,
				"lang_not"    => array('fr','eo','cpf','ar','es','bg','de','pl','hr','src'),
				"selectionType" => "word",
			),
			// guillemets simples, autres langues
			array(
				"id"          => 'guillemets_autres_simples',
				"name"        => _T('barre_outils:barre_guillemets_simples'),
				"className"   => "outil_guillemets_uniques", 
				"openWith"    => "&lsquo;", 
				"closeWith"   => "&rsquo;",
				"display"     => true,
				"lang_not"    => array('fr','eo','cpf','ar','es','bg','de','pl','hr','src'),
				"selectionType" => "word",
			),
			
			// separation
			array(
				"id" => "sepCaracteres",
				"separator" => "---------------",
				"display"   => true,
			),
			// icones clavier
			array(
				"id"          => 'grpCaracteres',
				"name"        => _T('barre_outils:barre_inserer_caracteres'),
				"className"   => 'outil_caracteres',
				"display"     => true,
				
				"dropMenu"    => array(
					// A majuscule accent grave
					array(
						"id"          => 'A_grave',
						"name"        => _T('barre_outils:barre_a_accent_grave'),
						"className"   => "outil_a_maj_grave", 
						"replaceWith"   => "&Agrave;",
						"display"     => true,
						"lang"    => array('fr','eo','cpf'),
					),
					// E majuscule accent aigu
					array(
						"id"          => 'E_aigu',
						"name"        => _T('barre_outils:barre_e_accent_aigu'),
						"className"   => "outil_e_maj_aigu", 
						"replaceWith"   => "&Eacute;",
						"display"     => true,
						"lang"    => array('fr','eo','cpf'),
					),
					// oe 
					array(
						"id"          => 'oe',
						"name"        => _T('barre_outils:barre_eo'),
						"className"   => "outil_oe", 
						"replaceWith"   => "&oelig;",
						"display"     => true,
						"lang"    => array('fr'),
					),
					// OE 
					array(
						"id"          => 'OE',
						"name"        => _T('barre_outils:barre_eo_maj'),
						"className"   => "outil_oe_maj", 
						"replaceWith"   => "&OElig;",
						"display"     => true,
						"lang"    => array('fr'),
					),
				),
			),

			
			// separation
			array(
				"id" => "sepLorem",
				"separator" => "---------------",
				"display"   => true,
			),
			// lorem ipsum
			array(
				"id"          => 'lorem_ipsum',
				"name"        => _T('barre_outils:barre_lorem_ipsum'),
				"className"   => "outil_lorem_ipsum", 
				"replaceWith" => "\n\nLorem ipsum dolor sit amet, consectetuer adipiscing elit." 
								. "Aenean ut orci vel massa suscipit pulvinar. Nulla sollicitudin. "
								. "Fusce varius, ligula non tempus aliquam, nunc turpis ullamcorper nibh, in "
								. "tempus sapien eros vitae ligula. Pellentesque rhoncus nunc et augue. "
								. "Integer id felis. Curabitur aliquet pellentesque diam. Integer quis metus "
								. "vitae elit lobortis egestas. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. "
								. "Morbi vel erat non mauris convallis vehicula. Nulla et sapien. Integer tortor tellus, "
								. "aliquam faucibus, convallis id, congue eu, quam. Mauris ullamcorper felis vitae erat. "
								. "Proin feugiat, augue non elementum posuere, metus purus iaculis lectus, "
								. "et tristique ligula justo vitae magna.\n\n",
				"display"     => true,
			),
			// lorem ipsum 3 paragraphes
			array(
				"id"          => 'lorem_ipsum_big',
				"name"        => _T('barre_outils:barre_lorem_ipsum_3'),
				"className"   => "outil_lorem_ipsum_big", 
				"replaceWith" => "\n\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. "
								. "Curabitur lacus mi, varius sit amet, suscipit in, hendrerit "
								. "sit amet, turpis. Duis in odio. Fusce mauris. Nulla quis ante. "
								. "Vestibulum id dui. Curabitur quis est ac quam euismod ullamcorper. "
								. "Phasellus nec justo. Vestibulum id erat sed odio ultrices hendrerit. "
								. "Duis fermentum, velit ut pretium fermentum, felis turpis rhoncus justo, "
								. "vel adipiscing nulla lectus sed eros. In hac habitasse platea dictumst. "
								. "In hac habitasse platea dictumst. Curabitur tellus velit, consequat nec, "
								. "tincidunt sit amet, posuere vel, ligula. Aenean auctor mollis mi. "
								. "In adipiscing dolor vel diam. Morbi justo. Maecenas eu risus id mi tincidunt "
								. "vestibulum.\n\n"
								. "Maecenas lacinia. Sed aliquet bibendum nisl. Vivamus vulputate, "
								. "sapien ut molestie iaculis, diam libero porttitor dolor, eget semper orci orci "
								. "ut sem. Nunc venenatis. Curabitur adipiscing, velit at iaculis dictum, "
								. "lacus nulla adipiscing mauris, id rhoncus velit nisl ac mauris. Aliquam "
								. "egestas, sapien sed placerat lacinia, tellus erat tempor quam, at sollicitudin "
								. "ligula eros sit amet sapien. Nam at dui id libero vehicula sodales. Vestibulum "
								. "dictum risus eget metus. Cras lorem. Pellentesque lobortis sodales ipsum. "
								. "Vivamus convallis lectus in nunc. Vivamus metus libero, ullamcorper in, "
								. "porttitor nec, dapibus id, est. Praesent pede. Sed viverra consequat leo. "
								. "Mauris pharetra tortor a orci.\n\n"
								. "Maecenas sed lacus. Phasellus iaculis risus et elit. Morbi sagittis nunc vitae "
								. "sem. Aliquam ac lorem vel magna ornare malesuada. Pellentesque habitant "
								. "morbi tristique senectus et netus et malesuada fames ac turpis egestas. "
								. "Mauris est dolor, aliquam eget, feugiat ut, tempus at, arcu. Duis porta, "
								. "pede sed hendrerit pellentesque, orci dolor consectetuer risus, id scelerisque "
								. "tellus ipsum quis felis. Sed ultrices. Nullam eleifend dui sodales massa. "
								. "Morbi consectetuer pellentesque dui. Vestibulum urna. Fusce congue velit ut "
								. "erat. Aliquam quis odio sollicitudin ipsum euismod porta. Vivamus pharetra, "
								. "lacus eu tempor lobortis, diam nisi vulputate lorem, ut aliquet dui neque eu "
								. "sem. Morbi varius, nisi ac laoreet mollis, pede odio cursus nisi, in imperdiet "
								. "dolor enim at metus. Nunc pretium pulvinar tortor. Vestibulum euismod ultrices "
								. "est. Etiam lobortis, enim ut bibendum dictum, urna orci lacinia tortor, "
								. "at eleifend pede sem eu sem. Morbi a neque. Vestibulum cursus.\n\n", 
				"display"     => true,
			),
							
			// separation
			array(
				"id" => "sepPreview", // trouver un nom correct !
				"separator" => "---------------",
				"display"   => true,
			),
			// clean
			array(
				"id"          => 'clean',
				"name"        => _T('barre_outils:barre_clean'), 
				"className"   => "outil_clean",
				"replaceWith" => 'function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") }',
				"display"     => true,
			),
			// preview
			array(
				"id"        => 'preview',
				"name"      => _T('barre_outils:barre_preview'), 
				"className" => "outil_preview",
				"call"      => "preview",
				"display"   => true,
			),

			
		),
		
	'functions'         => "
				// remplce ou cree -* ou -** ou -# ou -##
				function outil_liste(h, c) {
					if ((s = h.selection) && (r = s.match(/^-([*#]+) (.*)\$/)))	 {
						r[1] = r[1].replace(/[#*]/g, c);
						s = '-'+r[1]+' '+r[2];
					} else {
						s = '-' + c + ' '+s;
					}
					return s;
				}

				// indente des -* ou -#
				function outil_indenter(h) {
					if (s = h.selection) {
						if (s.substr(0,2)=='-*') {
							s = '-**' + s.substr(2);
						} else if (s.substr(0,2)=='-#') {
							s = '-##' + s.substr(2);
						} else {
							s = '-* ' + s;
						}
					}
					return s;
				}
						
				// desindente des -* ou -** ou -# ou -##
				function outil_desindenter(h){
					if (s = h.selection) {
						if (s.substr(0,3)=='-**') {
							s = '-*' + s.substr(3);
						} else if (s.substr(0,3)=='-* ') {
							s = s.substr(3);
						} else if (s.substr(0,3)=='-##') {
							s = '-#' + s.substr(3);
						} else if (s.substr(0,3)=='-# ') {
							s = s.substr(3);
						}
					}
					return s;
				}
				",
	));
	
	$set->cacher(array(
		'stroke_through',
		'sepLorem', 'lorem_ipsum', 'lorem_ipsum_big',
		'clean', 'preview',
	));
	
	return $set;
}


/**
 * Definition de la barre 'spip_forum' pour markitup
 */
function barre_outils_spip_forum(){
	$barre = barre_outils_spip();
	$barre->nameSpace = 'spip_forum';
	$barre->cacherTout();
	$barre->afficher(array(
		'bold','italic',
		'sepLink','link',
		'sepCitations', 'quote',
		'sepCaracteres','guillemets', 'guillemets_simples', 
		   'guillemets_de', 'guillemets_de_simples',
		   'guillemets_autres', 'guillemets_autres_simples',
		   'A_grave', 'E_aigu', 'oe', 'OE',
	));
	return $barre;
}


/**
 * Definitions des liens entre css et icones
 */
function barre_outils_icones(){
	return array(
		//'outil_header1' => 'text_heading_1.png',
		'outil_header1' => 'intertitre.png',
		'outil_bold' => 'text_bold.png',
		'outil_italic' => 'text_italic.png',
		
		'outil_stroke_through' => 'text_strikethrough.png',
		
		'outil_liste_ul' => 'text_list_bullets.png',
		'outil_liste_ol' => 'text_list_numbers.png',
		'outil_indenter' => 'text_indent.png',
		'outil_desindenter' => 'text_indent_remove.png',
		
		//'outil_quote' => 'text_indent.png',
		'outil_quote' => 'quote.png',
		
		//'outil_link' => 'world_link.png',
		'outil_link' => 'lien.png',
		'outil_notes' => 'notes.png',
		
		'outil_lorem_ipsum' => 'newspaper.png',
		'outil_lorem_ipsum_big' => 'newspaper_add.png',
		
		'outil_guillemets' => 'guillemets.png',
		'outil_guillemets_simples' => 'guillemets-simples.png',
		'outil_guillemets_de' => 'guillemets-de.png',
		'outil_guillemets_de_simples' => 'guillemets-uniques-de.png',
		'outil_guillemets_uniques' => 'guillemets-uniques.png',
		
		'outil_caracteres' => 'keyboard.png',
			'outil_a_maj_grave' => 'agrave-maj.png',
			'outil_e_maj_aigu' => 'eacute-maj.png',
			'outil_oe' => 'oelig.png',
			'outil_oe_maj' => 'oelig-maj.png',
		
		'outil_clean' => 'clean.png',
		'outil_preview' => 'eye.png',	
	);
}
?>

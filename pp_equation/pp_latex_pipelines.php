<?php

// Insertion dans le porte-plume

function pp_latex_porte_plume_barre_pre_charger($barres) {
	$barre = &$barres['edition'];
	/*
		per prima cosa aggiungo un separatore
		
		poi aggiungo dopo il separatore il bottone del mio gruppo
	*/

	$barre->ajouterApres('grpCaracteres', array(
					"id" => "sepLatex",
					"separator" => "---------------",
					"display"   => $visible,
		));
	$barre->ajouterApres('sepLatex', array(
		"id"          => 'grpEquation',
		"name"        => _T('pp_latex:barre_equations'),
		"className"   => 'outil_equation',
		"dropMenu"    => ma_barre(),
		"display"     => true
	 ));
	//	print_r($GLOBALS['meta']['adresse_site']);exit;
	return $barres;
}

// Icônes pour le porte-plume
//
function pp_latex_porte_plume_lien_classe_vers_icone($flux) {
	$icones = array(
		'outil_equation' => 'equation.png',
		'outil_math' => 'mathic.png',
		'outil_formula' => 'dollar.png',
		'outil_brackets' => 'brackets.png',
		'outil_squarebrackets' => 'squarebrackets.png',
		'outil_setbrackets' => 'setbrackets.png',
		'outil_cdot' => 'cdot.png',
		'outil_plusminus' => 'plusminus.png',
		'outil_frac' => 'frac.png',
		'outil_sqrt' => 'sqrt.png',
		'outil_system' => 'system.png',
		'outil_array' => 'array.png',
		'outil_pedex' => 'pedex.png',
		'outil_sum' => 'sum.png',
		'outil_prod' => 'prod.png',
		'outil_vector' => 'vector.png',
		'outil_rightarrow' => 'rightarrow.png',
		'outil_leftarrow' =>  'leftarrow.png',
		'outil_rrightarrow' => 'rarrow.png',
		'outil_leftrightarrow' => 'lrarrow.png',
		'outil_latexhelp' => 'aide-16.png',

	);
	//$icones['outil_equation'] = 'equation.png';

	return array_merge($flux, $icones);
}



// "selectionType" => "word" implica che dopo una parola, seleziona l'ultima parola
function ma_barre(){
return array(
                        // math - {{{
                        array(
                                "id"        => 'math',
                                "name"      => _T('pp_latex:barre_math'),
                                "key"       => "M",
                                "className" => "outil_math",
                                "openWith" => "<math>",
                                "closeWith" => "</math>",
                                "display"   => true,
                                "selectionType" => "line"
                        ), 
						// DOLLAR                       
						array(
                                "id"        => 'dollar',
                                "name"      => _T('pp_latex:barre_formula'),
                                "key"       => "4",
                                "className" => "outil_formula",
                                "openWith" => "$$",
                                "closeWith" => "$$",
                                "display"   => true,
                                "selectionType" => "word"
                        ),
						// brakets                       
						array(
                                "id"        => 'brackets',
                                "name"      => _T('pp_latex:barre_brackets'),
                                "key"       => "8",
                                "className" => "outil_brackets",
                                "openWith" => "\\left(",
                                "closeWith" => "\\right)",
                                "display"   => true,
                                //"selectionType" => "word"
                        ), 						
						// SQUAREbrakets                       
						array(
                                "id"        => 'squarebrackets',
                                "name"      => _T('pp_latex:barre_squarebrackets'),
                                //"key"       => "è",
                                "className" => "outil_squarebrackets",
                                "openWith" => "\\left[",
                                "closeWith" => "\\right]",
                                "display"   => true,
                                //"selectionType" => "word"
                        ), 						
						// SETbrakets                       
						array(
                                "id"        => 'setbrackets',
                                "name"      => _T('pp_latex:barre_setbrackets'),
                                "className" => "outil_setbrackets",
                                "openWith" => "\\left{",
                                "closeWith" => "\\right}",
                                "display"   => true,
                                //"selectionType" => "word"
                        ),                        
                      
						// fraction                      
						array(
                                "id"        => 'frac',
                                "name"      => _T('pp_latex:barre_frac'),
                                "key"       => "F",
                                "className" => "outil_frac",
                                "openWith" => "\\frac{}{}",
                                "display"   => true,
                        ),                        
						// SQRT                      
						array(
                                "id"        => 'sqrt',
                                "name"      => _T('pp_latex:barre_sqrt'),
                                //"key"       => "ò",
                                "className" => "outil_sqrt",
                                "openWith" => "\sqrt{",
                                "closeWith" => "}",
                                "display"   => true,
                                //"selectionType" => "word"
                        ),                           
						// cdot                      
						array(
                                "id"        => 'cdot',
                                "name"      => _T('pp_latex:barre_cdot'),
                                "key"       => "X",
                                "className" => "outil_cdot",
                                "openWith" => "\cdot",
                                "display"   => true
                        ),                        
						// PLUSMINUS                     
						array(
                                "id"        => 'pm',
                                "name"      => _T('pp_latex:barre_plusminus'),
                                //"key"       => "+",
                                "className" => "outil_plusminus",
                                "openWith" => "\pm",
                                "display"   => true
                        ), 
						// SYSTEM                       
						array(
                                "id"        => 'system',
                                "name"      => _T('pp_latex:barre_system'),
                                //"key"       => "S",
                                "className" => "outil_system",
                                "openWith" => "$\n\left\{\begin{array}{}\n",
                                "closeWith" => "ax+by=c\\\\\na_{1}x+b_{1}\ny=c_{1}\n\end{array}\n$",
                                "display"   => true,
                                "selectionType" => "line"
                        ),
						// EQUATION ARRAY                      
						array(
                                "id"        => 'array',
                                "name"      => _T('pp_latex:barre_array'),
                                //"key"       => "s",
                                "className" => "outil_array",
                                "openWith" => "$\n\begin{array}\left\n",
                                "closeWith" => "\n\n\\right\end{array}\n$",
                                "display"   => true,
                                "selectionType" => "line"
                        ),	
						// PEDEX                      
						array(
                                "id"        => 'pedex',
                                "name"      => _T('pp_latex:barre_pedex'),
                                "key"       => "P",
                                "className" => "outil_pedex",
                                "openWith" => "_{",
                                "closeWith" => "}",
                                "display"   => true,
                                
                        ), 
						// VECTOR                     
						array(
                                "id"        => 'vector',
                                "name"      => _T('pp_latex:barre_vector'),
                                //"key"       => "V",
                                "className" => "outil_vector",
                                "openWith" => "\\vec{",
                                "closeWith" => "}",
                                "display"   => true,
                                //"selectionType" => "word"
                        ),						
						// SUM                     
						array(
                                "id"        => 'sum',
                                "name"      => _T('pp_latex:barre_sum'),
                                //"key"       => "v",
                                "className" => "outil_sum",
                                "openWith" => "\\sum",
                                "display"   => true,
                                //"selectionType" => "word"
                        ), 						
						// PROD               
						array(
                                "id"        => 'prod',
                                "name"      => _T('pp_latex:barre_prod'),
                                "key"       => "P",
                                "className" => "outil_prod",
                                "openWith" => "\\prod",
                                "display"   => true,
                                //"selectionType" => "word"
                        ), 
						// RIGHTARROW                     
						array(
                                "id"        => 'rightarrow',
                                "name"      => _T('pp_latex:barre_rightarrow'),
                                //"key"       => "",
                                "className" => "outil_rightarrow",
                                "openWith" => "\\rightarrow",
                                "display"   => true
                        ), 
						// leftARROW                     
						array(
                                "id"        => 'leftarrow',
                                "name"      => _T('pp_latex:barre_leftarrow'),
                                //"key"       => "",
                                "className" => "outil_leftarrow",
                                "openWith" => "\\leftarrow",
                                "display"   => true
                        ),  	
												
						// leftrightarrow                     
						array(
                                "id"        => 'leftrightarrow',
                                "name"      => _T('pp_latex:barre_Leftrightarrow'),
                                //"key"       => "",
                                "className" => "outil_leftrightarrow",
                                "openWith" => "\Leftrightarrow",
                                "display"   => true
                        ), 
						// RightARROW                     
						array(
                                "id"        => 'rrightarrow',
                                "name"      => _T('pp_latex:barre_Rightarrow'),
                                //"key"       => "",
                                "className" => "outil_rrightarrow",
                                "openWith" => "\Rightarrow",
                                "display"   => true
                        ), 				
						// help                     
						array(
                                "id"        => 'latexhelp',
                                "name"      => _T('pp_latex:latex_reference'),
                                "className" => "outil_latexhelp",
                                "display"   => true,
								"beforeInsert" => "function(h) { window.open('".$GLOBALS['meta']['adresse_site']."/\?page=latex','LaTeX commands','menubar=no,scrollbars=yes,status=0,toolbar=no,location=0,left=300,width=760'); }",
								//"beforeInsert" => "function(h) { jQuery.modalboxload('".$GLOBALS['meta']['adresse_site']."/\?page=latex'); }",// popin code
								/* 
								original document code
								http://web.ift.uib.no/Fysisk/Teori/KURS/WRK/TeX/
								*/
								 
                        ),  
					);				
}
?>
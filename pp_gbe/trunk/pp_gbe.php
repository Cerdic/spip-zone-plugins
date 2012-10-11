<?php

/**
 * Definition de la barre 'edition' pour markitup (utilise par porte-plume)
 */
function pp_gbe_porte_plume_barre_pre_charger($barres) {
//	$barre = &$barres['edition']; // insertion dans la barre d'edition !!
	$barre = &$barres['forum']; // insertion dans la barre des forum ? (a tester)

//	$barre->ajouterApres('grpCaracteres', //old//
//		array(
//			'id'        => 'sepChrGbe', //old//
//			'separator' => '---------------', //old//
//			'display'   => false, //old//
//		) //old//
//	); //old//
	$barre->cacher('grpCaracteres'); //new//
//	$barre->ajouterApres('sepChrGbe', //old//
	$barre->ajouterApres('grpCaracteres', //new//
		array(
			'id'          => 'lstChrGbe',
			'name'        => _T('barre_outils:barre_inserer_caracteres'),
			'className'   => 'lstChrGbe',
			'openWith'    => '<multi>[[!['. "Indiquez le code ISO-2 de la nouvelle langue :\n- ajg n&#596; ajagb&egrave; \n en for english \n- ewe na e&#651;egb&egrave; \n- fr pour fran&ccedil;ais \n- fon nu f&#596;ngb&egrave; \n- gen n&#603; g&#603;&#771;gb&egrave; \n- etc." .']!]]',
			'closeWith'   => '</multi>',
//			'selectionType' => 'word',
			'display'     => true,
			'dropMenu'    => array(
//= caracteres latins modifies utilises par langues africaines et IPA
				array(  /// d-afro
					'id'          => 'chr_u0256',
					'name'        => _T('pp_gbe:u0256'),
					'className'   => 'chr_u0256',
					'replaceWith' => '&#598;',
					'display'     => true,
				),
				array( /// D-afro
					'id'          => 'chr_u0189',
					'name'        => _T('pp_gbe:u0189'),
					'className'   => 'chr_u0189',
					'replaceWith' => '&#393;',
					'display'     => true,
				),
				array( /// e-afro
					'id'          => 'chr_u025b',
					'name'        => _T('pp_gbe:u025b'),
					'className'   => 'chr_u025b',
					'replaceWith' => '&#603;',
					'display'     => true,
				),
				array( /// E-afro
					'id'          => 'chr_u0190',
					'name'        => _T('pp_gbe:u0190'),
					'className'   => 'chr_u0190',
					'replaceWith' => '&#400;',
					'display'     => true,
				),
				array( /// f-scrip
					'id'          => 'chr_u0192',
					'name'        => _T('pp_gbe:u0192'),
					'className'   => 'chr_u0192',
					'replaceWith' => '&#402;',
					'display'     => true,
//					'lang'        => array('ewe'),
				),
				array( /// F-script
					'id'          => 'chr_u0191',
					'name'        => _T('pp_gbe:u0191'),
					'className'   => 'chr_u0191',
					'replaceWith' => '&#401;',
					'display'     => true,
//					'lang'        => array('ewe'),
				),
				array( /// gamma
					'id'          => 'chr_u0263',
					'name'        => _T('pp_gbe:u0263'),
					'className'   => 'chr_u0263',
					'replaceWith' => '&#611;',
					'display'     => true,
//					'lang'        => array('ajg','ewe'),
				),
				array( /// Gamma
					'id'          => 'chr_u0194',
					'name'        => _T('pp_gbe:u0194'),
					'className'   => 'chr_u0194',
					'replaceWith' => '&#404;',
					'display'     => true,
//					'lang'        => array('ajg','ewe'),
				),
				array( /// eng
					'id'          => 'chr_u014b',
					'name'        => _T('pp_gbe:u014b'),
					'className'   => 'chr_u014b',
					'replaceWith' => '&#331;',
					'display'     => true,
//					'lang'        => array('ajg','ewe'),
				),
				array( /// Eng
					'id'          => 'chr_u014a',
					'name'        => _T('pp_gbe:u014a'),
					'className'   => 'chr_u014a',
					'replaceWith' => '&#330;',
					'display'     => true,
//					'lang'        => array('ajg','ewe'),
				),
				array( /// o-afro
					'id'          => 'chr_u0254',
					'name'        => _T('pp_gbe:u0254'),
					'className'   => 'chr_u0254',
					'replaceWith' => '&#596;',
					'display'     => true,
				),
				array( /// O-afro
					'id'          => 'chr_u0186',
					'name'        => _T('pp_gbe:u0186'),
					'className'   => 'chr_u0186',
					'replaceWith' => '&#390;',
					'display'     => true,
				),
				array( /// v-script
					'id'          => 'chr_u028b',
					'name'        => _T('pp_gbe:u028b'),
					'className'   => 'chr_u028b',
					'replaceWith' => '&#651;',
					'display'     => true,
//					'lang'        => array('ewe'),
				),
				array( /// V-script
					'id'          => 'chr_u01b2',
					'name'        => _T('pp_gbe:u01b2'),
					'className'   => 'chr_u01b2',
					'replaceWith' => '&#434;',
					'display'     => true,
//					'lang'        => array('ewe'),
				),
//= caracteres diacritiques (accents Unicode)
				array( /// tilda = nasalisation
					'id'          => 'chr_u0303',
					'name'        => _T('pp_gbe:u0303'),
					'className'   => 'chr_u0303',
					'replaceWith' => '&#771;',
					'display'     => true,
//					'lang'        => array('ajg','ewe'),
				),
				array( /// accent aigu = ton montant
					'id'          => 'chr_u0301',
					'name'        => _T('pp_gbe:u0301'),
					'className'   => 'chr_u0301',
					'replaceWith' => '&#769;',
					'display'     => true,
				),
				array( /// accent circonflexe = ton ascendant-descendant
					'id'          => 'chr_u0302',
					'name'        => _T('pp_gbe:u0302'),
					'className'   => 'chr_u0302',
					'replaceWith' => '&#770;',
					'display'     => true,
				),
				array( /// accent grave = ton descendant
					'id'          => 'chr_u0300',
					'name'        => _T('pp_gbe:u0300'),
					'className'   => 'chr_u0300',
					'replaceWith' => '&#768;',
					'display'     => true,
				),
				array( /// caron = ton descendant-ascendant
					'id'          => 'chr_u030c',
					'name'        => _T('pp_gbe:u030c'),
					'className'   => 'chr_u030c',
					'replaceWith' => '&#780;',
					'display'     => true,
				),
				array( /// macron = ton mi-moyen
					'id'          => 'chr_u0304',
					'name'        => _T('pp_gbe:u0304'),
					'className'   => 'chr_u0304',
					'replaceWith' => '&#772;',
					'display'     => true,
//					'lang'        => array('ajg'),
				),
				array( /// trema = son de langues coloniales
					'id'          => 'chr_u0308',
					'name'        => _T('pp_gbe:u0308'),
					'className'   => 'chr_u0308',
					'replaceWith' => '&#776;',
					'display'     => true,
//					'lang'        => array('deu','eng','esp','fra','ita','por'),
				),
				array( /// cedille = specifique langues europeennes
					'id'          => 'chr_u00c7',
					'name'        => _T('barre_outils:barre_c_cedille_maj'),
					'className'   => 'chr_u00c7',
					'replaceWith' => '&Ccedil;',
					'display'     => true,
//					'lang'        => array('cpf','fr'),
				),
//= caracteres ligaturees (second dans precedant)
				array( /// ae = ash
					'id'          => 'chr_u00e6',
					'name'        => _T('barre_outils:barre_ea'),
					'className'   => 'chr_u00e6',
					'replaceWith' => '&aelig;',
					'display'     => true,
					'lang'        => array('cpf','fr'),
				),
				array( /// AE = Ash
					'id'          => 'chr_u00c6',
					'name'        => _T('barre_outils:barre_ea_maj'),
					'className'   => 'chr_u00c6',
					'replaceWith' => '&AElig;',
					'display'     => false,
					'lang'        => array('cpf','fr'),
				),
				array( /// oe = ethel
					'id'          => 'chr_u0153',
					'name'        => _T('barre_outils:barre_eo'),
					'className'   => 'chr_u0153',
					'replaceWith' => '&oelig;',
					'display'     => true,
					'lang'        => array('fr'),
				),
				array( /// OE = Ethel
					'id'          => 'chr_u0152',
					'name'        => _T('barre_outils:barre_eo_maj'),
					'className'   => 'chr_u0152',
					'replaceWith' => '&OElig;',
					'display'     => true,
					'lang'        => array('fr'),
				),
				array( /// z dans s
					'id'          => 'chr_u00df',
					'name'        => _T('Eszet'),
					'className'   => 'chr_u00df',
					'replaceWith' => '&#223;',
					'display'     => false,
					'lang'        => array('de'),
				),
//= caracteres latins modifies utilises par des alphabets europeens
				array(  /// eth
					'id'          => 'chr_u00f0',
					'name'        => _T('eth'),
					'className'   => 'chr_u00f0',
					'replaceWith' => '&eth;',
					'display'     => false,
				),
				array(  /// Eth
					'id'          => 'chr_u00d0',
					'name'        => _T('Eth'),
					'className'   => 'chr_u00d0',
					'replaceWith' => '&Eth;',
					'display'     => false,
				),
				array(  /// o slash
					'id'          => 'chr_u00f8',
					'name'        => _T('pp_gbe:u0256'),
					'className'   => 'chr_u00f8',
					'replaceWith' => '&oslash;',
					'display'     => false,
				),
				array(  /// O slash
					'id'          => 'chr_u00d8',
					'name'        => _T('pp_gbe:u0256'),
					'className'   => 'chr_u00d8',
					'replaceWith' => '&Oslash;',
					'display'     => false,
				),
				array(  /// thorn
					'id'          => 'chr_u00fe',
					'name'        => _T('Thorn'),
					'className'   => 'chr_u00fe',
					'replaceWith' => '&thorn;',
					'display'     => false,
				),
				array(  /// Thorn
					'id'          => 'chr_u00de',
					'name'        => _T('Thorn'),
					'className'   => 'chr_u00de',
					'replaceWith' => '&Thorn;',
					'display'     => false,
				),
//= fonctions de changement de casse
				array( /// tout en majuscules
					'id'          => 'fcn_uppercase',
					'name'        => _T('barre_outils:barre_gestion_cr_changercassemajuscules'),
					'className'   => '2uppercase',
					'replaceWith' => 'function(markitup) { return markitup.selection.toUpperCase() }',
					'display'     => true,
					'selectionType' => 'word',
//					'lang'        => array('de','eo','en','es','fr','it','pt'),
				),
				array( /// tout en minuscules
					'id'          => 'fcn_2lowercase',
					'name'        => _T('barre_outils:barre_gestion_cr_changercasseminuscules'),
					'className'   => 'fcn_2lowercase',
					'replaceWith' => 'function(markitup) { return markitup.selection.toLowerCase() }',
					'display'     => true,
					'selectionType' => 'word',
//					'lang'        => array('de','eo','en','es','fr','it','pt'),
				),
				array( /// debut de mots en majuscule
					'id'          => 'fcn_2titlecase',
					'name'        => _T('barre_outils:barre_gestion_cr_changercassetitres'),
					'className'   => 'fcn_2titlecase',
					'replaceWith' => 'function(markitup) { return markitup.selection.toTitleCase() }',
					'display'     => false,
					'selectionType' => 'word',
//					'lang'        => array('de','eo','en','es','fr','it','pt'),
				),
//
			), // end drop-menu
		) // end group
	);
	return $barres;
}

/**
 * Definitions des liens entre css et icones
 */
function pp_gbe_porte_plume_lien_classe_vers_icone($flux) {
	$icones = array(
//cas		'nom_de_classe' => 'fichier_dans_icones_barre',
//ex:		'outil_header1' => 'text_heading_1.png',
//cas		'nom_de_classe' => array('spt-v1.png','-10px -226px'),
//ex:		'outil_header1' => array('spt-v1.png','-10px -226px'),

//		'lstChrGbe' => 'ajavio.png', //old//
		'lstChrGbe' => 'keyboard.png', //new//
		'chr_u00df' => 'u00df.png', //new//
		'chr_u00c6' => 'u014a.png', //new//
		'chr_u00e6' => 'u00e6.png', //new//
		'chr_u0152' => 'u0152.png', //new//
		'chr_u0153' => 'u0153.png', //new//

		'chr_u00c7' => 'u00c7.png', //new//
		'chr_u00e7' => 'u00e7.png', //new//
		'chr_u00b0' => 'u00b0.png', //new//
		'chr_u00d0' => 'u00d0.png', //new//

		'chr_u00de' => 'u00d0.png', //new//
		'chr_u00fe' => 'u00d0.png', //new//
		'chr_u00d8' => 'u00d0.png', //new//
		'chr_u00f8' => 'u00d0.png', //new//

		'chr_u014b' => 'u014b.png',
		'chr_u0186' => 'u0186.png',
		'chr_u0189' => 'u0189.png',
		'chr_u0190' => 'u0190.png',
		'chr_u0191' => 'u0191.png',
		'chr_u0192' => 'u0192.png',
		'chr_u0194' => 'u0194.png',
		'chr_u01b2' => 'u01b2.gif',
		'chr_u0254' => 'u0254.png',
		'chr_u0256' => 'u0256.png',
		'chr_u025b' => 'u025b.png',
		'chr_u0263' => 'u0263.png',
		'chr_u028b' => 'u028b.png',
		'chr_u0300' => 'u0300.gif',
		'chr_u0301' => 'u0301.gif',
		'chr_u0302' => 'u0302.gif',
		'chr_u0303' => 'u0303.gif',
		'chr_u0304' => 'u0304.gif',
		'chr_u0308' => 'u0308.gif',
		'chr_u030c' => 'u030c.gif',

		'fcn_2uppercase' => 'text2uppercase.png',
		'fcn_2lowercase' => 'text2lowercase.png',
		'fcn_2titlecase' => 'text2titlecase.png',
	);
	return array_merge($flux, $icones);
}

?>

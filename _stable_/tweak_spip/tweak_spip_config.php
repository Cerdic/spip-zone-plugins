<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

//-----------------------------------------------------------------------------//
//                               options                                       //
//-----------------------------------------------------------------------------//
/*
add_outil( array(
	'id' => 'revision_nbsp',
	'code:options' => '$GLOBALS["activer_revision_nbsp"] = true; $GLOBALS["test_i18n"] = true ;',
	'categorie' => 'admin',
));
*/

add_variable( array(
	'nom' => 'radio_desactive_cache3',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	// si la variable est egale a 1, on code...
	// jquery.js et forms_styles.css restent en cache.
	'code:%s' => "\$fond = isset(\$GLOBALS['fond'])?\$GLOBALS['fond']:_request('page');
if (!in_array(\$fond, array('jquery.js','forms_styles.css'))) \$_SERVER['REQUEST_METHOD']='POST';",
));
	// ici on a besoin d'une case input. La variable est : quota_cache
	// a la toute premiere activation de l'outil, la valeur sera : $GLOBALS['quota_cache']
add_variable( array(
	'nom' => 'quota_cache',
	'format' => 'nombre',
	'defaut' => "\$GLOBALS['quota_cache']",
	'code' => "\$GLOBALS['quota_cache']=%s;",
));
add_outil( array(
	'id' => 'SPIP_cache',
	'code:options' => "%%radio_desactive_cache3%%\n%%quota_cache%%",
	'categorie' => 'admin',
		'auteur' => '[C&eacute;dric MORIN->mailto:cedric.morin@yterium.com] (d&eacute;sactivation)'
));

	// ici on a besoin d'une case input. La variable est : dossier_squelettes
	// a la toute premiere activation de l'outil, la valeur sera : $GLOBALS['dossier_squelettes']
add_variable( array(
	'nom' => 'dossier_squelettes',
	'format' => 'chaine',
	'defaut' => "\$GLOBALS['dossier_squelettes']",
	'code' => "\$GLOBALS['dossier_squelettes']=%s;",
));
add_outil( array(
	'id' => 'dossier_squelettes',
	'code:options' => "%%dossier_squelettes%%",
	'categorie' => 'admin',
));

	// ici on a besoin d'une case input. La variable est : cookie_prefix
	// a la toute premiere activation de l'outil, la valeur sera : $GLOBALS['cookie_prefix']
add_variable( array(
	'nom' => 'cookie_prefix',
	'format' => 'chaine',
	'defaut' => "\$GLOBALS['cookie_prefix']",
	'code' => "\$GLOBALS['cookie_prefix']=%s;",
));
add_outil( array(
	'id' => 'cookie_prefix',
	'code:options' => "%%cookie_prefix%%",
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'supprimer_numero',
	// inserer : $table_des_traitements['TITRE'][]= 'typo(supprimer_numero(%s))';
	'traitement:TITRE:pre_typo' => 'supprimer_numero',
	// inserer : $table_des_traitements['NOM'][]= 'typo(supprimer_numero(%s))';
	'traitement:NOM:pre_typo' => 'supprimer_numero',
	'categorie' => 'public',
));

add_outil( array(
	'id' => 'paragrapher',
	'code:options' => "\$GLOBALS['toujours_paragrapher']=true;",
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'forcer_langue',
	'code:options' => "\$GLOBALS['forcer_lang']=true;",
	'categorie' => 'public',
));

add_outil( array(
	'id' => 'insert_head',
	'code:options' => "\$GLOBALS['spip_pipeline']['affichage_final'] .= '|f_insert_head';", 
	'categorie' => 'spip',
	'version-min' => 1.92,
));

	// ici on a besoin d'une case input. La variable est : suite_introduction
	// a la toute premiere activation de l'outil, la valeur sera : '&nbsp;(...)'
add_variable( array(
	'nom' => 'suite_introduction',
	'format' => 'chaine',
	'defaut' => '"&nbsp;(...)"',
	'code' => "define('_INTRODUCTION_SUITE', %s);",
));
add_variable( array(
	'nom' => 'lgr_introduction',
	'format' => 'nombre',
	'defaut' => 100,
	'code:%s && %s!=100' => "define('_INTRODUCTION_LGR', %s);",
));
if($GLOBALS['spip_version']<1.93) {
	add_outil( array(
		'id' => 'suite_introduction',
		'code:options' => "%%suite_introduction%%",
		'categorie' => 'spip',
		'version-min' => 1.93,
	));
	add_outil( array(
		'id' => 'introduction',
		'code:options' => "%%lgr_introduction%%",
		'categorie' => 'spip',
	));
} else { // on regroupe les deux variables !
	add_outil( array(
		'id' => 'introduction2',
		'code:options' => "%%lgr_introduction%%\n%%suite_introduction%%",
		'categorie' => 'spip',
		'version-min' => 1.93,
	));
}

	// ici on a besoin de deux boutons radio : _T('icone_interface_simple') et _T('icone_interface_complet')
add_variable( array(
	'nom' => 'radio_set_options4',
	'format' => 'chaine',
	'radio' => array('basiques' => 'icone_interface_simple', 'avancees' => 'icone_interface_complet'),
	'defaut' => '"avancees"',
	'code' => "\$GLOBALS['set_options']=%s;",
));
add_outil( array(
	'id' => 'set_options',
	'auteur' 	 => 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'code:options' => "%%radio_set_options4%%",
	'categorie' => 'admin',
	// pipeline pour retirer en javascript le bouton de controle de l'interface
	'pipeline:header_prive' => 'set_options_header_prive',
	// non supporte a partir de la version 1.9.3
	'version-max' => 1.93,
));

	// ici on a besoin de six boutons radio : 'page', 'html', 'propres', 'propres2, ''standard' et 'propres-qs'
add_variable( array(
	'nom' => 'radio_type_urls3',
	'format' => 'chaine',
	'radio' => array('page' => 'cout:page', 'html' => 'cout:html', 'propres' => 'cout:propres', 'propres2' => 'cout:propres2', 
			'standard' => 'cout:standard', 'propres-qs' => 'cout:propres-qs' ),
	'radio/ligne' => 4,
	'defaut' => "strlen(\$GLOBALS['type_urls'])?\$GLOBALS['type_urls']:'page'",
	'code' => "\$GLOBALS['type_urls']=%s;",
));
add_variable( array(
	'nom' => 'spip_script',
	'format' => 'chaine',
	'defaut' => "get_spip_script()",
	'code' => "define('_SPIP_SCRIPT', %s);",
));
add_outil( array(
	'id' => 'type_urls',
	'code:options' => "%%radio_type_urls3%%;%%spip_script%%",
	'categorie' => 'admin',
));

	// ici on a besoin de trois boutons radio : _T('cout:js_jamais'), _T('cout:js_defaut') et _T('cout:js_toujours')
add_variable( array(
	'nom' => 'radio_filtrer_javascript3',
	'format' => 'nombre',
	'radio' => array(-1 => 'cout:js_jamais', 0 => 'cout:js_defaut', 1 => 'cout:js_toujours'),
	'defaut' => 0,
	// si la variable est non nulle, on code...
	'code:%s' => "\$GLOBALS['filtrer_javascript']=%s;",
));
add_outil( array(
	'id' => 'filtrer_javascript',
	'code:options' => "%%radio_filtrer_javascript3%%",
	'categorie' => 'admin',
	'version-min' => 1.92,
));

	// ici on a besoin d'une case input. La variable est : forum_lgrmaxi
	// a la toute premiere activation de l'outil, la valeur sera : 0 (aucune limite)
add_variable( array(
	'nom' => 'forum_lgrmaxi',
	'format' => 'nombre',
	'defaut' => 0,
	'code:%s' => "define('_FORUM_LONGUEUR_MAXI', %s);",
));
add_outil( array(
	'id' => 'forum_lgrmaxi',
	'code:options' => "%%forum_lgrmaxi%%",
	'categorie' => 'admin',
	'version-min' => 1.92,
));

/*add_variable( array(
	'nom' => 'radio_desactive_cache3',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code' => "define('_FORUM_OBLIGE_AUTEUR', %s);",
));*/
add_outil( array(
	'id' => 'auteur_forum',
	'categorie'	 => 'admin',
	'code:options' => "define('_FORUM_OBLIGE_AUTEUR', 'oui');",
	'pipeline:affichage_final' => 'Auteur_forum_affichage_final',
));

	// ici on a besoin de trois boutons radio : _T('cout:sf_defaut'), _T('cout:sf_amont') et _T('cout:sf_tous')
add_variable( array(
	'nom' => 'radio_suivi_forums3',
	'format' => 'chaine',
	'radio' => array('defaut' => 'cout:sf_defaut', '_SUIVI_FORUMS_REPONSES' => 'cout:sf_amont', '_SUIVI_FORUM_THREAD' => 'cout:sf_tous'),
	'defaut' => '"defaut"',
	// si la variable est différente de 'defaut' alors on codera le define
	'code:%s!=="defaut"' => "define(%s, true);",
));
add_outil( array(
	'id' => 'suivi_forums',
	'code:options' => "%%radio_suivi_forums3%%",
	'categorie' => 'admin',
	'version-min' => 1.92,
));

add_outil( array(
	'id' => 'log_couteau_suisse',
	'code:options' => "\$GLOBALS['log_couteau_suisse']=true;",
));

add_outil( array(
	'id' => 'xml',
	'code:options' => "\$GLOBALS['xhtml']='sax';",
	'auteur' => 'Ma&iuml;eul Rouquette (maieulrouquette@tele2.fr)',
	'categorie' =>'public',
	'version-min' => '1.92',
));

add_outil( array(
	'id' => 'f_jQuery',
	'code:options' => "\$GLOBALS['spip_pipeline']['insert_head'] = str_replace('|f_jQuery', '', \$GLOBALS['spip_pipeline']['insert_head']);",
	'auteur' => 'Fil',
	'categorie' =>'public',
	'version-min' => '1.92',
));

add_variable( array(
	'nom' => 'style_p',
	'format' => 'chaine',
	'defaut' => '"spip"',
	'code:strlen(%s)' => ' class=%s',
));
add_variable( array(
	'nom' => 'style_h',
	'format' => 'chaine',
	'defaut' => '"spip"',
	'code:strlen(%s)' => ' class=%s',
));
add_outil( array(
	'id' => 'class_spip',
	'code:options' => "\$GLOBALS['class_spip']='%%style_p%%';\n\$GLOBALS['class_spip_plus']='%%style_h%%';",
	'categorie' => 'public',
	'version-min' => 1.93,
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'verstexte',
	'auteur' => '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie' => 'spip',
));

add_outil( array(
	'id' => 'orientation',
	'auteur' 	 => 'Pierre Andrews (Mortimer) &amp; IZO',
	'categorie' => 'spip',
));

add_outil( array(
	'id' => 'decoupe',
	'code:options' => "define('_decoupe_SEPARATEUR', '++++');",
	// inserer : $table_des_traitements['TEXTE'][]= 'decouper_en_pages(propre(%s))';
	'traitement:TEXTE:post_propre' => 'decouper_en_pages',
	'categorie' => 'typo-racc',
));

// couplage avec l'outil 'decoupe', donc 'sommaire' doit etre place juste apres :
// il faut inserer le sommaire dans l'article et ensuite seulement choisir la page
include_spip('inc/texte');
$code = str_replace("'", "\'", tweak_code_echappement("<!--  -->\n", 'SOMMAIRE'));
add_outil( array(
	'id' => 'sommaire',
	'code:options' => "define('_sommaire_REM', '$code');\ndefine('_sommaire_SANS_SOMMAIRE', '[!sommaire]');",
	// inserer : $table_des_traitements['TEXTE'][]= 'sommaire_d_article(propre(%s))';
	'traitement:TEXTE:post_propre' => 'sommaire_d_article',
	'categorie' => 'typo-corr',
));

//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction a revoir ?
add_outil( array(
	'id' => 'desactiver_flash',
	'auteur' 	 => '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie'	 => 'public',
	'pipeline:affichage_final' => 'InhibeFlash_affichage_final',
));

add_variable( array(
	'nom' => 'radio_target_blank3',
	'format' => 'nombre',
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),
	'defaut' => 0,
	'code' => '$GLOBALS["tweak_target_blank"]=%s;',
));
add_variable( array(
	'nom' => 'url_glossaire_externe',
	'format' => 'chaine',
	'defaut' => '$GLOBALS["url_glossaire_externe"]',
	'code:strlen(%s)' => '$GLOBALS["url_glossaire_externe"]=%s;',
));
add_outil( array(
	'id' => 'SPIP_liens',
	'code:options' => "%%radio_target_blank3%% %%url_glossaire_externe%%",
	'code:js' => 'if (%%radio_target_blank3%%) { $(document).ready(function () { $("a.spip_out,a.spip_url,a.spip_glossaire").attr("target", "_blank"); }); }',
	'categorie' => 'public',
));


//-----------------------------------------------------------------------------//
//                               TYPO                                          //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'toutmulti',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'ToutMulti_pre_typo',
));

add_outil( array(
	'id' => 'pucesli',
	'auteur' 	 => '[J&eacute;r&ocirc;me Combaz->http://conseil-recherche-innovation.net/index.php/2000/07/08/72-jerome-combaz]',
	'categorie'	 => 'typo-corr',
	'pipeline:pre_typo' => 'pucesli_pre_typo',
));

add_outil( array(
	'id' => 'decoration',
	'auteur' 	 => '[izo@aucuneid.net->http://www.aucuneid.com/bones]',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'decoration_pre_typo',
));

add_variable( array(
	'nom' => 'couleurs_fonds',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non' ),
	'defaut' => 1,
	'code' => "define('_COULEURS_FONDS', %s);",
));
add_variable( array(
	'nom' => 'set_couleurs',
	'format' => 'nombre',
	'radio' => array(0 => 'cout:toutes_couleurs', 1 => 'cout:certaines_couleurs'),
	'radio/ligne' => 1,
	'defaut' => 0,
	'code' => "define('_COULEURS_SET', %s);",
));
add_variable( array(
	'nom' => 'couleurs_perso',
	'format' => 'chaine',
	'lignes' => 3,
	'defaut' => '$GLOBALS["url_glossaire_externe"]',
	'code' => "define('_COULEURS_PERSO', %s);",
));
add_outil( array(
	'id' => 'couleurs',
	'auteur' 	 => 'Aur&eacute;lien PIERARD (id&eacute;e originale)',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'couleurs_pre_typo',
	'code:options' => "%%couleurs_fonds%% %%set_couleurs%%\n%%couleurs_perso%%",
));

// outil specifiquement français. D'autres langues peuvent etre ajoutees dans outils/typo_exposants.php
add_outil( array(
	'id' => 'typo_exposants',
	'auteur' 	 => 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'categorie'	 => 'typo-corr',
	'pipeline:post_typo' => 'typo_exposants',
));

add_outil( array(
	'id' => 'guillemets',
	'auteur' 	 => 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'categorie'	 => 'typo-corr',
	'pipeline:post_typo' => 'typo_guillemets',
));

add_variable( array(
	'nom' => 'liens_orphelins',
	'format' => 'nombre',
	'radio' => array(0 => 'cout:basique', 1 => 'cout:etendu'),
	'defaut' => 0,
	'code:%s' => '$GLOBALS["liens_orphelins_etendu"]=true;',
));
add_outil( array(
	'id' => 'liens_orphelins',
	'categorie'	 => 'typo-corr',
	'code:options' => '%%liens_orphelins%%',
	'pipeline:pre_propre' => 'liens_orphelins',
));

add_outil( array(
	'id' => 'filets_sep',
	'auteur' 	 => 'FredoMkb',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'filets_sep',
));

add_outil( array(
	'id' => 'smileys',
	'auteur' 	 => 'Sylvain',
	'categorie'	 => 'typo-corr',
	'pipeline:pre_typo' => 'tweak_smileys_pre_typo',
));

add_outil( array(
	'id' => 'chatons',
	'auteur' 	 => 'BoOz (booz.bloog@laposte.net)',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'chatons_pre_typo',
));

// Idées d'ajouts :
// http://archives.rezo.net/spip-core.mbox/
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS et d'autres balises #MAINTENANT #LESADMINISTRATEURS #LESREDACTEURS #LESVISITEURS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles
// voir :
//		$GLOBALS['debut_intertitre'] = "<h3 class='mon_style_h3'>";
//		$GLOBALS['fin_intertitre'] = "</h3>";
// http://www.spip-contrib.net/Generation-automatique-de
// Les sessions
// colorations du code
// boutonstexte

cout_log("Fin de tweak_spip_config.php");
//global $cout_variables; cout_log($cout_variables, 'tweak_variables :');
?>

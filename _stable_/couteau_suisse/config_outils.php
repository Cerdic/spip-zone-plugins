<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#

cs_log("inclusion de config_outils.php");
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
if (!in_array(\$fond, array('jquery.js','forms_styles.css'))) \$_SERVER['REQUEST_METHOD']='POST';\n",
));
add_variable( array(
	'nom' => 'duree_cache',
	'format' => 'nombre',
	'defaut' => "24", // 1 jour
	'code' => "define('_DUREE_CACHE_DEFAUT', %s*3600);\n",
));
add_variable( array(
	'nom' => 'quota_cache',
	'format' => 'nombre',
	'defaut' => "\$GLOBALS['quota_cache']",
	'code' => "\$GLOBALS['quota_cache']=%s;",
));
add_outil( array(
	'id' => 'SPIP_cache',
	'code:options' => "%%radio_desactive_cache3%%%%duree_cache%%%%quota_cache%%",
	'categorie' => 'admin',
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
	'code:options' => '%%dossier_squelettes%%',
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

add_variable( array(
	'nom' => 'paragrapher',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non', -1 => 'cout:par_defaut'),
	'defaut' => "\$GLOBALS['toujours_paragrapher']?1:-1",
	'code:%s>=0' => "\$GLOBALS['toujours_paragrapher']=%s;",
));
add_outil( array(
	'id' => 'paragrapher2',
	'code:options' => '%%paragrapher%%',
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
	'code' => "define('_INTRODUCTION_SUITE', %s);\n",
));
add_variable( array(
	'nom' => 'lgr_introduction',
	'format' => 'nombre',
	'defaut' => 100,
	'code:%s && %s!=100' => "define('_INTRODUCTION_LGR', %s);\n",
));
add_variable( array(
	'nom' => 'lien_introduction',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code' => "define('_INTRODUCTION_LIEN', %s);",
));
add_outil( array(
	'id' => 'introduction',
	'code:options' => "%%lgr_introduction%%%%suite_introduction%%%%lien_introduction%%",
	'categorie' => 'spip',
));

	// ici on a besoin de deux boutons radio : _T('icone_interface_simple') et _T('icone_interface_complet')
add_variable( array(
	'nom' => 'radio_set_options4',
	'format' => 'chaine',
	'radio' => array('basiques' => 'icone_interface_simple', 'avancees' => 'icone_interface_complet'),
	'defaut' => '"avancees"',
	'code' => "\$_GET['set_options']=\$GLOBALS['set_options']=%s;",
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

add_outil( array(
	'id' => 'simpl_interface',
	'code:options' => "@define('_ACTIVER_PUCE_RAPIDE', false);",
	'categorie' => 'admin',
	'version-min' => 1.93,
));

	// ici on a besoin de six boutons radio : 'page', 'html', 'propres', 'propres2, ''standard' et 'propres-qs'
add_variable( array(
	'nom' => 'radio_type_urls3',
	'format' => 'chaine',
	'radio' => array('page' => 'cout:page', 'html' => 'cout:html', 'propres' => 'cout:propres', 'propres2' => 'cout:propres2',
			'standard' => 'cout:standard', 'propres-qs' => 'cout:propres-qs' ),
	'radio/ligne' => 4,
	'defaut' => "strlen(\$GLOBALS['type_urls'])?\$GLOBALS['type_urls']:'page'",
	'code' => "\$GLOBALS['type_urls']=%s;\n",
));
add_variable( array(
	'nom' => 'spip_script',
	'format' => 'chaine',
	'defaut' => "get_spip_script()",
	'code' => "define('_SPIP_SCRIPT', %s);",
));
add_outil( array(
	'id' => 'type_urls',
	'code:options' => "%%radio_type_urls3%%%%spip_script%%",
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

add_outil( array(
	'id' => 'auteur_forum',
	'categorie'	 => 'admin',
	'jquery'	=> 'oui',
	'code:options' => "@define('_FORUM_OBLIGE_AUTEUR', 'oui');",
	'pipeline:affichage_final' => 'Auteur_forum_affichage_final',
));

	// ici on a besoin de trois boutons radio : _T('cout:par_defaut'), _T('cout:sf_amont') et _T('cout:sf_tous')
add_variable( array(
	'nom' => 'radio_suivi_forums3',
	'format' => 'chaine',
	'radio' => array('defaut' => 'cout:par_defaut', '_SUIVI_FORUMS_REPONSES' => 'cout:sf_amont', '_SUIVI_FORUM_THREAD' => 'cout:sf_tous'),
	'defaut' => '"defaut"',
	// si la variable est differente de 'defaut' alors on codera le define
	'code:%s!=="defaut"' => "define(%s, true);",
));
add_outil( array(
	'id' => 'suivi_forums',
	'code:options' => "%%radio_suivi_forums3%%",
	'categorie' => 'admin',
	'version-min' => 1.92,
));

add_outil( array(
	'id' => 'spam',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'no_IP',
	'code:options' => '$ip = substr(md5($ip),0,16);',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'flock',
	'code:options' => "@define('_SPIP_FLOCK',false);",
	'categorie' => 'admin',
	'version-min' => 1.93,
));

add_outil( array(
	'id' => 'log_couteau_suisse',
//	'code:options' => "@define('_LOG_CS','oui');",
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

add_variable( array(
	'nom' => 'admin_travaux',
	'format' => 'nombre',
	'radio' => array(0 => 'cout:tous', 1 => 'cout:sauf_admin'),
	'defaut' => 0,
	'code' => "define('_en_travaux_ADMIN', %s);\n",
));
add_variable( array(
	'nom' => 'message_travaux',
	'format' => 'chaine',
	'defaut' => "_T('cout:prochainement')",
	'lignes' => 3,
	'code' => "define('_en_travaux_MESSAGE', %s);\n",
));
add_variable( array(
	'nom' => 'titre_travaux',
	'format' => 'nombre',
	'radio' => array(1 => 'cout:travaux_titre', 0 => 'cout:travaux_nom_site'),
	'defaut' => 1,
	'code:%s' => "define('_en_travaux_TITRE', %s);",
));
add_outil( array(
	'id' => 'en_travaux',
	'code:options' => "%%message_travaux%%%%admin_travaux%%%%titre_travaux%%",
	'categorie' => 'admin',
	'auteur' => '[Arnaud Ventre->ventrea@gmail.com]',
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'verstexte',
	'auteur' => '[Cedric MORIN->cedric.morin@yterium.com]',
	'categorie' => 'spip',
));

add_outil( array(
	'id' => 'orientation',
	'auteur' 	 => 'Pierre Andrews (Mortimer) &amp; IZO',
	'categorie' => 'spip',
));

add_outil( array(
	'id' => 'decoupe',
	'contrib'	=> 2135,
	'code:options' => "@define('_decoupe_SEPARATEUR', '++++');
if (isset(\$_GET['var_recherche'])) {
	include_spip('inc/headers');
	redirige_par_entete(str_replace('var_recherche=', 'decoupe_recherche=', \$GLOBALS['REQUEST_URI']));
}",
	'code:css' => "div.pagination {display:block; text-align:center; }
div.pagination img { border:0pt none; margin:0pt; padding:0pt; }",
	// inserer : $table_des_traitements['TEXTE'][]= 'cs_decoupe(propre(%s))';
	'traitement:TEXTE:post_propre' => 'cs_decoupe',
	'traitement:TEXTE:pre_typo' => 'cs_onglets',
	// sans oublier les articles : $table_des_traitements['TEXTE']['articles']= 'cs_decoupe(propre(%s))';
	'traitement:TEXTE/articles:post_propre' => 'cs_decoupe',
	'traitement:TEXTE/articles:pre_typo' => 'cs_onglets',
	'categorie' => 'typo-racc',
));

// couplage avec l'outil 'decoupe', donc 'sommaire' doit etre place juste apres :
// il faut inserer le sommaire dans l'article et ensuite seulement choisir la page
include_spip('inc/texte');
$code = str_replace("'", "\'", cs_code_echappement("<!--  -->\n", 'SOMMAIRE'));
add_variable( array(
	'nom' => 'lgr_sommaire',
	'format' => 'nombre',
	'defaut' => 30,
	'code:%s>=9 && %s<=99' => "define('_sommaire_NB_CARACTERES', %s);\n",
));
add_variable( array(
	'nom' => 'auto_sommaire',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 1,
	'code:%s' => "define('_sommaire_AUTOMATIQUE', %s);\n",
));
add_variable( array(
	'nom' => 'balise_sommaire',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_sommaire_BALISE', %s);",
));
add_outil( array(
	'id' => 'sommaire',
	'contrib'	=> 2378,
	'code:options' => "define('_sommaire_REM', '$code');\ndefine('_sommaire_SANS_SOMMAIRE', '[!sommaire]');\ndefine('_sommaire_AVEC_SOMMAIRE', '[sommaire]');\n%%lgr_sommaire%%%%auto_sommaire%%%%balise_sommaire%%",
	// s'il y a un sommaire, on cache la navigation haute sur les pages
	'code:css' => "div.cs_sommaire {display:block; float:right; margin-left:1em; margin-right:0.4em; overflow:auto; z-index:100; max-height:350px; text-align:left;}",
	'code:js' => '$(document).ready(function(){ if($("div.cs_sommaire").length) $("div.decoupe_haut").css("display", "none"); });',
	// inserer : $table_des_traitements['TEXTE']['article']= 'sommaire_d_article(propre(%s))';
	'traitement:TEXTE/articles:post_propre' => 'sommaire_d_article',
	'traitement:CS_SOMMAIRE:post_propre' => 'sommaire_d_article_balise',
	'categorie' => 'typo-corr',
));

//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction a revoir ?
add_outil( array(
	'id' => 'desactiver_flash',
	'auteur' 	 => '[Cedric MORIN->cedric.morin@yterium.com]',
	'categorie'	 => 'public',
	'jquery'	=> 'oui',
	'pipeline:affichage_final' => 'InhibeFlash_affichage_final',
));

add_variable( array(
	'nom' => 'radio_target_blank3',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 1,
	'code' => '$GLOBALS["tweak_target_blank"]=%s;',
));
add_variable( array(
	'nom' => 'url_glossaire_externe2',
	'format' => 'chaine',
	'defaut' => '$GLOBALS["url_glossaire_externe"]',
	'code:strlen(%s)' => '$GLOBALS["url_glossaire_externe"]=%s;',
));
add_outil( array(
	'id' => 'SPIP_liens',
	'categorie' => 'public',
	'contrib'	=> 2443,
	'jquery'	=> 'oui',
	'code:options' => "%%radio_target_blank3%%\n%%url_glossaire_externe2%%",
	'code:js' => 'if (%%radio_target_blank3%%) { $(document).ready(function(){ $("a.spip_out,a.spip_url,a.spip_glossaire").attr("target", "_blank"); }); }',
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

add_variable( array(
	'nom' => 'decoration_styles',
	'format' => 'chaine',
	'lignes' => 8,
	'defaut' => '"span.sc = font-variant:small-caps;
span.souligne = text-decoration:underline;
span.barre = text-decoration:line-through;
span.dessus = text-decoration:overline;
span.clignote = text-decoration:blink;
span.surfluo = background-color:#ffff00; padding:0px 2px;
span.surgris = background-color:#EAEAEC; padding:0px 2px;
fluo = surfluo"',
	'code' => "define('_decoration_BALISES', %s);",
));
add_outil( array(
	'id' => 'decoration',
	'auteur' 	 => '[izo@aucuneid.net->http://www.aucuneid.com/bones], Pat',
	'contrib'	=> 2427,
	'categorie'	 => 'typo-racc',
	'code:options' => "%%decoration_styles%%",
	'pipeline:pre_typo' => 'decoration_pre_typo',
));

add_variable( array(
	'nom' => 'couleurs_fonds',
	'format' => 'nombre',
	'radio' => array(1 => 'item_oui', 0 => 'item_non' ),
	'defaut' => 1,
	'code' => "define('_COULEURS_FONDS', %s);\n",
));
add_variable( array(
	'nom' => 'set_couleurs',
	'format' => 'nombre',
	'radio' => array(0 => 'cout:toutes_couleurs', 1 => 'cout:certaines_couleurs'),
	'radio/ligne' => 1,
	'defaut' => 0,
	'code' => "define('_COULEURS_SET', %s);\n",
));
add_variable( array(
	'nom' => 'couleurs_perso',
	'format' => 'chaine',
	'lignes' => 3,
	'defaut' => '"gris, rouge"',
	'code' => "define('_COULEURS_PERSO', %s);",
));
add_outil( array(
	'id' => 'couleurs',
	'auteur' 	 => 'Aur&eacute;lien PIERARD (id&eacute;e originale), Pat',
	'categorie'	 => 'typo-racc',
	'contrib'	=> 2427,
	'pipeline:pre_typo' => 'couleurs_pre_typo',
	'code:options' => "%%couleurs_fonds%%%%set_couleurs%%%%couleurs_perso%%",
	'code:fonctions' => "// aide le Couteau Suisse a calculer la balise #INTRODUCTION
function couleurs_introduire(\$texte) {
	\$couleurs = unserialize(\$GLOBALS['meta']['cs_couleurs']);
	\$couleurs = _COULEURS_SET===0?\"\$couleurs[0]|\$couleurs[1]\":\$couleurs[0];
	return preg_replace(\",\[/?(bg|fond)?\s*(\$couleurs|couleur|color)\],i\", '', \$texte);
}
\$GLOBALS['cs_introduire'][] = 'couleurs_introduire';
",
));

// outil specifiquement français. D'autres langues peuvent etre ajoutees dans outils/typo_exposants.php
add_outil( array(
	'id' => 'typo_exposants',
	'auteur' 	 => 'Vincent Ramos [contact->www-lansargues@kailaasa.net]',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 1564,
	'pipeline:post_typo' => 'typo_exposants',
	'code:css' => 'sup.typo_exposants { font-size:75%; font-variant:normal; vertical-align:super; }',
));

add_outil( array(
	'id' => 'guillemets',
	'auteur' 	 => 'Vincent Ramos [contact->www-lansargues@kailaasa.net]',
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
// attention : liens_orphelins doit etre place avant mailcrypt
add_outil( array(
	'id' => 'liens_orphelins',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 2443,
	'code:options' => '%%liens_orphelins%%',
	'pipeline:pre_propre' => 'liens_orphelins_pipeline',
	'traitement:EMAIL' => 'expanser_liens(liens_orphelins',
));

add_outil( array(
	'id' => 'filets_sep',
	'auteur' 	 => 'FredoMkb',
	'categorie'	 => 'typo-racc',
	'contrib'	=> 1563,
	'pipeline:pre_typo' => 'filets_sep',
));

add_outil( array(
	'id' => 'smileys',
	'auteur' 	 => 'Sylvain',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 1561,
	'code:css' => "table.cs_smileys td {text-align:center; font-size:90%; font-weight:bold;}",
	'pipeline:pre_typo' => 'cs_smileys_pre_typo',
));

add_outil( array(
	'id' => 'chatons',
	'auteur' 	 => 'BoOz (booz.bloog@laposte.net)',
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'chatons_pre_typo',
));

add_variable( array(
	'nom' => 'glossaire_groupes',
	'format' => 'chaine',
	'defaut' => "Glossaire",
	'code' => "\$GLOBALS['glossaire_groupes']=%s;\n",
));
add_variable( array(
	'nom' => 'glossaire_limite',
	'format' => 'nombre',
	'defaut' => 0,
	'code:%s>0' => "define('_GLOSSAIRE_LIMITE', %s);\n",
));
add_variable( array(
	'nom' => 'glossaire_js',
	'radio' => array(0 => 'cout:glossaire_css', 1 => 'cout:glossaire_js'),
	'format' => 'nombre',
	'defaut' => 1,
	'code:%s' => "define('_GLOSSAIRE_JS', %s);",
));
add_outil( array(
	'id' => 'glossaire',
	'categorie'	=> 'typo-corr',
	'contrib'	=> 2206,
	'code:options' => "%%glossaire_limite%%%%glossaire_groupes%%%%glossaire_js%%",
//	'pipeline:post_propre' => 'cs_glossaire',
	'traitement:TEXTE:post_propre' => 'cs_glossaire',
	// sans oublier les articles...
	'traitement:TEXTE/articles:post_propre' => 'cs_glossaire',
	// et le chapo des articles...
	'traitement:CHAPO:post_propre' => 'cs_glossaire',
	// Precaution pour les articles virtuels
	'traitement:CHAPO:pre_propre' => 'nettoyer_chapo',
));

// attention : mailcrypt doit etre place apres liens_orphelins
add_outil( array(
	'id' => 'mailcrypt',
	'categorie'	=> 'typo-corr',
	'auteur' 	=> 'Alexis Roussel, Paolo',
	'contrib'	=> 2443,
	'jquery'	=> 'oui',
	'pipeline:post_propre' => 'mailcrypt_post_propre',
	'code:js' => "function lancerlien(a,b){ x='ma'+'ilto'+':'+a+'@'+b; return x; }"
		// jQuery pour remplacer l'arobase image par l'arobase texte
		. "\n\$(document).ready(function(){ \$('span.spancrypt').after('<span>&#6'+'4;<\/span>'); \$('span.spancrypt').remove(); });",
	'code:css' => 'span.spancrypt {background:transparent url(' . find_in_path('img/mailcrypt/leure.gif')
		. ') no-repeat scroll left center; color:#000099; padding-left:12px; text-decoration:none;}',
	'traitement:EMAIL' => 'mailcrypt',
)); 

// attention : liens_en_clair doit etre place apres tous les outils traitant des liens
add_outil( array(
	'id' => 'liens_en_clair',
	'categorie'	 => 'spip',
	'contrib'	=> 2443,
	'pipeline:post_propre' => 'liens_en_clair_post_propre',
)); 

add_outil( array(
	'id' => 'blocs',
	'categorie'	=> 'typo-racc',
	'jquery'	=> 'oui',
	'pipeline:pre_typo' => 'blocs_pre_typo',
));

// Idees d'ajouts :
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

//global $cs_variables; cs_log($cs_variables, 'cs_variables :');
?>

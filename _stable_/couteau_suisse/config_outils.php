<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#

// Noter :
// outils/mon_outil.php : inclus par les pipelines de l'outil
// outils/mon_outil_options.php : inclus par cout_options.php
// outils/mon_outil_fonctions.php : inclus par cout_fonctions.php

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

/* pour SPIP 2.0
define('_NO_CACHE',-1); -> ne jamais utiliser le cache ni meme creer les fichiers cache (Compat : 1)
define('_NO_CACHE',1); -> ne pas utiliser le fichier en cache, mais stocker le resultat du calcul dans le fichier cache (Compat : 0)
define('_NO_CACHE',0); -> toujours prendre tous les fichiers en cache 
 * La fonction retourne :
 * 1 si il faut mettre le cache a jour
 * 0 si le cache est valide
 * -1 si il faut calculer sans stocker en cache
*/
add_variable( array(
	'nom' => 'radio_desactive_cache3',
	'format' => _format_NOMBRE,
	'radio' => defined('_SPIP19300')?array(1 => 'item_oui', 0 => 'item_non'):array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	// si la variable est egale a 1, on code...
	// jquery.js et forms_styles.css restent en cache.
	'code:%s' => "\$cs_fond = isset(\$GLOBALS['fond'])?\$GLOBALS['fond']:_request('page');
if (!in_array(\$cs_fond, array('jquery.js','forms_styles.css'))) \$_SERVER['REQUEST_METHOD']='POST';\n",
));
add_variable( array(
	'nom' => 'duree_cache',
	'format' => _format_NOMBRE,
	'defaut' => "24", // 1 jour
	'code' => "\$GLOBALS['delais']=%s*3600;\n",
));
add_variable( array(
	'nom' => 'duree_cache_mutu',
	'format' => _format_NOMBRE,
	'defaut' => "24", // 1 jour
	'code' => "define('_DUREE_CACHE_DEFAUT', %s*3600);\n",
));
add_variable( array(
	'nom' => 'quota_cache',
	'format' => _format_NOMBRE,
	'defaut' => 10, // 10 Mo
	'code' => "\$GLOBALS['quota_cache']=%s;\n",
));
add_variable( array(
	'nom' => 'derniere_modif_invalide',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),
	'defaut' => 0,
	'code' => "\$GLOBALS['derniere_modif_invalide']=%s;\n",
));

/*add_variable( array(
	'nom' => 'exceptions_cache',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_cache_PERSO', %s);",
));*/
// balise pour choisir le cache a appliquer aux articles

add_outil( array(
	'id' => 'spip_cache',
	'code:spip_options' => "%%radio_desactive_cache3%%%%duree_cache%%%%duree_cache_mutu%%%%quota_cache%%%%derniere_modif_invalide%%",//%%exceptions_cache%%",
	'categorie' => 'admin',
//	'pipelinecode:insert_head' => 'if(defined(\'_cache_PERSO\')) cs_fixe_cache($GLOBALS[\'delais\']);'
));

	// ici on a besoin d'une case input. La variable est : dossier_squelettes
	// a la toute premiere activation de l'outil, la valeur sera : $GLOBALS['dossier_squelettes']
add_variable( array(
	'nom' => 'dossier_squelettes',
	'format' => _format_CHAINE,
	'defaut' => "\$GLOBALS['dossier_squelettes']",
	'code' => "\$GLOBALS['dossier_squelettes']=%s;",
));
add_outil( array(
	'id' => 'dossier_squelettes',
	'code:spip_options' => '%%dossier_squelettes%%',
	'categorie' => 'admin',
));

/*
add_variable( array(
	'nom' => 'cookie_prefix',
	'format' => _format_CHAINE,
	'defaut' => "'spip'",
	'code' => "\$GLOBALS['cookie_prefix']=%s;",
));
add_outil( array(
	'id' => 'cookie_prefix',
	'code:options' => "%%cookie_prefix%%",
	'categorie' => 'admin',
));
*/

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
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non', -1 => 'couteauprive:par_defaut'),
	'defaut' => "-1",
	'code:%s>=0' => "\$GLOBALS['toujours_paragrapher']=%s;",
));
add_outil( array(
	'id' => 'paragrapher2',
	'code:spip_options' => '%%paragrapher%%',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'forcer_langue',
	'code:spip_options' => "\$GLOBALS['forcer_lang']=true;",
	'categorie' => 'public',
));

add_outil( array(
	'id' => 'insert_head',
	'code:options' => "\$GLOBALS['spip_pipeline']['affichage_final'] .= '|f_insert_head';",
	'categorie' => 'spip',
));

	// ici on a besoin d'une case input. La variable est : suite_introduction
	// a la toute premiere activation de l'outil, la valeur sera : '&nbsp;(...)'
add_variable( array(
	'nom' => 'suite_introduction',
	'format' => _format_CHAINE,
	'defaut' => '"&nbsp;(...)"',
	'code' => "define('_INTRODUCTION_SUITE', %s);\n",
));
add_variable( array(
	'nom' => 'lgr_introduction',
	'format' => _format_NOMBRE,
	'defaut' => 100,
	'code:%s && %s!=100' => "define('_INTRODUCTION_LGR', %s);\n",
));
add_variable( array(
	'nom' => 'lien_introduction',
	'format' => _format_NOMBRE,
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
	'format' => _format_CHAINE,
	'radio' => array('basiques' => 'icone_interface_simple', 'avancees' => 'icone_interface_complet'),
	'defaut' => '"avancees"',
	'code' => "\$_GET['set_options']=\$GLOBALS['set_options']=%s;",
));
add_outil( array(
	'id' => 'set_options',
	'auteur' 	 => 'Vincent Ramos',
	'code:options' => "%%radio_set_options4%%",
	'categorie' => 'interface',
	// pipeline pour retirer en javascript le bouton de controle de l'interface
	'pipeline:header_prive' => 'set_options_header_prive',
	// non supporte a partir de la version 1.93
	'version-max' => '1.9299',
));

add_outil( array(
	'id' => 'simpl_interface',
	'code:spip_options' => "define('_ACTIVER_PUCE_RAPIDE', false);",
	'categorie' => 'interface',
	'version-min' => '1.9300',
));

add_outil( array(
	'id' => 'icone_visiter',
	'categorie' => 'interface',
	'pipeline:header_prive' => 'icone_visiter_header_prive',
));

add_variable( array(
	'nom' => 'tri_articles',
	'format' => _format_CHAINE,
	'radio' => array(
		'date_modif DESC' => 'couteauprive:tri_modif',
		'0+titre,titre' => 'couteauprive:tri_titre',
		'date DESC' => 'couteauprive:tri_publi', 
		'perso' => 'couteauprive:tri_perso' ),
	'radio/ligne' => 1,
	'defaut' => "'date DESC'", //"'0+titre,titre'",
	'code:%s!="perso"' => "define('_TRI_ARTICLES_RUBRIQUE', %s);\n",
));
add_variable( array(
	'nom' => 'tri_perso',
	'format' => _format_CHAINE,
	'defaut' => '',
	'code:strlen(%s)' => "define('_TRI_ARTICLES_RUBRIQUE', %s);",
));
add_outil( array(
	'id' => 'tri_articles',
	'code:spip_options' => "%%tri_articles%%%%tri_perso%%",
	'categorie' => 'interface',
	'version-min' => '1.9300',
));

// ici on a besoin de six boutons radio : 'page', 'html', 'propres', 'propres2, ''standard' et 'propres-qs'
define('_CS_PROPRE_QS', defined('_SPIP19300')?'propres_qs':'propres-qs');
add_variable( array(
	'nom' => 'radio_type_urls3',
	'format' => _format_CHAINE,
	'radio' => array('page' => 'couteauprive:url_page',
					 'html' => 'couteauprive:url_html', 
					 'propres' => 'couteauprive:url_propres',
					 'propres2' => 'couteauprive:url_propres2',
					  'arbo'=> 'couteauprive:url_arbo',
					  'standard' => 'couteauprive:url_standard',
					   _CS_PROPRE_QS => 'couteauprive:url_propres-qs'),
	'radio/ligne' => 4,
	'defaut' => "'page'",
	'code' => "\$GLOBALS['type_urls']=%s;\n",
));
add_variable( array(
	'nom' => 'url_arbo_minuscules',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),				
	'defaut' => 1,
	'code' => "define('_url_arbo_minuscules', %s);\n",
));
add_variable( array(
	'nom' => 'urls_arbo_sans_type',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),				
	'defaut' => 0,
	'code' => "define('_urls_arbo_sans_type', %s);\n"
));

add_variable( array(
	'nom' => 'spip_script',
	'format' => _format_CHAINE,
	'defaut' => "get_spip_script()",
	'code' => "define('_SPIP_SCRIPT', %s);\n",
));
add_variable( array(
	'nom' => 'url_arbo_sep_id',
	'format' => _format_CHAINE,
	'defaut' => "'-'",
	'code' => "define('_url_arbo_sep_id', %s);\n",
));
add_variable( array(
	'nom' => 'terminaison_urls_arbo',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code' => "define('_terminaison_urls_arbo', %s);\n",
));
add_outil( array(
	'id' => 'type_urls',
	'code:spip_options' => "%%radio_type_urls3%%%%spip_script%%%%url_arbo_minuscules%%%%url_arbo_sep_id%%%%urls_arbo_sans_type%%%%terminaison_urls_arbo%%",
	'categorie' => 'admin',
));

	// ici on a besoin de trois boutons radio : _T('couteauprive:js_jamais'), _T('couteauprive:js_defaut') et _T('couteauprive:js_toujours')
add_variable( array(
	'nom' => 'radio_filtrer_javascript3',
	'format' => _format_NOMBRE,
	'radio' => array(-1 => 'couteauprive:js_jamais', 0 => 'couteauprive:js_defaut', 1 => 'couteauprive:js_toujours'),
	'defaut' => 0,
	// si la variable est non nulle, on code...
	'code:%s' => "\$GLOBALS['filtrer_javascript']=%s;",
));
add_outil( array(
	'id' => 'filtrer_javascript',
	'code:options' => "%%radio_filtrer_javascript3%%",
	'categorie' => 'admin',
	'version-min' => '1.9200',
));

	// ici on a besoin d'une case input. La variable est : forum_lgrmaxi
	// a la toute premiere activation de l'outil, la valeur sera : 0 (aucune limite)
add_variable( array(
	'nom' => 'forum_lgrmaxi',
	'format' => _format_NOMBRE,
	'defaut' => 0,
	'code:%s' => "define('_FORUM_LONGUEUR_MAXI', %s);",
));
add_outil( array(
	'id' => 'forum_lgrmaxi',
	'code:spip_options' => "%%forum_lgrmaxi%%",
	'categorie' => 'admin',
	'version-min' => '1.9200',
));

add_outil( array(
	'id' => 'auteur_forum',
	'categorie'	 => 'admin',
	'jquery'	=> 'oui',
	'code:options' => "@define('_FORUM_OBLIGE_AUTEUR', 'oui');",
	'pipeline:affichage_final' => 'Auteur_forum_affichage_final',
));

	// ici on a besoin de trois boutons radio : _T('couteauprive:par_defaut'), _T('couteauprive:sf_amont') et _T('couteauprive:sf_tous')
add_variable( array(
	'nom' => 'radio_suivi_forums3',
	'format' => _format_CHAINE,
	'radio' => array('defaut' => 'couteauprive:par_defaut', '_SUIVI_FORUMS_REPONSES' => 'couteauprive:sf_amont', '_SUIVI_FORUM_THREAD' => 'couteauprive:sf_tous'),
	'defaut' => '"defaut"',
	// si la variable est differente de 'defaut' alors on codera le define
	'code:%s!=="defaut"' => "define(%s, true);",
));
add_outil( array(
	'id' => 'suivi_forums',
	'code:options' => "%%radio_suivi_forums3%%",
	'categorie' => 'admin',
	// effectif que dans la version 1.92 (cf : plugin notifications)
	'version-min' => '1.9200',
	'version-max' => '1.9299',
));

add_variable( array(
	'nom' => 'spam_mots',
	'format' => _format_CHAINE,
	'lignes' => 8,
	'defaut' => '"sucking blowjob superbabe ejakulation fucking (asses)"',
	'code' => "define('_spam_MOTS', %s);",
));
add_outil( array(
	'id' => 'spam',
	'code:options' => '%%spam_mots%%',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'no_IP',
	'code:spip_options' => '$ip = substr(md5($ip),0,16);',
	'categorie' => 'admin',
));

add_outil( array(
	'id' => 'flock',
	'code:spip_options' => "define('_SPIP_FLOCK',false);",
	'categorie' => 'admin',
	'version-min' => '1.9300',
));

add_outil( array(
	'id' => 'log_couteau_suisse',
));

add_outil( array(
	'id' => 'xml',
	'code:options' => "\$GLOBALS['xhtml']='sax';",
	'auteur' => 'Ma&iuml;eul Rouquette',
	'categorie' =>'public',
	'version-min' => '1.9200',
));

add_outil( array(
	'id' => 'f_jQuery',
	'code:options' => "\$GLOBALS['spip_pipeline']['insert_head'] = str_replace('|f_jQuery', '', \$GLOBALS['spip_pipeline']['insert_head']);",
	'auteur' => 'Fil',
	'categorie' =>'public',
	'version-min' => '1.9200',
));

add_variable( array(
	'nom' => 'style_p',
	'format' => _format_CHAINE,
	'defaut' => '"spip"',
	'code:strlen(%s)' => ' class=%s',
));
add_variable( array(
	'nom' => 'style_h',
	'format' => _format_CHAINE,
	'defaut' => '"spip"',
	'code:strlen(%s)' => ' class=%s',
));
add_variable( array(
	'nom' => 'racc_hr',
	'format' => _format_CHAINE,
	'defaut' => defined('_SPIP19300')?"'<hr />'":"'<hr class=\"spip\" />'",
	'code:strlen(%s)' => "\$GLOBALS['ligne_horizontale']=%s;\n",
));
add_variable( array(
	'nom' => 'racc_h1',
	'format' => _format_CHAINE,
	'defaut' => defined('_SPIP19300')?"''":"'<h3 class=\"spip\">'",
	'code:strlen(%s)' => "\$GLOBALS['debut_intertitre']=%s;\n",
));
add_variable( array(
	'nom' => 'racc_h2',
	'format' => _format_CHAINE,
	'defaut' => defined('_SPIP19300')?"''":"'</h3>'",
	'code:strlen(%s)' => "\$GLOBALS['fin_intertitre']=%s;\n",
));
add_variable( array(
	'nom' => 'racc_i1',
	'format' => _format_CHAINE,
	'defaut' => '',
	'code:strlen(%s)' => "\$GLOBALS['debut_italique']=%s;\n",
));
add_variable( array(
	'nom' => 'racc_i2',
	'format' => _format_CHAINE,
	'defaut' => '',
	'code:strlen(%s)' => "\$GLOBALS['fin_italique']=%s;\n",
));
add_variable( array(
	'nom' => 'puce',
	'format' => _format_CHAINE,
	'defaut' => '"AUTO"',
	'code:strlen(%s)' => "\$GLOBALS['puce']=%s;",
));
add_outil( array(
	'id' => 'class_spip',
	'code:options' => "\$GLOBALS['class_spip']='%%style_p%%';\n\$GLOBALS['class_spip_plus']='%%style_h%%';\n%%racc_hr%%%%racc_h1%%%%racc_h2%%%%racc_i1%%%%racc_i2%%%%puce%%",
	'categorie' => 'public',
	'description' => 
	// avant SPIP 1.93 : <hr/> seulement
	// et apres : <hr/> + puce
		(!defined('_SPIP19300')?'<:class_spip:1:>':'<:class_spip:2:>').
	// des SPIP 1.91 : les intertitres
		'<:class_spip:3:>'.
	// des SPIP 1.93 : les italiques + les styles
		(!defined('_SPIP19300')?'':'<:class_spip:4:>'),
));

add_variable( array(
	'nom' => 'admin_travaux',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'couteauprive:tous', 1 => 'couteauprive:sauf_admin'),
	'defaut' => 0,
	'code' => "define('_en_travaux_ADMIN', %s);\n",
));
add_variable( array(
	'nom' => 'message_travaux',
	'format' => _format_CHAINE,
	'defaut' => "_T('couteauprive:travaux_prochainement')",
	'lignes' => 3,
	'code' => "define('_en_travaux_MESSAGE', %s);\n",
));
add_variable( array(
	'nom' => 'titre_travaux',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'couteauprive:travaux_titre', 0 => 'couteauprive:travaux_nom_site'),
	'defaut' => 1,
	'code:%s' => "define('_en_travaux_TITRE', %s);",
));
add_outil( array(
	'id' => 'en_travaux',
	'code:options' => "%%message_travaux%%%%admin_travaux%%%%titre_travaux%%",
	'categorie' => 'admin',
	'auteur' => "Arnaud Ventre pour l'id&eacute;e originale",
));

add_variable( array(
	'nom' => 'cs_rss',
	'check' => 'couteauprive:rss_var',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_CS', %s);\n",
));
add_variable( array(
	'nom' => 'format_spip',
	'check' => 'couteauprive:format_spip',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_ARTICLES', %s);\n",
));
add_variable( array(
	'nom' => 'stat_auteurs',
	'check' => 'couteauprive:stat_auteurs',
	'defaut' => 1,
	'code:%s' => "define('boites_privees_AUTEURS', %s);\n",
));
add_outil( array(
	'id' => 'boites_privees',
	'auteur'=>'Pat, Joseph LARMARANGE (format SPIP)',
	'contrib' => 2564,
	'code:options' => "%%cs_rss%%%%format_spip%%%%stat_auteurs%%",
	'categorie' => 'interface',
	'pipeline:affiche_milieu' => 'boites_privees_affiche_milieu',
	'pipeline:affiche_droite' => 'boites_privees_affiche_droite',
));

add_variable( array(
	'nom' => 'max_auteurs_page',
	'format' => _format_NOMBRE,
	'defaut' => 30,
	'code:%s' => "@define('MAX_AUTEURS_PAR_PAGE', %s);\n",
));
add_variable( array(
	'nom' => 'auteurs_0',	'check' => 'info_administrateurs',	'defaut' => 1,	'code:%s' => "'0minirezo',",
));
add_variable( array(
	'nom' => 'auteurs_1',	'check' => 'info_redacteurs',	'defaut' => 1,	'code:%s' => "'1comite',",
));
add_variable( array(
	'nom' => 'auteurs_5',	'check' => 'info_statut_site_4',	'defaut' => 1,	'code:%s' => "'5poubelle',",
));
add_variable( array(
	'nom' => 'auteurs_6',	'check' => 'info_visiteurs',	'defaut' => 0,	'code:%s' => "'6forum',",
));
add_variable( array(
	'nom' => 'auteurs_n',	'check' => 'couteauprive:nouveaux',	'defaut' => 0,	'code:%s' => "'nouveau',",
));
add_variable( array(
	'nom' => 'auteurs_tout_voir',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'couteauprive:statuts_tous', 0 => 'couteauprive:statuts_spip'),
	'radio/ligne' => 1,
	'defaut' => 0,
//	'code:!%s' => "@define('AUTEURS_DEFAUT', join(\$temp_auteurs,','));",
	'code:!%s' => "if (_request('exec')=='auteurs' && !_request('statut')) \$_GET['statut'] = join(\$temp_auteurs,',');",
	'code:%s' => "if (_request('exec')=='auteurs' && !_request('statut')) \$_GET['statut'] = '!foo';",
));
add_outil( array(
	'id' => 'auteurs',
	'code:options' => "%%max_auteurs_page%%\$temp_auteurs=array(%%auteurs_0%%%%auteurs_1%%%%auteurs_5%%%%auteurs_6%%%%auteurs_n%%); %%auteurs_tout_voir%% unset(\$temp_auteurs);",
	'categorie' => 'interface',
	'version-min' => '1.9300',
//	'pipeline:affiche_milieu' => 'auteurs_affiche_milieu',
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'verstexte',
	'auteur' => 'C&eacute;dric MORIN',
	'categorie' => 'spip',
));

add_outil( array(
	'id' => 'orientation',
	'auteur' 	 => 'Pierre Andrews (Mortimer) &amp; IZO',
	'categorie' => 'spip',
));

add_variable( array(
	'nom' => 'balise_decoupe',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_decoupe_BALISE', %s);\n",
));
add_outil( array(
	'id' => 'decoupe',
	'contrib'	=> 2135,
	'code:options' => "%%balise_decoupe%%define('_onglets_FIN', '<span class=\'_fooonglets\'></span>');\n@define('_decoupe_SEPARATEUR', '++++');
if (isset(\$_GET['var_recherche'])) {
	include_spip('inc/headers');
	redirige_par_entete(str_replace('var_recherche=', 'decoupe_recherche=', \$GLOBALS['REQUEST_URI']));
}",
	'code:css' => "div.pagination {display:block; text-align:center; }
div.pagination img { border:0pt none; margin:0pt; padding:0pt; }",
	// construction des onglets
	'code:jq_init' => "onglets_init.apply(this);",
	// inserer : $table_des_traitements['TEXTE'][]= 'cs_decoupe(propre(%s))';
	'traitement:TEXTE:post_propre' => 'cs_decoupe',
	'traitement:TEXTE:pre_propre' => 'cs_onglets',
	// sans oublier les articles : $table_des_traitements['TEXTE']['articles']= 'cs_decoupe(propre(%s))';
	'traitement:TEXTE/articles:post_propre' => 'cs_decoupe',
	'traitement:TEXTE/articles:pre_propre' => 'cs_onglets',
	'categorie' => 'typo-racc',
	'pipeline:bt_toolbox' => 'decoupe_BarreTypo',
	'pipeline:nettoyer_raccourcis_typo' => 'decoupe_nettoyer_raccourcis',
));

// couplage avec l'outil 'decoupe', donc 'sommaire' doit etre place juste apres :
// il faut inserer le sommaire dans l'article et ensuite seulement choisir la page
add_variable( array(
	'nom' => 'lgr_sommaire',
	'format' => _format_NOMBRE,
	'defaut' => 30,
	'code:%s>=9 && %s<=99' => "define('_sommaire_NB_CARACTERES', %s);\n",
));
add_variable( array(
	'nom' => 'auto_sommaire',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 1,
	'code:%s' => "define('_sommaire_AUTOMATIQUE', %s);\n",
));
add_variable( array(
	'nom' => 'balise_sommaire',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code:%s' => "define('_sommaire_BALISE', %s);",
));
include_spip('inc/filtres');
$path = url_absolue(find_in_path(defined('_SPIP19100')?"img_pack/spip_out.gif":"images/spip_out.gif"));
add_outil( array(
	'id' => 'sommaire',
	'contrib'	=> 2378,
	'code:options' => "define('_sommaire_REM', '<span class=\'_foosommaire\'></span>');\ndefine('_sommaire_SANS_SOMMAIRE', '[!sommaire]');\ndefine('_sommaire_AVEC_SOMMAIRE', '[sommaire]');\n%%lgr_sommaire%%%%auto_sommaire%%%%balise_sommaire%%",
	// s'il y a un sommaire, on cache la navigation haute sur les pages
	'code:css' => "div.cs_sommaire {display:block; float:right; margin-left:1em; margin-right:0.4em; overflow:auto; z-index:100; max-height:350px; text-align:left;}
a.sommaire_ancre {background:transparent url($path) no-repeat scroll left center; padding-left:10px; text-decoration:none; }
div.cs_sommaire a:after {display:none;}",
	'code:jq' => 'if(jQuery("div.cs_sommaire").length) jQuery("div.decoupe_haut").css("display", "none");',
	// inserer : $table_des_traitements['TEXTE']['article']= 'sommaire_d_article(propre(%s))';
	'traitement:TEXTE/articles:post_propre' => 'sommaire_d_article',
	'traitement:CS_SOMMAIRE:post_propre' => 'sommaire_d_article_balise',
	'categorie' => 'typo-corr',
	'pipeline:nettoyer_raccourcis_typo' => 'sommaire_nettoyer_raccourcis',
));

//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction a revoir ?
add_outil( array(
	'id' => 'desactiver_flash',
	'auteur' 	 => 'C&eacute;dric MORIN',
	'categorie'	 => 'public',
	'jquery'	=> 'oui',
	// fonction InhibeFlash_init() codee dans desactiver_flash.js : executee lors du chargement de la page et a chaque hit ajax
	'code:jq_init' => 'InhibeFlash_init.apply(this);',
));

add_variable( array(
	'nom' => 'radio_target_blank3',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 0,
	'code' => '$GLOBALS["tweak_target_blank"]=%s;',
));
add_variable( array(
	'nom' => 'url_glossaire_externe2',
	'format' => _format_CHAINE,
	'defaut' => '""',
	'code:strlen(%s)' => '$GLOBALS["url_glossaire_externe"]=%s;',
));
add_outil( array(
	'id' => 'SPIP_liens',
	'categorie' => 'public',
	'contrib'	=> 2443,
	'jquery'	=> 'oui',
	'code:options' => "%%radio_target_blank3%%\n%%url_glossaire_externe2%%",
	'code:jq_init' => 'if (%%radio_target_blank3%%) { jQuery("a.spip_out,a.spip_url,a.spip_glossaire",this).attr("target", "_blank"); }',
));

//-----------------------------------------------------------------------------//
//                               NOISETTES                                     //
//-----------------------------------------------------------------------------//

add_outil( array(
	'id' => 'visiteurs_connectes',
	'auteur' => "Phil d'apr&egrave;s spip-contrib",
	'categorie' => 'public',
	'code:options' => "define('_VISITEURS_CONNECTES',1);
function cs_compter_visiteurs(){ return count(preg_files(_DIR_TMP.'visites/','.')); }
function action_visiteurs_connectes(){ echo cs_compter_visiteurs(); return true; }",
	'version-min' => '1.9200', // pour la balise #ARRAY
	//	une mise a jour toutes les 120 sec ?
/*	'code:js' => 'function Timer_visiteurs_connectes(){
		jQuery("span.cs_nb_visiteurs").load("spip.php?action=visiteurs_connectes");
		setTimeout("Timer_visiteurs_connectes()",120000);					
}',
	'code:jq' => ' if(jQuery("span.cs_nb_visiteurs").length) Timer_visiteurs_connectes(); ',
	'jquery' => 'oui',*/
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
	'auteur' 	 => "J&eacute;r&ocirc;me Combaz pour l'id&eacute;e originale",
	'categorie'	 => 'typo-corr',
	'pipeline:pre_typo' => 'pucesli_pre_typo',
));

add_variable( array(
	'nom' => 'decoration_styles',
	'format' => _format_CHAINE,
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
	'auteur' 	 => 'izo@aucuneid.net, Pat',
	'contrib'	=> 2427,
	'categorie'	 => 'typo-racc',
	'code:options' => "%%decoration_styles%%",
	'pipeline:pre_typo' => 'decoration_pre_typo',
	'pipeline:bt_toolbox' => 'decoration_BarreTypo',
));

add_variable( array(
	'nom' => 'couleurs_fonds',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non' ),
	'defaut' => 1,
	'code' => "define('_COULEURS_FONDS', %s);\n",
));
add_variable( array(
	'nom' => 'set_couleurs',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'couteauprive:toutes_couleurs', 1 => 'couteauprive:certaines_couleurs'),
	'radio/ligne' => 1,
	'defaut' => 0,
	'code' => "define('_COULEURS_SET', %s);\n",
));
add_variable( array(
	'nom' => 'couleurs_perso',
	'format' => _format_CHAINE,
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
	'pipeline:bt_toolbox' => 'couleurs_BarreTypo',
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
	'auteur' 	 => 'Vincent Ramos, Pat',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 1564,
	'pipeline:post_typo' => 'typo_exposants',
	'code:css' => 'sup.typo_exposants { font-size:75%; font-variant:normal; vertical-align:super; }',
));

add_outil( array(
	'id' => 'guillemets',
	'auteur' 	 => 'Vincent Ramos',
	'categorie'	 => 'typo-corr',
	'pipeline:post_typo' => 'typo_guillemets',
));

add_variable( array(
	'nom' => 'liens_interrogation',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),
	'defaut' => 1,
	'code:%s' => "\$GLOBALS['liens_interrogation']=true;\n",
));
add_variable( array(
	'nom' => 'liens_orphelins',
	'format' => _format_NOMBRE,
	'radio' => array(-1 => 'item_non', 0 => 'couteauprive:basique', 1 => 'couteauprive:etendu'),
	'defaut' => 0,
	'code' => '$GLOBALS["liens_orphelins"]=%s;',
));
// attention : liens_orphelins doit etre place avant mailcrypt
add_outil( array(
	'id' => 'liens_orphelins',
	'categorie'	 => 'typo-corr',
	'contrib'	=> 2443,
	'code:options' => '%%liens_interrogation%%%%liens_orphelins%%',
	'pipeline:pre_propre' => 'liens_orphelins_pipeline',
	'traitement:EMAIL' => 'expanser_liens(liens_orphelins',
 	'pipeline:pre_typo'   => 'interro_pre_typo',
 	'pipeline:post_propre'   => 'interro_post_propre',
));

add_outil( array(
	'id' => 'filets_sep',
	'auteur' 	 => 'FredoMkb',
	'categorie'	 => 'typo-racc',
	'contrib'	=> 1563,
	'pipeline:pre_typo' => 'filets_sep',
	'pipeline:bt_toolbox' => 'filets_sep_BarreTypo',
));

add_outil( array(
	'id' => 'smileys',
	'auteur' 	 => "Sylvain pour l'id&eacute;e originale",
	'categorie'	 => 'typo-corr',
	'contrib'	=> 1561,
	'code:css' => "table.cs_smileys td {text-align:center; font-size:90%; font-weight:bold;}",
	'pipeline:pre_typo' => 'cs_smileys_pre_typo',
	'pipeline:bt_toolbox' => 'cs_smileys_BarreTypo',
));

add_outil( array(
	'id' => 'chatons',
	'auteur' 	 => "BoOz pour l'id&eacute;e originale",
	'categorie'	 => 'typo-racc',
	'pipeline:pre_typo' => 'chatons_pre_typo',
	'pipeline:bt_toolbox' => 'chatons_BarreTypo',
));

add_variable( array(
	'nom' => 'glossaire_groupes',
	'format' => _format_CHAINE,
	'defaut' => "'Glossaire'",
	'code' => "\$GLOBALS['glossaire_groupes']=%s;\n",
));
add_variable( array(
	'nom' => 'glossaire_limite',
	'format' => _format_NOMBRE,
	'defaut' => 0,
	'code:%s>0' => "define('_GLOSSAIRE_LIMITE', %s);\n",
));
add_variable( array(
	'nom' => 'glossaire_js',
	'radio' => array(0 => 'couteauprive:glossaire_css', 1 => 'couteauprive:glossaire_js'),
	'format' => _format_NOMBRE,
	'defaut' => 1,
	'code:%s' => "define('_GLOSSAIRE_JS', %s);",
));
add_outil( array(
	'id' => 'glossaire',
	'categorie'	=> 'typo-corr',
	'contrib'	=> 2206,
	'code:options' => "%%glossaire_limite%%%%glossaire_groupes%%%%glossaire_js%%",
//	'traitement:LIEU:post_propre' => 'cs_glossaire',
	'traitement:TEXTE:post_propre' => 'cs_glossaire',
	// sans oublier les articles...
	'traitement:TEXTE/articles:post_propre' => 'cs_glossaire',
	// et le chapo des articles...
	'traitement:CHAPO:post_propre' => 'cs_glossaire',
	// Precaution pour les articles virtuels
	'traitement:CHAPO:pre_propre' => 'nettoyer_chapo',
	'code:css' =>  'a.cs_glossaire:after {display:none;}',
	// fonction glossaire_init() codee dans glossaire.js : executee lors du chargement de la page et a chaque hit ajax
	'code:jq_init' => 'glossaire_init.apply(this);',
));

// attention : mailcrypt doit etre place apres liens_orphelins
add_outil( array(
	'id' => 'mailcrypt',
	'categorie'	=> 'typo-corr',
	'auteur' 	=> "Alexis Roussel, Paolo, Pat",
	'contrib'	=> 2443,
	'jquery'	=> 'oui',
	'pipelinecode:post_propre' => "if(strpos(\$flux, '@')!==false) \$flux=cs_echappe_balises('', 'mailcrypt', \$flux);",
	'code:js' => "function lancerlien(a,b){ x='ma'+'ilto'+':'+a+'@'+b; return x; }",
	// jQuery pour remplacer l'arobase image par l'arobase texte
	'code:jq_init' => "jQuery('span.spancrypt', this).attr('class','cryptOK').html('&#6'+'4;');",
	'code:css' => 'span.spancrypt {background:transparent url(' . url_absolue(find_in_path('img/mailcrypt/leure.gif'))
		. ') no-repeat scroll 0.1em center; padding-left:12px; text-decoration:none;}',
	'traitement:EMAIL' => 'mailcrypt',
)); 

// attention : liens_en_clair doit etre place apres tous les outils traitant des liens
add_outil( array(
	'id' => 'liens_en_clair',
	'categorie'	 => 'spip',
	'contrib'	=> 2443,
	'pipeline:post_propre' => 'liens_en_clair_post_propre',
	'code:css' => 'a.spip_out:after {display:none;}',
)); 

add_outil( array(
	'id' => 'blocs',
	'categorie'	=> 'typo-racc',
	'contrib' => 2583,
	// fonction blocs_init() codee dans blocs.js : executee lors du chargement de la page et a chaque hit ajax
	'code:jq_init' => 'blocs_init.apply(this);',
	'jquery'	=> 'oui',
	'pipeline:pre_typo' => 'blocs_pre_typo',
	'pipeline:bt_toolbox' => 'blocs_BarreTypo',
));

add_variable( array(	// variable utilisee par 'pipelinecode:insert_head'
	'nom' => 'scrollTo',
	'check' => 'couteauprive:scrollTo',
	'defaut' => 1,
	'format' => _format_NOMBRE,
));
add_variable( array(	// variable utilisee par 'pipelinecode:insert_head'
	'nom' => 'LocalScroll',
	'check' => 'couteauprive:LocalScroll',
	'defaut' => 1,
	'format' => _format_NOMBRE,
));
add_outil( array(
	'id' => 'soft_scroller',
	'categorie'	=> 'public',
	'jquery'	=> 'oui',
	'pipelinecode:insert_head' => 'if(%%scrollTo%%) {$flux.=\'<script src="'.url_absolue(find_in_path("outils/jquery.scrollto.js")).'" type="text/javascript"></script>\'."\n";}
if(%%LocalScroll%%) {$flux.=\'<script src="'.url_absolue(find_in_path("outils/jquery.localscroll.js")).'" type="text/javascript"></script>\'."\n";}',
	'code:js' => 'function soft_scroller_init() { if(typeof jQuery.localScroll=="function") jQuery.localScroll({hash: true}); }',
	'code:jq_init' => 'soft_scroller_init.apply(this);',
));

// http://www.malsup.com/jquery/corner/
add_variable( array(
	'nom' => 'jcorner_classes',
	'format' => _format_CHAINE,
	'lignes' => 10,
	'defaut' => defined('_SPIP19300')?'"// coins ronds aux formulaires
.formulaire_inscription, .formulaire_forum, .formulaire_ecrire_auteur

// colorisation de la dist de SPIP 2.0 en ajoutant un parent
\".chapo, .texte\" = wrap(\'<div style=\"padding:4px; background-color:#ffe0c0; margin:4px 0;\"></div>\') .parent() .addClass(\'jc_parent\')
\".menu\" = wrap(\'<div style=\"padding:4px; background-color:lightBlue; margin:4px 0;\"></div>\') .parent() .addClass(\'jc_parent\')

// coins ronds aux parents !
.jc_parent"'
		:'" // coins ronds pour les menus de navigation
.rubriques, .breves, .syndic, .forums, .divers

 // en couleurs sur l\'accueil
.liste-articles li .texte = css(\'background-color\', \'#E0F0F0\') .corner()

// colorisation de la dist de SPIP 1.92 en ajoutant un parent
\"#contenu .texte\" = wrap(\'<div style=\"padding:4px; background-color:#E0F0F0; margin:4px 0;\"></div>\') .parent() .addClass(\'jc_parent\')

// coins ronds aux parents !
.jc_parent"',
	'code' => "define('_jcorner_CLASSES', %s);",
));
add_variable( array(	// variable utilisee par 'pipelinecode:insert_head'
	'nom' => 'jcorner_plugin',
	'check' => 'couteauprive:jcorner_plugin',
	'defaut' => 1,
	'format' => _format_NOMBRE,
));
add_outil( array(
	'id' => 'jcorner',
	'categorie'	=> 'public',
	'jquery'	=> 'oui',
	'code:options' => "%%jcorner_classes%%",
	'pipelinecode:insert_head' => 'if(%%jcorner_plugin%%) {$flux.=\'<script src="'.url_absolue(find_in_path("outils/jquery.corner.js")).'" type="text/javascript"></script>\'."\n";}',
	'pipeline:insert_head' => 'jcorner_insert_head',
	// jcorner_init() n'est disponible qu'en partie publique
	'code:jq_init' => 'if(typeof jcorner_init=="function") jcorner_init.apply(this);',
));


add_variable( array(
	'nom' => 'insertions',
	'format' => _format_CHAINE,
	'lignes' => 8,
	'defaut' => '"coeur = c&oelig;ur
manoeuvre = man&oelig;uvre
(oeuvre(s?|r?)) = &oelig;uvre$1
(O(E|e)uvre(s?|r?)\b/ = &OElig;uvre$2
((h|H)uits) = $1uit
/\b(c|C|m.c|M.c|rec|Rec)onn?aiss?a(nce|nces|nt|nts|nte|ntes|ble)\b/ = $1onnaissa$2
/\boeuf(s?)\b/ = &oelig;uf$1
"',
	'code' => "define('_insertions_LISTE', %s);",
));
add_outil( array(
	'id' => 'insertions',
	'categorie'	 => 'typo-corr',
	'code:options' => "%%insertions%%",
	'traitement:TEXTE:pre_propre' => 'insertions_pre_propre',
	'traitement:TEXTE/articles:pre_propre' => 'insertions_pre_propre',
));

// Ajout des outils personnalises
if(isset($GLOBALS['mes_outils']))
	foreach($GLOBALS['mes_outils'] as $id=>$outil) {
		$outil['id'] = $id;
		if(strlen($outil['nom'])) $outil['nom'] = "<i>$outil[nom]</i>";
		add_outil($outil);
	}

// Idees d'ajouts :
// http://archives.rezo.net/spip-core.mbox/
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS et d'autres balises #MAINTENANT #LESADMINISTRATEURS #LESREDACTEURS #LESVISITEURS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles
// http://www.spip-contrib.net/Generation-automatique-de
// Les sessions
// colorations du code
// boutonstexte

//global $cs_variables; cs_log($cs_variables, 'cs_variables :');
?>
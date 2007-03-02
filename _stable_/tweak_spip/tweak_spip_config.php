<?php

#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

//-----------------------------------------------------------------------------//
//                               options                                       //
//-----------------------------------------------------------------------------//
/*
add_tweak( array(
	'id'	=> 'revision_nbsp',
	'code' 	=> '$GLOBALS["activer_revision_nbsp"] = true; $GLOBALS["test_i18n"] = true ;',
	'categorie'	=> 'admin',
	'options'	=> 1,
));
*/
add_tweak( array(
	'id'	=> 'desactive_cache',
	'code' 	=> '$_SERVER["REQUEST_METHOD"]="POST";',
	'auteur'	=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie'	=> 'admin',
	'options'	=> 1,
));

add_tweak( array(
	'id'	=> 'quota_cache',
	// ici on demande a Tweak Spip une case input. La variable est : quota_cache
	// par defaut, la valeur sera $GLOBALS["quota_cache"]
	// le /s demande a Tweak Spip de traiter la variable comme un nombre.
	'code' 	=> '$GLOBALS["quota_cache"]=%%quota_cache/d/$GLOBALS["quota_cache"]%%;',
	'categorie'	=> 'admin',
	'options'	=> 1,
));

add_tweak( array(
	'id'	=> 'dossier_squelettes',
	// ici on demande a Tweak Spip une case input. La variable est : dossier_squelettes
	// par defaut, la valeur sera $GLOBALS["dossier_squelettes"]
	// le /s demande a Tweak Spip de traiter la variable comme une chaine.
	'code' 	=> '$GLOBALS["dossier_squelettes"]=%%dossier_squelettes/s/$GLOBALS["dossier_squelettes"]%%;',
	'categorie'	=> 'admin',
	'options'	=> 1,
));
add_tweak( array(
	'id'	=> 'cookie_prefix',
	// ici on demande a Tweak Spip une case input. La variable est : dossier_squelettes
	// par defaut, la valeur sera $GLOBALS["cookie_prefix"]
	// le /s demande a Tweak Spip de traiter la variable comme une chaine.
	'code' 	=> '$GLOBALS["cookie_prefix"]=%%cookie_prefix/s/$GLOBALS["cookie_prefix"]%%;',
	'categorie'	=> 'admin',
	'options'	=> 1,
));
add_tweak( array(
	'id'	=> 'supprimer_numero',
	'code' 	=> '$GLOBALS["table_des_traitements"]["TITRE"][]= "typo(supprimer_numero(%s))";
$GLOBALS["table_des_traitements"]["NOM"][]="typo(supprimer_numero(%s))";',
	'categorie'	=> 'squel',
	'options'	=> 1
));

add_tweak( array(
	'id'	=> 'paragrapher',
	'code'	=> '$GLOBALS["toujours_paragrapher"]=true;',
	'categorie'	=> 'admin',
	'options'	=> 1
));

add_tweak( array(
	'id'	=> 'forcer_langue',
	'code'	=> '$GLOBALS["forcer_lang"]=true;', 
	'categorie'	=> 'squel',
	'options'	=> 1
));

add_tweak( array(
	'id'	=> 'insert_head',
	'code'	=> '$spip_pipeline["affichage_final"] .= "|f_insert_head";', 
	'categorie'	=> 'squel',
	'options'	=> 1,
	'version-min'	=> 1.92,
));

add_tweak( array(
	'id'	=> 'set_options',
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	// ici on demande a Tweak Spip deux boutons radio : _T('icone_interface_simple') et _T('icone_interface_complet')
	// pour les boutons radio, il faut utiliser une deuxi�me variable avec le prefixe radio_ : radio_set_options
	// la variable Spip est : set_options
	// le /avancees signifie que avancees (traduit par : _T('icone_interface_complet')) sera coche par defaut
	// le /s demande a Tweak Spip de traiter la variable comme une chaine.
	'code' 	=> '$GLOBALS["radio_set_options"]=%%radio_set_options/s/"avancees(basiques=icone_interface_simple|avancees=icone_interface_complet)"%%;
list($set_options, $foo) = explode("(", $GLOBALS["radio_set_options"], 2);
$_GET["set_options"] = $GLOBALS["set_options"] = $set_options;',
	'categorie'	=> 'admin',
	'options'	=> 1,
	// pipeline pour retirer en javascript le bouton de controle de l'interface
	'pipeline:header_prive' => 'set_options_header_prive',
	// non supporte a partir de la version 1.9.3
	'version-max'	=> 1.93,
));

add_tweak( array(
	'id'	=> 'filtrer_javascript',
	// ici on demande a Tweak Spip trois boutons radio : _T('tweak:js_jamais'), _T('tweak:js_defaut') et _T('tweak:js_toujours')
	// pour les boutons radio, il faut utiliser une deuxi�me variable avec le prefixe radio_ : radio_set_options
	// la variable Spip est : set_options
	// le /defaut signifie que avancees (traduit par : _T('tweak:js_defaut')) sera coche par defaut
	// le /s demande a Tweak Spip de traiter la variable comme une chaine.
	'code' 	=> '$GLOBALS["radio_filtrer_javascript2"]=%%radio_filtrer_javascript2/s/"0(-1=tweak:js_jamais|0=tweak:js_defaut|1=tweak:js_toujours)"%%;
list($GLOBALS["filtrer_javascript"], $foo) = explode("(", $GLOBALS["radio_filtrer_javascript2"], 2);',
	'categorie'	=> 'admin',
	'options'	=> 1,
	'version-min'	=> 1.92,
));

add_tweak( array(
	'id'	=> 'log_tweaks',
	'code' 	=> '$GLOBALS["log_tweaks"]=true;',
//	'categorie'	=> 'admin',
	'options'	=> 1,
));

add_tweak( array (
	'id' => 'xml',
	'code'		=> '$xhtml = "sax";',
	'auteur' => 'Ma&iuml;eul Rouquette (maieulrouquette@tele2.fr)',
	'categorie' =>'squel',
	'version-min'	=> '1.92',
	'options'	=>	1
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'id'	=> 'verstexte',
	'auteur' 	=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie'	=> 'squel',
	'fonctions'	=> 1,
));

add_tweak( array(
	'id'	=> 'orientation',
	'auteur' 		=> 'Pierre Andrews (Mortimer) &amp; IZO',
	'categorie'	=> 'squel',
	'fonctions'		=> 1,
));


//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction a revoir ?
add_tweak( array(
	'id'	=> 'desactiver_flash',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie'		=> 'squel',
	'pipeline:affichage_final' => 'InhibeFlash_affichage_final',
));

//-----------------------------------------------------------------------------//
//                               TYPO                                          //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'id'	=> 'toutmulti',
	'categorie'		=> 'typo',
	'pipeline:pre_typo'	=> 'ToutMulti_pre_typo',
));

add_tweak( array(
	'id'	=> 'pucesli',
	'auteur' 		=> '[J&eacute;r&ocirc;me Combaz->http://conseil-recherche-innovation.net/index.php/2000/07/08/72-jerome-combaz]',
	'categorie'		=> 'typo',
	'pipeline:pre_typo' => 'pucesli_pre_typo',
));	

add_tweak( array(
	'id'	=> 'decoration',
	'auteur' 		=> '[izo@aucuneid.net->http://www.aucuneid.com/bones]',
	'categorie'		=> 'typo',
	'pipeline:pre_typo' => 'decoration_pre_typo',
));

// tweak specifiquement fran�ais. D'autres langues peuvent etre ajoutees dans tweaks/typo_exposants.php
add_tweak( array(
	'id'	=> 'typo_exposants',
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'categorie'		=> 'typo',
	'pipeline:post_typo'	=> 'typo_exposants',
));

add_tweak( array(
	'id'	=> 'guillemets',
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'categorie'		=> 'typo',
	'pipeline:post_typo'	=> 'typo_guillemets',
));

add_tweak( array(
	'id'	=> 'filets_sep',
	'auteur' 		=> 'FredoMkb',
	'categorie'		=> 'typo',
	'pipeline:pre_typo'	=> 'filets_sep',
));

add_tweak( array(
	'id'	=> 'smileys',
	'auteur' 		=> 'Sylvain',
	'categorie'		=> 'typo',
	'pipeline:pre_typo'	=> 'tweak_smileys_pre_typo',
));

add_tweak( array(
	'id'	=> 'chatons',
	'auteur' 		=> 'BoOz (booz.bloog@laposte.net)',
	'categorie'		=> 'typo',
	'pipeline:pre_typo'	=> 'chatons_pre_typo',
));

// Id�es d'ajouts :
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS et d'autres balises #MAINTENANT #LESADMINISTRATEURS #LESREDACTEURS #LESVISITEURS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles
// Un Sommaire. voir :     
//		$GLOBALS['debut_intertitre'] = "<h3 class='mon_style_h3'>";
//		$GLOBALS['fin_intertitre'] = "</h3>";
// http://www.spip-contrib.net/Generation-automatique-de
// Les sessions

tweak_log("tweak_spip_config");
?>
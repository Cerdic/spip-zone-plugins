<?php

#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
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
//	'code' 	=> '$GLOBALS["quota_cache"]=10;',
	'code' 	=> '$GLOBALS["quota_cache"]=%%quota_cache/d/$GLOBALS["quota_cache"]%%;',
	'categorie'	=> 'admin',
	'options'	=> 1,
));

add_tweak( array(
	'id'	=> 'dossier_squelettes',
	'code' 	=> '$GLOBALS["dossier_squelettes"]=%%dossier_squelettes/s/$GLOBALS["dossier_squelettes"]%%;',
	'categorie'	=> 'admin',
	'options'	=> 1,
));

add_tweak( array(
	'id'	=> 'supprimer_numero',
	'code' 	=> '$GLOBALS["table_des_traitements"]["TITRE"][]= "typo(supprimer_numero(%s))";',
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
	'categorie'	=> 'admin',
	'options'	=> 1
));

add_tweak( array(
	'id'	=> 'insert_head',
	'code'	=> '$spip_pipeline["affichage_final"] .= "|f_insert_head";', 
	'categorie'	=> 'squel',
	'options'	=> 1,
	'version'	=> 1.92,
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
	'categorie'		=> 'admin',
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

// tweak specifiquement français. D'autres langues peuvent etre ajoutees dans tweaks/typo_exposants.php
add_tweak( array(
	'id'	=> 'typo_exposants',
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'categorie'		=> 'typo',
	'pipeline:post_typo'	=> 'typo_exposants',
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

// Idées d'ajouts :
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
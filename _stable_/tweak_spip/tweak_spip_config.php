<?php

#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

global $tweaks;
$tweaks = array();

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
	'id'	=> 'supprimer_numero',
	'code' 	=> '$GLOBALS["table_des_traitements"]["TITRE"][]= "typo(supprimer_numero(%s))";',
	'categorie'	=> 'admin',
	'options'	=> 1
));

add_tweak( array(
	'id'	=> 'paragrapher',
	'code'	=> '$GLOBALS["toujours_paragrapher"]=true;',
	'categorie'	=> 'admin',
	'options'	=> 1
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

// TODO : gestion du jQuery dans la fonction à revoir ?
add_tweak( array(
	'id'	=> 'desactiver_flash',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie'		=> 'admin',
	// pipeline => fonction
	'affichage_final' => 'InhibeFlash_affichage_final',
));

//-----------------------------------------------------------------------------//
//                               TYPO                                          //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'id'	=> 'toutmulti',
	'categorie'		=> 'typo',
	// pipeline => fonction
	'pre_typo'	=> 'ToutMulti_pre_typo',
));

add_tweak( array(
	'id'	=> 'bellespuces',
	'auteur' 		=> '[J&eacute;r&ocirc;me Combaz->http://conseil-recherche-innovation.net/index.php/2000/07/08/72-jerome-combaz]',
	'categorie'		=> 'typo',
	// pipeline => fonction
	'pre_typo' => 'bellespuces_pre_typo',
));	

add_tweak( array(
	'id'	=> 'decoration',
	'auteur' 		=> '[izo@aucuneid.net->http://www.aucuneid.com/bones]',
	'categorie'		=> 'typo',
	// pipeline => fonction
	'pre_typo' => 'decoration_pre_typo',
));

// tweak specifiquement français. D'autres langues peuvent etre ajoutees dans inc/typo_exposants.php
// TODO : le dire sur spip-contrib
add_tweak( array(
	'id'	=> 'typo_exposants',
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'categorie'		=> 'typo',
	// pipeline => fonction
	'post_typo'	=> 'typo_exposants',
));

add_tweak( array(
	'id'	=> 'filets_sep',
	'auteur' 		=> 'FredoMkb',
	'categorie'		=> 'typo',
	// pipeline => fonction
	'pre_typo'	=> 'filets_sep',
));

add_tweak( array(
	'id'	=> 'smileys',
	'auteur' 		=> 'Sylvain',
	'categorie'		=> 'typo',
	// pipeline => fonction
	'pre_typo'	=> 'tweak_smileys',
));

// Idées d'ajouts :
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS et d'autres balises #MAINTENANT #LESADMINISTRATEURS #LESREDACTEURS #LESVISITEURS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles
// Les sommaires. voir :     
//		$GLOBALS['debut_intertitre'] = "<h3 class='mon_style_h3'>";
//		$GLOBALS['fin_intertitre'] = "</h3>";
// http://www.spip-contrib.net/Generation-automatique-de

//-----------------------------------------------------------------------------//
//                        activation des tweaks                                //
//-----------------------------------------------------------------------------//

// exclure ce qui n'est pas un pipeline...
global $tweak_exclude;
$tweak_exclude = array('id', 'nom', 'description', 'auteur', 'categorie', 'code', 'options', 'fonctions', 'actif');

// lire les metas et initialiser : $tweaks_pipelines, $tweaks_css
global $tweaks_pipelines, $tweaks_css;
tweak_initialisation();

// print_r($tweaks); print_r($tweaks_pipelines); print_r($tweaks_css);

?>
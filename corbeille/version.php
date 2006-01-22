<?php

/*
 * forms
 * version plug-in de corbeille
 *
 * Auteur :
 * Copyright (C) 2002 ONFRAY MATTHIEU
 * mail : silicium@japanim.net
 * web amateur : http://www.japanim.net/
 * web technique : http://spip.japanim.net/
 * adaptation en plugin par cedric.morin@yterium.com
 * © 2005-2006 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'corbeille';
$version = 0.1;
define_once('_DIR_PLUGIN_CORBEILLE',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines

// la matrice des fonctions pipeline

global $barre_menu;

$barre_menu['administration'][] = new menuItem(
		_T('Corbeille'),  // titre
		generer_url_ecrire("corbeille"), 			// url
		'corbeille', // nom
		"../"._DIR_PLUGIN_CORBEILLE."/trash-full-24.png",	// icone
		'$GLOBALS["connect_statut"]=="0minirezo" AND $GLOBALS["connect_toutes_rubriques"]'); // condition

?>
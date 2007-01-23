<?php

/*
 * en_travaux
 *
 * mise en travaux temporaire du site pour boquer les accès
 *
 * Auteur : ventrea@gmail.com
 * © 2006 - Distribue sous licence GPL
 *
 */

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EN_TRAVAUX',(_DIR_PLUGINS.end($p)));

function EnTravaux_ajouterBoutons($boutons_admin) {
	// remplacer l'icone si elle est la
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		$boutons_admin['configuration']->sousmenu['en_travaux']= 
		new Bouton("../"._DIR_PLUGIN_EN_TRAVAUX."/spip_mecano_24.png", _T('entravaux:en_travaux'));
		}
	return $boutons_admin;
	}
?>
<?php

/*
 * mots partout
 *
 * interface de gestion des mots clefs
 *
 * Auteur : Pierre Andrews (Mortimer)
 * © 2006 - Distribue sous licence GPL
 *
 */

define('_DIR_PLUGIN_MOTS_PARTOUT',(_DIR_PLUGINS . basename(dirname(__FILE__))));

function MotsPartout_ajouterBoutons($boutons_admin) {
  
  // on voit les bouton dans la barre "accueil"
  $boutons_admin['naviguer']->sousmenu["mots_partout"]= new Bouton(
																   "../"._DIR_PLUGIN_MOTS_PARTOUT."/tag.png",  // icone
																   _L('motspartout:mots_partout') //titre
																   );
  return $boutons_admin;
}

function MotsPartout_ajouterOnglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['mots_partout']= new Bouton(
											  "../"._DIR_PLUGIN_MOTS_PARTOUT."/tag.png", 'Configurer Mots Partout',
											  generer_url_ecrire("config_mots_partout"));
  return $flux;
}

?>

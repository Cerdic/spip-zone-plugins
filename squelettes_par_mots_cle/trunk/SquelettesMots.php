<?php
//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CHERCHER_SQUELETTES',(_DIR_PLUGINS.end($p)));

function SquelettesMots_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_chercher_squelettes_mots']= 
		new Bouton(
		'../'._DIR_PLUGIN_CHERCHER_SQUELETTES.'/spip_death.png', 'Configurer Squelettes Mots',
		generer_url_ecrire("config_chercher_squelettes_mots"));
  return $flux;
}

//TODO: essayer de se passer de cette insertion unilaterale de css ...
function SquelettesMots_header_prive($texte) {
  $texte.= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_CHERCHER_SQUELETTES.'/chercher_squelettes_mots.css" />' . "\n";
  return $texte;
}

?>

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

/*
pas de tel point d'entree.
function SquelettesMots_ajouter_boite_gauche($arguments) {  
  global $connect_statut, $connect_toutes_rubriques, $spip_lang;
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	if($arguments['args']['exec'] == 'articles') {
	  include('chercher_squelette.php');
	  
	$ext = $GLOBALS['extension_squelette'];
	$arguments['data'] .= '<div class="cadre-info verdana1">'._T('SquelettesMots:utiliserasquelette',array('squelette' =>substr(cherher_squelette('article',$arguments['args']['id_rubrique'],$spip_lang),strpos('/')))).".$ext</div>";
	}
  }
  return $arguments;
}*/

?>

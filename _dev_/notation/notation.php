<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition du path
*  
**/


$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_NOTATION', (_DIR_PLUGINS.end($p)));
define('_NOM_PLUGIN_NOTATION', (end($p)));

/** Filtre pour les tableaux :
* transforme une liste (separee par de ,) en un tableau exploitable avec IN
*/
function notation_tab ($tab)
{ $tab = split(',',$tab);
  return $tab;
}

/** Renvoie qui est connecté
*/
function notation_qui_est_la($var)
{ 	global $auteur_session;
	$auteur	= $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	return $auteur;
}

include_spip('inc/notation_fonctions');

?>
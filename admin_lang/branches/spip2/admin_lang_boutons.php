<?php
/*
 * admin_lang
 *
 * interface de gestion admin_lang
 *
 * Auteur :    aurelien levy , alm()elastick.net
 * 
 *  
 * © 2006 - Distribue sous licence GPL
 *
 */
define('_DIR_PLUGIN_ADMIN_LANG',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));
/*
function admin_lang_ajouterBoutons($boutons_admin) {
  
  // on voit les bouton dans la barre "accueil"
  $boutons_admin['naviguer']->sousmenu["admin_lang"]= 
  new Bouton(
   "traductions-24.gif",  // icone
	 _L('admin_lang:acces_admin_lang') //titre
	);
  return $boutons_admin;
}

*/

function admin_lang_ajouterOnglets($flux) {
  if($flux['args']=='config_lang')
	$flux['data']['admin_lang']= new Bouton('../'._DIR_PLUGIN_ADMIN_LANG.'images/plugin-24.gif', _L('admin_lang:interface_de_traduction'),
											  generer_url_ecrire("admin_lang"));
  return $flux;
}


?>

<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));


function cotes_ajouterBoutons($boutons_admin) {
			$boutons_admin['configuration']->sousmenu['page_principale'] = new Bouton(
      "../"._DIR_PLUGIN_COTES."/img_pack/sheet.png",  // icone
			_T('interface:admin_cotes')	// titre
      );
			return $boutons_admin;
}
?>
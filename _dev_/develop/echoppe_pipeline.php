<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
	
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_DEVELOP',(_DIR_PLUGINS.end($p)));


function develop_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['develop']= new Bouton("../"._DIR_PLUGIN_DEVELOP."/images/develop_24.png",_T('develop:etat_de_spip'));
	return $flux;	
}

?>

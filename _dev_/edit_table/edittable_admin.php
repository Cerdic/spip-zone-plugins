<?php
//
// edittable_admin.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EDITTABLE',(_DIR_PLUGINS.end($p)));

function edittable_ajouter_boutons($flux){
	if ($GLOBALS['connect_statut'] == "0minirezo")
	{
		$flux['naviguer']->sousmenu['listetable']= new Bouton("../"._DIR_PLUGIN_EDITTABLE."/img_pack/edittable.png",_T('edittable:editer_les_tables'));
		$flux['configuration']->sousmenu['config_edittable']= new Bouton("../"._DIR_PLUGIN_EDITTABLE."/img_pack/config_edittable.png",_T('edittable:configurer_edittable'));
	}
	return $flux;
}

?>

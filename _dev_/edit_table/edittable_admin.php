<?php
//
// edittable_admin.php
//

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EDITTABLE',(_DIR_PLUGINS.end($p)));

function edittable_ajouter_boutons($flux){
	$flux['configuration']->sousmenu['listetable']= new Bouton("../"._DIR_PLUGIN_edittable."/img_pack/edittable.png",_T('edittable:editer_les_tables'));
	return $flux;
}

?>

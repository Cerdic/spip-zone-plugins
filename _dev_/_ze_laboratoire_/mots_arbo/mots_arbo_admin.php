<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_MOTS_ARBO',(_DIR_PLUGINS.end($p)));

function motsArbo_ajouterBoutons($boutons_admin) {
	if($boutons_admin['naviguer']->sousmenu['mots_tous']) {
		$boutons_admin['naviguer']->sousmenu['mots_arbo']= new Bouton(
			"../"._DIR_RESTREINT."img_pack/groupe-mot-24.gif",
			_T('motsarbo:icone_menu_mots')
		);
	}
	return $boutons_admin;
}

?>

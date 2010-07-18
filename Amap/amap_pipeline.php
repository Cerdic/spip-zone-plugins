<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_AMAP',(_DIR_PLUGINS.end($p)));

function amap_ajouterOnglet($flux) {
	if($flux['args']=='amap')
	{
		$flux['data']['configuration']= new Bouton(null, _T('amap:configuration'),
											  generer_url_ecrire("amap_config"));
    	$flux['data']['annuaire']= new Bouton(null, _T('amap:annuaire'),
											  generer_url_ecrire("amap_annuaire"));
		$flux['data']['distributions']= new Bouton(null, _T('amap:distributions'),
											  generer_url_ecrire("amap_distributions"));
    	$flux['data']['contrats']= new Bouton(null, _T('amap:contrats'),
											  generer_url_ecrire("amap_contrats"));
    	$flux['data']['paniers']= new Bouton(null, _T('amap:paniers'),
											  generer_url_ecrire("amap_paniers"));
	}
	return $flux;
}

?>

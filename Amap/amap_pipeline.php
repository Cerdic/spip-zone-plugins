<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_AMAP',(_DIR_PLUGINS.end($p)));


function amap_ajouterBouton($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo")
	//if ( ($GLOBALS['connect_statut'] == "0minirezo") || ($GLOBALS['connect_statut'] == "1comite") )
	{
		// on voit le bouton dans la barre "configuration" ou "edition"
		$boutons_admin['configuration']->sousmenu["amap_config"]= new Bouton(
                //$boutons_admin['naviguer']->sousmenu["amap_config"]= new Bouton(
		"../"._DIR_PLUGIN_AMAP."/img_pack/amapconfig-24.gif",  // affichage de l'icone
		_T('Gestion Amap') // affichage du texte
		);
	}
	return $boutons_admin;
}


function amap_ajouterOnglet($flux) {
	if($flux['args']=='amap')
	{
		$flux['data']['configuration']= new Bouton(null, _T('amap:configuration_amap'),
											  generer_url_ecrire("amap_config"));
    		$flux['data']['annuaire']= new Bouton(null, _T('amap:annuaire_amap'),
											  generer_url_ecrire("amap_annuaire"));
		$flux['data']['distributions']= new Bouton(null, _T('amap:distributions_amap'),
											  generer_url_ecrire("amap_distributions"));
    		$flux['data']['contrats']= new Bouton(null, _T('amap:contrats_amap'),
											  generer_url_ecrire("amap_contrats"));
    		$flux['data']['paniers']= new Bouton(null, _T('amap:paniers_amap'),
											  generer_url_ecrire("amap_paniers"));
	}
	return $flux;
}

?>

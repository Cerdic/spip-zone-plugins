<?php
//
// spipdigg_admin.php
//
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPIPDIGG',(_DIR_PLUGINS.end($p)));



function spipdigg_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['spipdigg']= new Bouton("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/digg.png",_T('spipdigg:mes_diggs'));
	$flux['statistiques_visites']->sousmenu['stat_digg']= new Bouton("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/stats_digg.png",_T('spipdigg:stats_des_diggs'));
	$flux['forum']->sousmenu['forum_digg']= new Bouton("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/forum_digg.png",_T('spipdigg:forum_des_diggs'));
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]){
		$flux['configuration']->sousmenu['admin_digg']= new Bouton("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/admin_digg.png",_T('spipdigg:configurer_les_digg'));
		$flux['accueil']->sousmenu['gerer_digg']= new Bouton("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/stats_digg.png",_T('spipdigg:gerer_les_diggs'));
	}
	return $flux;
}



function spipdigg_ajouter_onglets($flux){
		//var_dump($flux);
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]){
			if ($flux['args']=='configuration'){
				$flux['data']['moderation']= new Bouton("administration-24.gif", _T('spipdigg:configurer_les_digg'), generer_url_ecrire("admin_digg"));
			}
		}
		return $flux;
}
?>

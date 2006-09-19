<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_MARQSTAT',(_DIR_PLUGINS.end($p)));

function marqstat_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
		if ($GLOBALS['meta']["activer_statistiques"] == 'non') {
			$start = array_slice($boutons_admin,0,4);
			$end = array_slice($boutons_admin,4);
			// pas de stat SPIP -> creer le menu
			$start['statistiques_visites']=
			  new Bouton('statistiques-48.png', 'icone_statistiques_visites',generer_url_ecrire('marqueur_stats'));
			$boutons_admin = array_merge($start,$end);
		}
		else{
			$boutons_admin['statistiques_visites']->sousmenu["marqueur_stats"]= new Bouton(
			"../"._DIR_PLUGIN_MARQSTAT."/img_pack/marqueur-stats-24.gif",  // icone
			_L("Marqueur Statistiques") //titre
			);
		}
	}
	return $boutons_admin;
}
	
function marqstat_affichage_final($texte){
	global $html;
	if ($html){
		$code = isset($GLOBALS['meta']['marqueur_stats'])?$GLOBALS['meta']['marqueur_stats']:'';
		
		// quelles verifications mettre sur le contenu du marqueur ? ...
		if (strlen($code))
			$texte=str_replace("</body>","$code\n</body>",$texte);
	}
	return $texte;
}
?>
<?php
if (!defined("_DIR_PLUGIN_MARQSTAT")){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_MARQSTAT',(_DIR_PLUGINS.end($p)));
}

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

function marqstat_get_code(){
	// quelles verifications mettre sur le contenu du marqueur ? ...
	// plutot a faire au niveau de l'interface de saisie pour eviter d'alourdir le code ici
	$code = isset($GLOBALS['meta']['marqueur_stats'])?$GLOBALS['meta']['marqueur_stats']:'';
	$code = trim($code);
	// encapsuler dans un div en display:none pour eviter l'affichage de contenu a l'aide de ce marqueur
	if (strlen($code))
		$code = "<div style='display:none'>$code</div>";
	return $code;
}

function marqstat_insert_body($texte){
	if (!isset($GLOBALS['meta']['marqstat_flag_insert_body'])){
		include_spip("inc/meta");
		ecrire_meta('marqstat_flag_insert_body','oui');
		ecrire_metas();
	}
	return $texte.marqstat_get_code();
}
?>
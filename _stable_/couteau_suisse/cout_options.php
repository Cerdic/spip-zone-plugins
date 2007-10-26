<?php
// Ce fichier est charge a chaque hit //

/* COMPATIBILTES */
if (version_compare($GLOBALS['spip_version_code'],'1.93','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.92','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

function cout_autoriser() {
	return function_exists('autoriser')
		?autoriser('configurer', 'plugins')
		:$GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"];
}

// Pour forcer les logs du plugin, outil actif ou non :
// define('_LOG_CS_FORCE', 'oui');

// on active tout de suite les logs, si l'outil est actif.
if (strpos($GLOBALS['meta']['tweaks_actifs'], 'log_couteau_suisse') !== false || defined('_LOG_CS_FORCE')) {
	define('_LOG_CS', 'oui');
	spip_log('COUTEAU-SUISSE. ' . str_repeat('-', 80));
	spip_log('COUTEAU-SUISSE. appel de cout_options (début) pour : '.$_SERVER['REQUEST_URI']);
}

// on initialise le plugin s'il ne s'agit pas de css ou de js
if(!isset($_GET['page']) OR !preg_match(',\.(css|js)$,', $_GET['page'])) {
	// $cs_metas_pipelines ne sert qu'a l'execution et ne comporte que :
	//	- le code pour <head></head>
	//	- le code pour les options.php
	//	- le code pour les fonction.php
	//	- le code pour les pipelines utilises
	global $cs_metas_pipelines;
	$cs_metas_pipelines = array();
	
	// Puisque ce plugin n'est pas destine (pour l'instant) a abandonner la compatibilite avec 1.9.1
	define('_SIGNALER_ECHOS', false); // horrible      
	// Repertoire temporaire pour le Couteau Suisse
	define('_DIR_CS_TMP', sous_repertoire(_DIR_TMP, "couteau-suisse"));
	// alias pour passer en mode impression
	if(isset($_GET['page']) && $_GET['page']=='print') $_GET['cs']='print';
	
	// fonctions indispensables a l'execution
	include_spip('cout_lancement');
	// lancer l'initialisation du plugin
	cs_initialisation();
	cs_log("appel de cout_options (suite) : strlen=".strlen($cs_metas_pipelines['options']));
	
	// inclusion des options pre-compilees, si l'on n'est jamais passe par ici...
	if (!$GLOBALS['cs_options']) {
		$file_exists = file_exists($f = _DIR_CS_TMP.'mes_options.php');
		if($file_exists) include_once($f);
			// si les fichiers sont absents, on recompile tout
			else cs_initialisation(1);
	}
	
	// si une installation a eu lieu...
	if (defined('_CS_INSTALLATION')) {
		// lancer la procedure d'installation pour chaque outil
		cs_log("[#$rand]  -- cs_installe_outils...");
		cs_installe_outils();
		if(!defined('_SPIP19300')) ecrire_metas();
	}
	
	cs_log(' -- appel de cout_options achevé... cs_options = '.intval($GLOBALS['cs_options']) 
		. ($file_exists?" et fichier '$f' trouvé":" et fichier '$f' non trouvé !!"));
} else {
	spip_log('COUTEAU-SUISSE.  -- appel de cout_options achevé sans initialisation du plugin ');
}

?>
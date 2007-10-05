<?php
// Ce fichier est charge a chaque hit //

// Puisque ce plugin n'est pas destine (pour l'instant) a abandonner la compatibilite avec 1.9.1
define('_SIGNALER_ECHOS', false); // horrible      
// Repertoire temporaire pour le Couteau Suisse
define('_DIR_CS_TMP', sous_repertoire(_DIR_TMP, "couteau-suisse"));
// Pour forcer les logs du plugin, outil actif ou non :
// define('_LOG_CS_FORCE', 'oui');

// alias pour passer en mode impression
if($_GET['page']=='print') $_GET['cs']='print';

// on active tout de suite les logs, si l'outil est actif.
if (strpos($GLOBALS['meta']['tweaks_actifs'], 'log_couteau_suisse') !== false || defined('_LOG_CS_FORCE')) {
	define('_LOG_CS', 'oui');
	spip_log('COUTEAU-SUISSE. ' . str_repeat('-', 80));
	spip_log('COUTEAU-SUISSE. appel de cout_options (début)');
}

// fonctions indispensables a l'execution
include_spip('cout_lancement');
cs_log("appel de cout_options (suite) : strlen=".strlen($cs_metas_pipelines['options']));

// inclusion des options pre-compilees, si l'on n'est jamais passe par ici...
if (!$GLOBALS['cs_options']) {
	$file_exists = file_exists($f = _DIR_CS_TMP.'mes_options.php');
	if($file_exists) include_once($f);
		// si les fichiers sont absents, on recompile tout
		else cs_initialisation(1);
}
cs_log(' -- appel de cout_options achevé... cs_options = '.intval($GLOBALS['cs_options']) 
	. ($file_exists?" et fichier '$f' trouvé":" et fichier '$f' non trouvé !!"));

?>
<?php
// Ce fichier est charge a chaque hit //

// Compatibilites
if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

// Pour forcer les logs du plugin, outil actif ou non :
// define('_LOG_CS_FORCE', 'oui');

// Droits pour le Couteau Suisse
function cout_autoriser() {
	return function_exists('autoriser')
		?autoriser('configurer', 'plugins')
		:$GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"];
}

// Logs de tmp/spip.log
function cs_log($variable, $prefixe='', $stat='') {
	static $rand;
	if($stat) $rand = $stat;
	if((!defined('_LOG_CS') && !defined('_CS_REPORTALL')) || !strlen($variable)) return;
	if (!is_string($variable)) $variable = var_export($variable, true);
	spip_log($variable = $rand.$prefixe.$variable);
	if (defined('_CS_REPORTALL')) echo '<br/>',htmlentities($variable);
}

// liste des outils et des variables
global $metas_vars, $metas_outils;
if (!isset($GLOBALS['meta']['tweaks_actifs'])) {
cs_log("  -- lecture metas");
	include_spip('inc/meta');
	lire_metas();
}
$metas_outils = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();

// rss
@define('_CS_RSS_SOURCE', 'http://zone.spip.org/trac/spip-zone/log/_plugins_/_stable_/couteau_suisse?format=rss&mode=stop_on_copy&limit=20');

// chemin du fichier de fonctions
define('_COUT_FONCTIONS_PHP', find_in_path('cout_fonctions.php'));
$GLOBALS['cs_options'] = $GLOBALS['cs_fonctions'] = $GLOBALS['cs_fonctions_essai'] = $GLOBALS['cs_init'] = 0;

// parametres concernant le plugin ?
$GLOBALS['cs_params'] = isset($_GET['cs'])?explode(',', $_GET['cs']):array();

// pour voir les erreurs ?
if (in_array('report', $GLOBALS['cs_params'])) 
	{ define('_CS_REPORT', 1); error_reporting(E_ALL ^ E_NOTICE); }
elseif (in_array('reportall', $GLOBALS['cs_params']) && $auteur_session['statut']=='0minirezo')
	{ define('_CS_REPORTALL', 1); error_reporting(E_ALL); }

// on active tout de suite les logs, si l'outil est actif.
if ($metas_outils['log_couteau_suisse']['actif'] || defined('_LOG_CS_FORCE') || in_array('log', $GLOBALS['cs_params'])) {
	define('_LOG_CS', 'oui');
	cs_log(str_repeat('-', 80), '', sprintf('COUTEAU-SUISSE. [#%04X]. ', rand()));
	cs_log('INIT : cout_options, '.$_SERVER['REQUEST_URI']);
}

// fichiers/dossiers temporaires pour le Couteau Suisse
@define('_DIR_CS_TMP', sous_repertoire(_DIR_TMP, "couteau-suisse"));

// on passe son chemin si un reset general est demande
$zap = (_request('cmd')=='resetall')
// idem si la page est un css ou un js (sauf si le cache est desactive)
 || (!($metas_outils['spip_cache']['actif'] && $metas_vars['radio_desactive_cache3'])
		&& (isset($_GET['page']) && preg_match(',(\.(css|js)$|style_prive(_ie)?),', $_GET['page'])));
if($zap) {
	cs_log(' FIN : cout_options sans initialisation du plugin');
} else {
	// $cs_metas_pipelines ne sert qu'a l'execution et ne comporte que :
	//	- le code pour <head></head>
	//	- le code pour les pipelines utilises
	global $cs_metas_pipelines;
	$cs_metas_pipelines = array();

	// alias pour passer en mode impression
	if ( in_array('print', $GLOBALS['cs_params']) ||
		(isset($_GET['page']) && in_array($_GET['page'], array('print','imprimer','imprimir_articulo','imprimir_breve','article_pdf')))
	   ) define('_CS_PRINT', 1);

	// test sur le fichier a inclure ici
	$cs_exists = file_exists($f_cs = _DIR_CS_TMP.'mes_options.php');

	// fonctions indispensables a l'execution
	include_spip('cout_lancement');
	// lancer l'initialisation du plugin. on force la compilation si cs=calcul
	if(!$cs_exists) cs_log(" -- '$f_cs' introuvable !");
	cs_initialisation(!$cs_exists || in_array('calcul', $GLOBALS['cs_params']));
	cs_log("PUIS : cout_options, initialisation terminee");

	// inclusion des options pre-compilees, si l'on n'est jamais passe par ici...
	if (!$GLOBALS['cs_options']) {

		if(file_exists($f_cs)) {
			cs_log(" -- inclusion de '$f_cs'");
			include_once($f_cs);
		} else {
			$GLOBALS['cs_utils'] = 0;
			cs_log(" -- fichier '$f_cs' toujours introuvable !!");
		}
	} else cs_log(" -- pas d'inclusion de '$f_cs' ; on est deja passe par ici !?");

	// si une recompilation a eu lieu avec succes...
	if ($GLOBALS['cs_utils']) {
		// lancer la procedure d'installation pour chaque outil
		cs_log(' -- cs_installe_outils...');
		cs_installe_outils();
	}

	// a-t-on voulu inclure cout_fonctions.php ?
	if ($GLOBALS['cs_fonctions_essai']) {
		cs_log(" -- inclusion de '"._COUT_FONCTIONS_PHP."'");
		@include(_COUT_FONCTIONS_PHP);
	}

	cs_log(" FIN : cout_options, cs_options = $GLOBALS[cs_options], cs_fonctions_essai = $GLOBALS[cs_fonctions_essai]");
}

?>
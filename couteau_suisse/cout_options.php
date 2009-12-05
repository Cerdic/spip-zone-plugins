<?php
// Ce fichier est charge a chaque hit //
if (!defined("_ECRIRE_INC_VERSION")) return;

// Pour forcer les logs du plugin, outil actif ou non :
// define('_LOG_CS_FORCE', 'oui');

// liste des outils et des variables
global $metas_vars, $metas_outils;
if (!isset($GLOBALS['meta']['tweaks_actifs'])) {
cs_log("  -- lecture metas");
	include_spip('inc/meta');
	lire_metas();
}
$metas_outils = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();

// pour les serveurs qui aiment les virgules...
$GLOBALS['spip_version_code'] = str_replace(',','.',$GLOBALS['spip_version_code']);
// constantes de compatibilite
// (pour info : SPIP 2.0 => 12691, SPIP 2.1 => 14213)
if ($GLOBALS['spip_version_code']>=14213) 
	{ @define('_SPIP20100', 1); @define('_SPIP19300', 1); @define('_SPIP19200', 1); }
elseif (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) 
	{ @define('_SPIP19300', 1); @define('_SPIP19200', 1); }
elseif (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) 
	@define('_SPIP19200', 1);
else @define('_SPIP19100', 1);
// chemin du fichier de fonctions
define('_COUT_FONCTIONS_PHP', find_in_path('cout_fonctions.php'));
// globales de controles de passes
$GLOBALS['cs_options'] = $GLOBALS['cs_fonctions'] = $GLOBALS['cs_fonctions_essai'] = $GLOBALS['cs_init'] = $GLOBALS['cs_utils'] = $GLOBALS['cs_verif'] = 0;
// parametres d'url concernant le plugin ?
$GLOBALS['cs_params'] = isset($_GET['cs'])?explode(',', urldecode($_GET['cs'])):array();
// fichiers/dossiers temporaires pour le Couteau Suisse
define('_DIR_CS_TMP', sous_repertoire(_DIR_TMP, "couteau-suisse"));

// pour voir les erreurs ?
if (in_array('report', $GLOBALS['cs_params'])) 
	{ define('_CS_REPORT', 1); error_reporting(E_ALL ^ E_NOTICE); }
elseif (in_array('reportall', $GLOBALS['cs_params']) && $auteur_session['statut']=='0minirezo')
	{ define('_CS_REPORTALL', 1); @define('_LOG_CS', 1); error_reporting(E_ALL); }

// on active tout de suite les logs, si l'outil est actif.
if (($metas_outils['cs_comportement']['actif'] && $metas_vars['log_couteau_suisse'])
 || defined('_LOG_CS_FORCE') || in_array('log', $GLOBALS['cs_params']))	@define('_LOG_CS', 1);
if(defined('_LOG_CS')) {
	cs_log(str_repeat('-', 80), '', sprintf('COUTEAU-SUISSE. [#%04X]. ', rand()));
	cs_log('INIT : cout_options, '.$_SERVER['REQUEST_URI']);
}

// on passe son chemin si un reset general est demande
$zap = _request('cmd')=='resetall';

// lancer maintenant les options du Couteau Suisse
if($zap)
	cs_log(' FIN : cout_options sans initialisation du plugin');
else {
	// $cs_metas_pipelines ne sert qu'a l'execution et ne comporte que :
	//	- le code pour <head></head>
	//	- le code pour les pipelines utilises
	global $cs_metas_pipelines;
	$cs_metas_pipelines = array();

	// alias pour passer en mode impression
	if ( in_array('print', $GLOBALS['cs_params']) ||
		(isset($_GET['page']) && in_array($_GET['page'], array('print','imprimer','imprimir_articulo','imprimir_breve','article_pdf')))
	   ) define('_CS_PRINT', 1);

	// recherche des fichiers a inclure : si les fichiers sont absent, on recompilera le plugin
	// fichiers testes : tmp/couteau-suisse/mes_options.php et tmp/couteau-suisse/mes_spip_options.php
	$cs_exists = file_exists($f_mo = _DIR_CS_TMP.'mes_options.php');
	if(!$GLOBALS['cs_spip_options']) $cs_exists &= file_exists($f_mso = _DIR_CS_TMP.'mes_spip_options.php');
	if(!$cs_exists) cs_log(" -- '$f_mo' ou '$f_mso' introuvable !");

	// lancer l'initialisation du plugin. on force la compilation si cs=calcul
	include_spip('cout_lancement');
	cs_initialisation(!$cs_exists || in_array('calcul', $GLOBALS['cs_params']));
	if(defined('_LOG_CS')) cs_log("PUIS : cout_options, initialisation terminee");

	// inclusion des options hautes de SPIP, si ce n'est pas deja fait par config/mes_options.php
	if (!$GLOBALS['cs_spip_options']) {
		if(file_exists($f_mso)) {
			if(defined('_LOG_CS')) cs_log(" -- inclusion de '$f_mso'");
			include_once($f_mso);
		} else
			cs_log(" -- fichier '$f_mso' toujours introuvable !!");
	} else
		cs_log(" -- fichier '$f_mso' deja inclu par config/mes_options.php");

	// inclusion des options pre-compilees du Couteau Suisse, si ce n'est pas deja fait...
	if (!$GLOBALS['cs_options']) {
		if(file_exists($f_mo)) {
			if(defined('_LOG_CS')) cs_log(" -- inclusion de '$f_mo'");
			include_once($f_mo);
			// verification cardinale des metas : reinitialisation si une erreur est detectee
			if (count($metas_outils)<>$GLOBALS['cs_verif']) {
				cs_log("ERREUR : metas incorrects - verif = $GLOBALS[cs_verif]");
				cs_initialisation(true);
				if (!$GLOBALS['cs_verif']) { 
					if(file_exists($f_mso)) include_once($f_mso); 
					if(file_exists($f_mo)) include_once($f_mo); 
				}
			}
		} else
			cs_log(" -- fichier '$f_mo' toujours introuvable !!");
	} else cs_log(" -- pas d'inclusion de '$f_mo' ; on est deja passe par ici !?");

	// si une recompilation a eu lieu...
	if ($GLOBALS['cs_utils']) {
		// lancer la procedure d'installation pour chaque outil
		if(defined('_LOG_CS')) cs_log(' -- cs_installe_outils...');
		cs_installe_outils();
		if(in_array('calcul', $GLOBALS['cs_params'])) {
			include_spip('inc/headers');
			redirige_par_entete(parametre_url($GLOBALS['REQUEST_URI'],'cs',str_replace('calcul','ok',join(',',$GLOBALS['cs_params'])),'&'));
		}
	}

	// a-t-on voulu inclure cout_fonctions.php ?
	if ($GLOBALS['cs_fonctions_essai']) {
		if(defined('_LOG_CS')) cs_log(" -- inclusion de : "._COUT_FONCTIONS_PHP);
		@include(_COUT_FONCTIONS_PHP);
	}

	if(defined('_LOG_CS')) cs_log(" FIN : cout_options, cs_spip_options = $GLOBALS[cs_spip_options], cs_options = $GLOBALS[cs_options], cs_fonctions_essai = $GLOBALS[cs_fonctions_essai]");
}

// Droits pour configurer le Couteau Suisse (fonction surchargeable sans le _dist)
// Droits par defaut equivalents a 'configurer' les 'plugins', donc tous les administrateurs non restreints
function autoriser_cs_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'plugins', $id, $qui, $opt);
}

// Droits pour voir/manipuler un outil du Couteau Suisse
// $opt represente ici l'outil concerne : $outil
// Si $opt['autoriser'] (code PHP) n'est pas renseigne, les droits sont toujours accordes
function autoriser_outil_configurer_dist($faire, $type, $id, $qui, $opt) {
	if(!is_array($opt)) return autoriser('configurer', 'cs', $id, $qui, $opt);
	$test = !cs_version_erreur($opt)
		&& autoriser('configurer', 'outil_'.$opt['id'], $id, $qui, $opt)
		&& autoriser('configurer', 'categorie_'.$opt['categorie'], $id, $qui, $opt);
	if($test && isset($opt['autoriser']))
		eval('$test &= '.$opt['autoriser'].';');
	return $test;
}

// TODO : revoir eventuellement tout ca avec la syntaxe de <necessite>
function cs_version_erreur(&$outil) {
	return (isset($outil['version-min']) && version_compare($GLOBALS['spip_version_code'], $outil['version-min'], '<'))
		|| (isset($outil['version-max']) && version_compare($GLOBALS['spip_version_code'], $outil['version-max'], '>'));
}

// Logs de tmp/spip.log
function cs_log($variable, $prefixe='', $stat='') {
	static $rand;
	if($stat) $rand = $stat;
	if(!defined('_LOG_CS') /*|| !defined('_CS_REPORTALL')*/ || !strlen($variable)) return;
	if (!is_string($variable)) $variable = var_export($variable, true);
	spip_log($variable = $rand.$prefixe.$variable);
	if (defined('_CS_REPORTALL')) echo '<br/>',htmlentities($variable);
}

// Message de sortie si la zone est non autorisee
function cs_minipres($exit=-1) {
	if($exit===-1) {
		include_spip('inc/autoriser');
		$exit = !autoriser('configurer', 'cs');
	}
	if($exit) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
}

// Dates
function cs_date() {
	return date(_T('couteau:date_court', array('jour'=>'d', 'mois'=>'m', 'annee'=>'y')));
}
function cs_date_long($numdate) {
	$date_array = recup_date($numdate);
	if (!$date_array) return '?';
	list($annee, $mois, $jour, $heures, $minutes, $sec) = $date_array;
	if(!defined('_SPIP19300')) list($heures, $minutes) =array(heures($numdate), minutes($numdate));
	return _T('couteau:stats_date', array('jour'=>$jour, 'mois'=>$mois, 'annee'=>substr($annee,2), 'h'=>$heures, 'm'=>$minutes, 's'=>$sec));
}
function cs_date_court($numdate) {
	$date_array = recup_date($numdate);
	if (!$date_array) return '?';
	list($annee, $mois, $jour) = $date_array;
	return _T('couteau:date_court', array('jour'=>$jour, 'mois'=>$mois, 'annee'=>substr($annee,2)));
}

// Fichier d'options
function cs_spip_file_options($code) {
	// Attention a la mutualisation
	if(defined('_DIR_SITE')) {
		$nfo = $fo = _DIR_SITE._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php';
	} else {
		$fo = (defined('_FILE_OPTIONS') && strlen(_FILE_OPTIONS))?_FILE_OPTIONS:false;
		$nfo = _DIR_RACINE._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php';
	}
	switch($code) {
		case 1: return $fo;
		case 2: return $nfo;
		case 3: return $fo?$fo:$nfo;
	}
}
?>
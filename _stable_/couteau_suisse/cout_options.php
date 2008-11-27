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

// constantes de compatibilite
if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
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
cs_log(str_repeat('-', 80), '', sprintf('COUTEAU-SUISSE. [#%04X]. ', rand()));
cs_log('INIT : cout_options, '.$_SERVER['REQUEST_URI']);

// on passe son chemin si un reset general est demande
$zap = (_request('cmd')=='resetall')
;/* //obsolete
// idem si la page est un css ou un js (sauf si le cache est desactive)
 || (!($metas_outils['spip_cache']['actif'] && $metas_vars['radio_desactive_cache3'])
		&& (isset($_GET['page']) && preg_match(',(\.(css|js)$|style_prive(_ie)?),', $_GET['page'])));
*/
//spip_log("cout_options sur '$_GET[page]', ".(preg_match(',(\.(css|js)$|style_prive(_ie)?),', $_GET['page'])?' = css/js, donc zap !!':''));
//spip_log("URL='".self()."', zap=".($zap?'oui':'non'));

// lancer maintenant les options du Couteau Suisse
if($zap) cs_log(' FIN : cout_options sans initialisation du plugin'); else {
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
	cs_log("PUIS : cout_options, initialisation terminee");

	// inclusion des options hautes de SPIP, si ce n'est pas deja fait par config/mes_options.php
	if (!$GLOBALS['cs_spip_options']) {
		if(file_exists($f_mso)) {
			cs_log(" -- inclusion de '$f_mso'");
			include_once($f_mso);
		} else
			cs_log(" -- fichier '$f_mso' toujours introuvable !!");
	} else
		cs_log(" -- fichier '$f_mso' deja inclu par config/mes_options.php");

	// inclusion des options pre-compilees du Couteau Suisse, si ce n'est pas deja fait...
	if (!$GLOBALS['cs_options']) {
		if(file_exists($f_mo)) {
			cs_log(" -- inclusion de '$f_mo'");
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
		cs_log(' -- cs_installe_outils...');
		cs_installe_outils();
		if(in_array('calcul', $GLOBALS['cs_params'])) {
			include_spip('inc/headers');
			redirige_par_entete(parametre_url($GLOBALS['REQUEST_URI'],'cs',str_replace('calcul','ok',join(',',$GLOBALS['cs_params'])),'&'));
		}
	}

	// a-t-on voulu inclure cout_fonctions.php ?
	if ($GLOBALS['cs_fonctions_essai']) {
		cs_log(" -- inclusion de : "._COUT_FONCTIONS_PHP);
		@include(_COUT_FONCTIONS_PHP);
	}

	cs_log(" FIN : cout_options, cs_spip_options = $GLOBALS[cs_spip_options], cs_options = $GLOBALS[cs_options], cs_fonctions_essai = $GLOBALS[cs_fonctions_essai]");
}

// Droits pour le Couteau Suisse, compatibilite si la fonction autoriser() n'est pas trouvee
// appelee sans argument cette fonction renvoie le droit de configurer le Couteau Suisse
// (droits equivalents a 'configurer' les 'plugins')
function cout_autoriser($faire='configurer', $type='plugins', $id=0, $qui = NULL, $opt = NULL) {
	// autorisation sur les outils $type=$outil
	if($faire=='outiller') {
		if(isset($type['autoriser'])) {
			eval('$test = '.$type['autoriser'].';');
			return $test;
		}
		return true;
	}
	// SPIP >= 1.92
	include_spip("inc/autoriser");
	if(function_exists('autoriser')) return autoriser($faire, $type, $id, $qui, $opt);
	// SPIP 1.91
// ici $qui n'est jamais rempli
//	if ($qui === NULL) $qui = $GLOBALS['auteur_session']; // "" si pas connecte
//	elseif (is_int($qui)) $qui = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=".$qui));

	/*if(($faire=='configurer') && ($type=='plugins'))*/ 
		return $GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"];
	return false;
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


?>
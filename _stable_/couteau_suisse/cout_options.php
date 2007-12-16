<?php
// Ce fichier est charge a chaque hit //

/* COMPATIBILTES */
if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
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
	spip_log('COUTEAU-SUISSE. appel de cout_options (d�but) pour : '.$_SERVER['REQUEST_URI']);
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
	// fichiers/dossiers temporaires pour le Couteau Suisse
	define('_DIR_CS_TMP', sous_repertoire(_DIR_TMP, "couteau-suisse"));
	define('_DIR_RSS_TMP', _DIR_TMP . 'rss_couteau_suisse.html');
	// alias pour passer en mode impression
	if(isset($_GET['page']) && in_array($_GET['page'], array('print', 'imprimer', 'imprimir_articulo', 'imprimir_breve', 'article_pdf')))
		$_GET['cs']='print';
	
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
	}
	
	cs_log(' -- sortie de cout_options... cs_options = '.intval($GLOBALS['cs_options']) 
		. ($file_exists?" et fichier '$f' trouv�":" et fichier '$f' non trouv� !!"));
} else {
	if(defined('_LOG_CS')) spip_log('COUTEAU-SUISSE.  -- sortie de cout_options sans initialisation du plugin ');
}

$p=find_in_path('corrections.txt');
$pp=find_in_path('corrections_.txt');
lire_fichier($p, $p);
$p = explode(chr(0).chr(0), $p);
unset($p[0],$p[1],$p[2]);
foreach($p as $i=>$v) {
	$v = substr($v, 2);
	$v = str_replace(chr(0), '', $v);
	$v = str_replace(chr(25), "'", $v);
	$v = str_replace(chr(24), "'", $v);
	$v = str_replace(chr(83).chr(1), '&oelig;', $v);
	$v = str_replace(chr(82).chr(1), '&OElig;', $v);
	$v = str_replace(chr(172), '&euro;', $v);
	$v = str_replace(chr(174), '&reg;', $v);
	$v = str_replace(chr(34).chr(33), '&trade;', $v);
	$v = str_replace(chr(38).chr(32), '&hellip;', $v);
//	$v = $v . ' - ' . sprintf('%d, %d, %d', ord($v[0]), ord($v[1]), ord($v[2]));
	$x = preg_quote($y=$p[$i-1], '/');
	$z=$x==$y?'':"# preg : $x\n";
	$x=htmlentities($y);
	$z.=$x==$y?'':"# html : $x\n";
	$vv = str_replace('&amp;', '&', htmlentities(trim($v)));
//	if(preg_match(',(.+)s$,', $y, $r1) && preg_match(',(.+)s$,', $vv, $r2)) { $vv = $r2[1].'$1'; $y = $r1[1].'(s?)'; }
	if(!($i % 2)) {
		$q[$z.'('.$y.')'] = $vv;
		$GLOBALS['ins']["deux $y trois"] = "deux $vv trois";
	}
	$p[$i] = trim($v);
}
$p = '';
foreach($q as $i=>$v)
	$p .= "$i = $v\n";
//print_r($GLOBALS['ins']);
//ecrire_fichier($pp, $p);

?>
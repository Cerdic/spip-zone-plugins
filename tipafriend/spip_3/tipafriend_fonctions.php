<?php
/**
 * Bibliothèque de fonctions du plugin
 *
 * Ce fichier n'est pas indiqué dans le fichier 'plugin.xml' car il n'est nécessaire que
 * pour le traitement du formulaire. Il est donc inclus par ce fichier.
 * @name 		Fonctions
 * @author 		Piero Wbmstr <http://www.spip-contrib.net/PieroWbmstr>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package		Tip-a-friend
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction renvoyant les valeurs globales pour le contexte d'appel des patrons
 *
 * Surchargé par le traitement du formulaire.
 * @return	Array Tableau du contexte pour l'intégration des patrons de mail
 */
function tipafriend_contexte(){
	return array(
		'nom_site' => $GLOBALS['meta']['nom_site'],
		'adresse_site' => $GLOBALS['meta']['adresse_site'],
		'mail_titre' => '',
		'mail_charset' => $GLOBALS['meta']['charset'],
		'mail_patron' => tipafriend_config('patron'),
		'mail_patron_html' => tipafriend_config('patron_html'),
		'mail_lang' => $GLOBALS['meta']['langue_site'],
	);
}

/**
 * Fonction de formatage du titre du mail à 128 caractères
 *
 * => ?? !! une fonction SPIP fait ça si je n'm'abuse ! => à revoir
 *
 * @param	String	Le titre de depart
 * @return	String	Le titre d'arrivee
 */
function tipafriend_titrage($titre){
	$titre = substr($titre, 0, 128);
	return $titre;
}

/**
 * Constructeur des listes d'email
 *
 * Remplace tout caractère de séparaion (typiquement tout ce qui ne peut pas être dans une
 * adresse mail) par un point-virgule puis sépare aux points-virgules
 */
function tipafriend_multimails($str='') {
	$m = explode(';', str_replace( array(',', '/', ':', ' ') , ';', $str));
	$m = array_filter($m);
	return $m;
}

// -----------------------------
// FONCTIONS
// -----------------------------

/**
 * Fonction renvoyant la configuration courante.
 * @param	string	$var Le nom d'une variable de config voulue | optionnel
 * @return	array/string	Array de configuration complet ou valeur de la variable de configuration entrée en paramètre (config utilisateur si présent, sinon config par défaut).
 */
function tipafriend_config($var=''){
	$config = array();
	$a = $GLOBALS['TIPAFRIEND_DEFAULTS'];
	if(isset($GLOBALS['meta']['tipafriend']))
		$a = array_merge($a, unserialize($GLOBALS['meta']['tipafriend']));

	// preparation / rectifications
	foreach($a as $key=>$val){
		if($key == 'options') {
			if(!strlen($val) AND $a['modele'] == $GLOBALS['TIPAFRIEND_DEFAULTS']['modele']) {
				$config['javascript_standard'] = $GLOBALS['TIPAFRIEND_DEFAULTS']['javascript_standard'];
			}
			$config[$key] = str_replace('.html', '', $val);
		}
		elseif($key == 'javascript_standard') {
			if(!isset($config[$key])) $config[$key] = $val;
		}
		elseif($key == 'patron') {
			$config[$key] = str_replace('.html', '', $val);
		}
		else $config[$key] = str_replace('.html', '', $val);
	}

	if(strlen($var)){
		if(isset($config[$var])) return($config[$var]);
		return false;
	}
	return $config;
}

/**
 * Constructeur des blocs de débogue
 */
function taf_dbg_block($tab_dbg=null) {
	if(is_null($tab_dbg)) return;
	if(defined('_TIPAFRIEND_TEST') && _TIPAFRIEND_TEST) {
		$str_dbg = taf_dbg_block_css();
		foreach($tab_dbg as $ttl=>$val) {
			if (is_string($ttl))
				$str_dbg .= "<li><b>$ttl</b><br />$val</li>";
			else $str_dbg .= "<li><b>$val</b></li>";
		}
		return "<div class=\"taf_dbg_global\">"
			."<div class=\"taf_dbg_title\"><small><strong>"._T('tipafriend:taftest_title')."</strong></small></div>"
			."<pre class=\"taf_dbg_pre\"><ul>".$str_dbg."</ul></pre></div>";
	}
	return '';
}

function taf_dbg_block_css() {
	static $TAF_dbg_cssOK=false;
	if ($TAF_dbg_cssOK==true) return '';
	$TAF_dbg_cssOK=true;
	return "<style type=\"text/css\">
/* ---- Blocs de debug ... ---- */
.taf_dbg_global {color:black;border:1px solid #ddd;margin:.1em;padding:0;background:#fff}
.taf_dbg_title {height:20px;background-color:#ddd;border-bottom:1px solid #ddd;padding-left:1em;padding-top:.4em}
pre.taf_dbg_pre {max-height:280px;overflow:auto;color:black;padding:.6em;margin:0}
pre.taf_dbg_pre ul li {margin-bottom:.6em}
</style>";
}

?>
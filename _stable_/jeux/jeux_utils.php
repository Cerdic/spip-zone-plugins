<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('jeux_config');

// sacree compatibilite...
 if (defined('_SPIP19100')) {
	define(_DIR_VAR, _DIR_IMG);
	function set_request($var, $val = NULL) {
		unset($_GET[$var]);
		unset($_POST[$var]);
		if ($val !== NULL) $_GET[$var] = $val;
	}
}

if (!defined('_DIR_PLUGIN_JEUX')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$p=_DIR_PLUGINS.end($p); if ($p[strlen($p)-1]!='/') $p.='/';
	define('_DIR_PLUGIN_JEUX', $p);
}

// 4 fonctions pour traiter la valeur du parametre de configuration place apres le separateur [config]
global $jeux_config;
function jeux_config($param) {
  global $jeux_config;
  $p = trim($jeux_config[$param]);
  if (in_array($p, array('true', 'vrai', 'oui', 'yes', '1', 'si', 'ja', strtolower(_T('item_oui'))))) return true;
  if (in_array($p, array('false', 'faux', 'non', 'no', '0', 'nein', strtolower(_T('item_non'))))) return false;
  return $p;
}
function jeux_config_set($param, $valeur) {
  global $jeux_config;
  if ($param!='') $jeux_config[$param] = $valeur;
}
function jeux_config_init($texte, $ecrase) {
 global $jeux_config;
 $lignes = preg_split("/[\r\n]+/", $texte);
 foreach ($lignes as $ligne) {
  $ligne = preg_replace(',\/\*(.*)\*\/,','', $ligne);
  $ligne = preg_replace(',\/\/(.*)$,','', $ligne);
  if (preg_match('/([^=]+)=(.+)/', $ligne, $regs)) {
    list($p, $v) = array(trim($regs[1]), trim($regs[2]));
	if ($ecrase || ($jeux_config[$p]=='')) $jeux_config[$p] = $v;
  }
 }
}
function jeux_config_reset() {
  global $jeux_config;
  $jeux_config = false;
}

// splitte le texte du jeu avec les separateurs concernes
// et traite les parametres de config
function jeux_split_texte($jeu, &$texte) {
  global $jeux_caracteristiques;
  jeux_config_reset();
  $texte = '['._JEUX_TEXTE.']'.trim($texte).' ';
  $expr = '/(\['.join('\]|\[', $jeux_caracteristiques['SEPARATEURS'][$jeu]).'\])/';
  $tableau = preg_split($expr, $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
//  foreach($tableau as $i => $valeur) $tableau[$i] = preg_replace('/^\[(.*)\]$/', '\\1', trim($valeur));
  foreach($tableau as $i => $valeur) if (($i & 1) && preg_match('/^\[(.*)\]$/', trim($valeur), $reg)) {
   $tableau[$i] = strtolower(trim($reg[1]));
   if ($reg[1]==_JEUX_CONFIG && $i+1<count($tableau)) jeux_config_init($tableau[$i+1], true); 
  }
  return $tableau;
}  

// transforme un texte en listes html 
function jeux_listes($texte) {
	$tableau = preg_split("/[\r\n]+/", trim($texte));	
	foreach ($tableau as $i=>$valeur) if (($valeur=trim($valeur))!='') $tableau[$i] = "<li>$valeur</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>"; 
}

// retourne un tableau de mots ou d'expressions a partir d'un texte
function jeux_liste_mots($texte) {
	$texte = filtrer_entites(trim($texte));
	$split = explode('"', $texte);
	$c = count($split);
	$split2 = array();
	for($i=0; $i<$c; $i++) if (($s = trim($split[$i])) != ""){
		if (($i & 1) && ($i != $c-1)) {
			// on touche pas au texte entre deux ""
			$split2[] = $s;
		} else {
			// on rassemble tous les separateurs : ,;.|\s\t\n
			$temp = preg_replace("/[,;\.\|\s\t\n\r]+/", "\t", $s);
			$temp = str_replace("+"," ", $temp);
			$split2 = array_merge($split2, explode("\t", $temp));
		}
	}
		return array_unique($split2);
}
function jeux_liste_mots_maj($texte) {
	return jeux_liste_mots(strtoupper($texte));
}
function jeux_liste_mots_min($texte) {
	return jeux_liste_mots(strtolower($texte));
}

// retourne la boite de score
function jeux_afficher_score($score, $total, $id_jeu=false) {
	if ($id_jeu){		
		include_spip('base/jeux_ajouter_resultat');
		jeux_ajouter_resulat($id_jeu, $score);
	}
	return '<div class="jeux_score">'._T('jeux:score')
	  			. "&nbsp;$score&nbsp;/&nbsp;".$total.'<br />'
				. ($score==$total?_T('jeux:bravo'):'').'</div>';
}

// fonctions qui retournent des boutons
function jeux_bouton_reinitialiser() {
	return '<div class="jeux_bouton_corriger" align="right">[ <a href="'
	 . parametre_url(self(),'var_mode','recalcul').'">'._T('jeux:reinitialiser').'</a> ]</div>';
}
function jeux_bouton_recommencer() {
	return '<div class="jeux_bouton_corriger" align="right">[ <a href="'
	 . parametre_url(self(),'var_mode','recalcul').'">'._T('jeux:recommencer').'</a> ]</div>';
}

// ajoute un module jeu a la bibliotheque
function jeux_include_jeu($jeu, &$texte, $indexJeux) {
	$fonc = 'jeux_'.$jeu;
	if (!function_exists($fonc)) include_spip('jeux/'.$jeu);
	// on est jamais trop prudent !!
	if (function_exists($fonc)) $texte = $fonc($texte, $indexJeux);
}	

// decode les jeux, si le module jeux/lejeu.php est present
// retourne la liste des jeux trouves et inclut la bibliotheque si $indexJeux existe
function jeux_liste_des_jeux(&$texte, $indexJeux=NULL) {
	global $jeux_caracteristiques;
	$liste = array();
	foreach($jeux_caracteristiques['SIGNATURES'] as $jeu=>$signatures) {
		$ok = false;
		foreach($signatures as $s) $ok |= (strpos($texte, "[$s]")!==false);
		if ($ok) { 
		 if ($indexJeux) jeux_include_jeu($jeu, $texte, $indexJeux);
		 $liste[]=$jeu;
		}
	}
	return array_unique($liste);
}

// retourne les types de jeu trouves dans le $texte
function jeux_trouver_nom($texte) {
	global $jeux_caracteristiques;
	$liste = jeux_liste_des_jeux($texte);
	foreach($liste as $i=>$jeu)
		$liste[$i]=$jeux_caracteristiques['TYPES'][$jeu];
	return join(', ', $liste);
}

// retourne le titre public, si le separateur [titre] est present
function jeux_trouver_titre_public($texte) {
  $titre_public = false;
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('la_totale', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre_public = $tableau[$i+1];
  }
  return $titre_public;
}


// pour placer des commentaires
function jeux_rem($rem, $index=false, $jeu='') {
 return code_echappement("\n<!-- ".$rem.($index!==false?'-#'.$index:'').(strlen($jeu)?" '".$jeu."'":'')." -->\n");
}

// pour inserer un css
function jeux_stylesheet($b) {
 $f = find_in_path("styles/$b.css");
 return $f?'<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />'."\n":'';
}

// pour inserer un css.html
function jeux_stylesheet_html($b) {
 $f = find_in_path("$b.css.html");
 $args = 'ltr=' . $GLOBALS['spip_lang_left'];
 return $f?'<link rel="stylesheet" type="text/css" href="'.generer_url_public("$b.css", $args)."\" >\n"."\n":'';
}

// pour inserer un js
function jeux_javascript($b) {
 $f = find_in_path("javascript/$b.js");
 if (!$f && @is_readable($s = _DIR_IMG_PACK .$b.'.js')) $f = $s;		// compatibilite avec 1.9.1
 return $f?'<script type="text/javascript" src="'.$f.'"></script>'."\n":'';
}

// deux fonctions qui utilisent inc/layer.php
function jeux_block_init() {
  global $spip_version_code;
  if (defined('_SPIP19100')) { $temp = _DIR_IMG_PACK; define(_DIR_IMG_PACK, 'ecrire/'._DIR_IMG_PACK);	}	// compatibilite avec 1.9.1
  include_spip('inc/layer');
   if (defined('_SPIP19100')) define(_DIR_IMG_PACK, $temp);		// compatibilite avec 1.9.1
}
function jeux_block_invisible($id, $texte, $block) {
 if (!strlen($texte)) return '';
 if(function_exists('bouton_block_depliable'))						// fonction introduite en 1.93
 	return bouton_block_depliable($texte, false, $id).debut_block_depliable(false, $id).$block.fin_block();
 else return defined('_SPIP19100')?				// compatibilite avec 1.9.1
	bouton_block_visible($id).$texte.debut_block_visible($id).$block.fin_block()
	:bouton_block_invisible($id).$texte.debut_block_invisible($id).$block.fin_block();
}

// deux fonctions qui encadrent un jeu dans un formulaire
function jeux_form_debut($name, $indexJeux, $class="", $method="post", $action="") {
	if (strlen($name)) $name=" name=\"$name$indexJeux\"";
	if (strlen($class)) $class=" class=\"$class\"";
	if (strlen($method)) $method=" method=\"$method\"";
	/*if (strlen($action))*/ $action=" action=\"$action#JEU$indexJeux\"";
	return "\n<form".$name.$class.$method.$action." >\n"
		."<input type=\"hidden\" name=\"debut_index_jeux\" value=\"{$GLOBALS['debut_index_jeux']}\" />\n"
		."<input type=\"hidden\" name=\"index_jeux\" value='$indexJeux' />\n"
		."<input type=\"hidden\" name=\"var_correction_$indexJeux\" value='1' />\n";
}
function jeux_form_fin() {
	return "\n</form>\n";
}

?>
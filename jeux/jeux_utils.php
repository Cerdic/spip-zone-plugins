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
			// on rassemble tous les separateurs : ,;.\s\t\n
			$temp = preg_replace("/[,;\.\s\t\n\r]+/", "\t", $s);
			$temp = str_replace("+"," ", $temp);
			$split2 = array_merge($split2, explode("\t", $temp));
		}
	}
	return array_unique($split2);
}
function jeux_majuscules($texte) {
	return init_mb_string()?mb_strtoupper($texte,$GLOBALS['meta']['charset']):strtoupper($texte);
}
function jeux_minuscules($texte) {
	return init_mb_string()?mb_strtolower($texte,$GLOBALS['meta']['charset']):strtolower($texte);
}
function jeux_in_liste($texte, $liste=array()) {
	// eviter un pb d'apostrophe par exemple
	$texte = filtrer_entites(corriger_typo($texte));
	$texte_m = jeux_minuscules($texte);
	foreach($liste as $expr) {
		// interpretation des expressions regulieres grace aux virgules : ,un +mot,i
		if(strpos($expr, ',')===0) {
			if(preg_match($expr, $texte)) return true;
		} elseif(strpos($expr, '/M')===($len=strlen($expr)-2)) {
			if(substr($expr,0,$len)===$texte) return true;
		} else {
			if(jeux_minuscules($expr)===$texte_m) return true;
		}
	}
	return false;
}

// retourne la boite de score
function jeux_afficher_score($score, $total, $id_jeu=false, $resultat_long='', $categories='') {
	if ($id_jeu){
		// ici, #CONTENU* est passe par le filtre |traite_contenu_jeu{#ID_JEU}
		include_spip('base/jeux_ajouter_resultat');
		jeux_ajouter_resultat($id_jeu, $score, $total, $resultat_long);
	}
	include_spip('public/assembler');
	return recuperer_fond('fonds/jeu_score', 
		array('id_jeu'=>$id_jeu, 'score'=>$score, 'total'=>$total,
			'resultat_long'=>$resultat_long, 
			'commentaire'=>jeux_commentaire_score($categories, $score, $total)
		)
	);
}

function jeux_commentaire_score($categ, $score, $total) {
	if(!strlen(categ)) return '';
	$score = intval($score);
	$total = intval($total);
	$res = false;
	$categ = preg_split('@(^|\n|\r)\s*(-?[0-9]+[.,]?[0-9]*)\s*(%|pt|pts)(?:\s|&nbsp;)*:@', trim($categ), -1, PREG_SPLIT_DELIM_CAPTURE);
	for($i=2; $i<count($categ); $i+=4) {
		$mini = $categ[$i+1]=='%'?$total*$categ[$i]/100:$categ[$i];
		if($score > $total) $res=false;
		elseif($score >= $mini) $res = $i+2;
		else break;
	}
	return $res===false?'':$categ[$res];
}

// fonctions qui retournent des boutons
function jeux_bouton_reinitialiser($item='jeux:reinitialiser') {
	$self = self(); // nettoyer_uri();
	if($id_jeu = _request('id_jeu')) $self = parametre_url($self, 'id_jeu', $id_jeu);
	return '<div class="jeux_bouton_reset">&#091; <a href="'
	 . parametre_url($self, 'var_mode', 'recalcul').'">' . _T($item) . '</a> &#093;</div>';
}
function jeux_bouton_recommencer() {
	return jeux_bouton_reinitialiser('jeux:recommencer');
}

// ajoute un module jeu a la bibliotheque
function jeux_include_jeu($jeu, &$texte, $indexJeux) {
	if (!function_exists($fonc = 'jeux_'.$jeu))
		include_spip('jeux/'.$jeu);
	if (function_exists($fonc))
		$texte = $fonc($texte, $indexJeux);
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
  $texte = str_replace(array('<jeux>','</jeux>'), '', $texte);
  $titre_public = false;
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('la_totale', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre_public = $tableau[$i+1];
  }
  return $titre_public;
}

// retourne la configuration interne, si le separateur [config] est present
function jeux_trouver_configuration_interne($texte) {
  $texte = str_replace(array('<jeux>','</jeux>'), '', $texte);
  $configuration_interne = array();
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('la_totale', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	if ($valeur==_JEUX_CONFIG) {
		$lignes = preg_split(",[\r\n]+,", $tableau[$i+1]);
		foreach ($lignes as $ligne) {
			$ligne = trim($ligne);
		 	if(strlen($ligne)) $configuration_interne[] = $ligne;
		}
	}
  }
  return $configuration_interne;
}


// pour placer des commentaires
function jeux_rem($rem, $index=false, $jeu='', $balise='div') {
 return code_echappement("<$balise class='jeux_rem' title='".$rem.($index!==false?'-#'.$index:'').(strlen($jeu)?" `".$jeu."`":'')."'></$balise>");
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

// pour obtenir un bloc depliable
function jeux_block_depliable($texte, $block) {
 if (!strlen($texte)) return '';
 return "<div class='jeux_deplie jeux_replie'>$texte</div><div class='jeux_deplie_contenu jeux_invisible'>$block</div>";
}

// deux fonctions qui encadrent un jeu dans un formulaire
function jeux_form_debut($name, $indexJeux, $class="", $method="post", $action="") {
	if (strlen($name)) $name=" name='$name$indexJeux'";
	if (strlen($class)) $class=" class='$class'";
	if (strlen($method)) $method=" method='$method'";
	/*if (strlen($action))*/ $action=" action='$action#JEU$indexJeux'";
	$id_jeu = intval(jeux_config('id_jeu'));
	return "\n<form".$name.$class.$method.$action." >\n"
		."<input type='hidden' name='id_jeu' value='$id_jeu' />\n"
		."<input type='hidden' name='debut_index_jeux' value='{$GLOBALS['debut_index_jeux']}' />\n"
		."<input type='hidden' name='index_jeux' value='$indexJeux' />\n"
		."<input type='hidden' name='var_correction_$indexJeux' value='1' />\n";
}
function jeux_form_fin() {
	return "\n</form>\n";
}

?>

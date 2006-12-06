<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

// 4 fonctions pour traiter la valeur du parametre de configuration place apres le separateur [config]
global $jeux_config;
function jeux_config($param) {
  global $jeux_config;
  return in_array($jeux_config[$param], array('oui', 'yes', '1', 'si', 'ja', strtolower(_T('item_oui'))));
}
function jeux_config_set($param, $valeur) {
  global $jeux_config;
  if ($param!='') $jeux_config[$param] = $valeur;
}
function jeux_config_init($texte) {
 $lignes = preg_split("/\r?\n/", $texte);
 foreach ($lignes as $ligne)
  if (preg_match('/([^=]+)=(.+)/', $ligne, $regs)) jeux_config_set(trim($regs[1]), trim($regs[2]));
}
function jeux_config_reset() {
  global $jeux_config;
  $jeux_config = false;
}

// splitte le texte du jeu avec les separateurs concernes
// et traite les parametres de config
function jeux_split_texte($jeu, &$texte) {
  global $jeux_separateurs;
  jeux_config_reset();
  $texte = '['._JEUX_TEXTE.']'.trim($texte).' ';
  $expr = '/(\['.join('\]|\[', $jeux_separateurs[$jeu]).'\])/';
  $tableau = preg_split($expr, $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
//  foreach($tableau as $i => $valeur) $tableau[$i] = preg_replace('/^\[(.*)\]$/', '\\1', trim($valeur));
  foreach($tableau as $i => $valeur) if (($i & 1) && preg_match('/^\[(.*)\]$/', trim($valeur), $reg)) {
   $tableau[$i] = strtolower(trim($reg[1]));
   if ($reg[1]==_JEUX_CONFIG && $i+1<count($tableau)) jeux_config_init($tableau[$i+1]); 
  }
  return $tableau;
}  

// transforme un texte en listes html 
function jeux_listes($texte) {
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$valeur) if (($valeur=trim($valeur))!='') $tableau[$i] = "<li>$valeur</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>"; 
}

// retourne un tableau de mots ou d'expressions a partir d'un texte
function jeux_liste_mots($texte) {
	$texte = filtrer_entites(trim($texte));
	$texte = preg_replace("/[,;\.\|\s\t\n\r]+/", " ", $texte);
	$split = split('"', $texte);
	$c = count($split);
	for($i=0; $i<$c; $i++) if ($i & 1) $split[$i] = str_replace(' ','+', $split[$i]);
	$texte = join('', $split);
	$texte = str_replace(" ","\t", $texte);
	$texte = str_replace("+"," ", $texte);
	return (array_unique(split("\t", $texte)));
}
function jeux_liste_mots_maj($texte) {
	return jeux_liste_mots(strtoupper($texte));
}
function jeux_liste_mots_min($texte) {
	return jeux_liste_mots(strtolower($texte));
}

// retourne la boite de score
function jeux_afficher_score($score, $total) {
	return '<center><div class="jeux_score">'._T('jeux:score')
	  			. "&nbsp;$score&nbsp;/&nbsp;".$total.'<br>'
				. ($score==$total?_T('jeux:bravo'):'').'</div></center>';
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
function include_jeux($jeu, &$texte, $indexJeux) {
	$fonc = 'jeux_'.$jeu;
	if (!function_exists($fonc)) include_spip('inc/'.$jeu);
	// on est jamais trop prudent !!
	if (function_exists($fonc)) $texte = $fonc($texte, $indexJeux);
}	

// inclut et decode les jeux, si le module inc/lejeu.php est present
function jeux_inclure_et_decoder(&$texte, $indexJeux) {
	global $jeux_signatures;
	foreach($jeux_signatures as $jeu=>$signatures) {
		$ok = false;
		foreach($signatures as $s) $ok |= (strpos($texte, "[$s]")!==false);
		if ($ok) include_jeux($jeu, $texte, $indexJeux);
	}
}

// pour placer des commentaires
function jeux_rem($rem, $index=false) {
 return code_echappement("\n<!-- ".$rem.($index!==false?'-#'.$index:'')." -->\n");
}

// pour inserer un css en public
function jeux_stylesheet_public($b) {
 $f = find_in_path("styles/$b.css");
 return $f?'<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />'."\n":'';
}

// pour inserer un css en prive
function jeux_stylesheet_prive($b) {
 $f = find_in_path("styles/$b.css");
 return $f?'<link rel="stylesheet" href="'.$f.'" type="text/css" media="projection, screen" />'."\n":'';
// return '<link rel="stylesheet" href="'._DIR_PLUGIN_JEUX."styles/$b.css\" type=\"text/css\" media=\"projection, screen\" />\n";
}

// pour inserer un js
function jeux_javascript($b) {
 $f = find_in_path("javascript/$b.js");
 return $f?'<script type="text/javascript" src="'.$f.'"></script>'."\n":'';
}

// deux fonctions qui utilisent inc/layer.php
function jeux_block_init() {
  include_spip('inc/layer');
  verif_butineur();
}
function jeux_block_invisible($id, $texte, $block) {
 return $texte?bouton_block_invisible($id).$texte.debut_block_invisible($id).$block.fin_block():'';
}

?>
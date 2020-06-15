<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('jeux_config');

// 4 fonctions pour traiter la valeur du parametre de configuration place apres le separateur [config]
global $jeux_config;

function jeux_config($param, $config = false) {
  global $jeux_config;
  $param = jeux_verif_param_config($param);
  $p = $config === false ? $jeux_config : $config;
  $p = isset($p[$param]) ? trim($p[$param]) : "";

  if (in_array($p, array('true', 'vrai', 'oui', 'yes', 'on',  '1', 'si', 'ja', strtolower(_T('item_oui'))))) return true;
  if (in_array($p, array('false', 'faux', 'non', 'no', 'off', '0', 'nein', strtolower(_T('item_non'))))) return false;
  $p = trim($p, '"');
  return $p;
}

function jeux_config_tout() {
  global $jeux_config;
  return $jeux_config;
}

function jeux_config_set($param, $valeur) {
  global $jeux_config;
  $param = jeux_verif_param_config($param);
  if ($param != '')
	  $jeux_config[$param] = trim($valeur);
}

// alimente la globale $jeux_config, tout en preferant les valeurs de la configuration CFG du plugin
// le texte est une liste de config de type : param = valeur
// $ecrase : forcer a ecraser la config eventuellement existante
function jeux_config_init($texte_config, $ecrase = false) {
	global $jeux_config;
	// configuration CFG
	$config_CFG = jeux_configuration_generale(true);
	$parametres = jeux_parse_section_config($texte_config);
	foreach ($parametres as $p) {
		// array($ligne epuree, $param_original, $param, $valeur)
		list( , , $p_base, $valeur) = $p;
		// au moment de la config initiale, preferer les valeurs de CFG
		if (!$ecrase && !isset($jeux_config[$p_base]))
			$jeux_config[$p_base] = isset($config_CFG[$p_base]) ? $config_CFG[$p_base] : $valeur;
		if ($ecrase || !isset($jeux_config[$p_base]))
			$jeux_config[$p_base] = $valeur;
	}
}

// ecrase les parametres de la globale $jeux_config;
function jeux_config_ecrase($texte_config) { 
	jeux_config_init($texte_config, true); 
}

// reinitialise la globale $jeux_config;
function jeux_config_reset() {
  global $jeux_config;
  $jeux_config = array();
}

// splitte le texte du jeu avec les separateurs concernes
// et traite les parametres de config
function jeux_split_texte($jeu, &$texte) {
  global $jeux_caracteristiques;
  jeux_config_reset();
  if (function_exists($init = 'jeux_'  .$jeu . '_init'))
	  jeux_config_init($init());
  $texte = '[' . _JEUX_TEXTE . ']' . trim($texte) . ' ';
  $expr = '/(\[' . join('\]|\[', $jeux_caracteristiques['SEPARATEURS'][$jeu]) . '\])/';
  $tableau = preg_split($expr, $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
//  foreach($tableau as $i => $valeur) $tableau[$i] = preg_replace('/^\[(.*)\]$/', '\\1', trim($valeur));
  foreach($tableau as $i => $valeur)
	  if (($i & 1) && preg_match('/^\[(.*)\]$/', trim($valeur), $reg)) {
	    $tableau[$i] = strtolower(trim($reg[1]));
	    if ($reg[1] == _JEUX_CONFIG && $i+1 < count($tableau))
			jeux_config_ecrase($tableau[$i+1]); 
	  }
  return $tableau;
}  

// transforme un texte en listes html 
function jeux_listes($texte) {
	$tableau = preg_split('/[\r\n]+/', trim($texte));	
	foreach ($tableau as $i=>$valeur)
		if (($valeur = trim($valeur)) != '')
			$tableau[$i] = "<li>$valeur</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>";
}

// retourne un tableau de mots ou d'expressions a partir d'un texte
function jeux_liste_mots($texte, $unique = true) {
	// corrections typo eventuelles
	$texte = str_replace(
		array('&#8217;','&laquo;&nbsp;','&nbsp;&raquo;','&laquo; ',' &raquo;','&nbsp;?','&nbsp;!','&nbsp;;'),
		array("'",'"','"','"','"','?','!',';'), echappe_retour($texte));
	$texte = filtrer_entites(trim($texte));
	$split = explode('"', $texte);
	$c = count($split);
	$split2 = array();
	for($i=0; $i<$c; $i++) if (strlen($s = trim($split[$i]))) {
		if (($i & 1) && ($i != $c-1)) {
			// on touche pas au texte entre deux ""
			$split2[] = $s;
		} else {
			// on rassemble tous les separateurs : ,;\s\t\n
			$temp = str_replace(array(' ?', ' !', ' ;'), array('?', '!', ';'), $s);
			$temp = preg_replace('/[,;\s\t\n\r]+/'.($GLOBALS['meta']['charset']=='utf-8'?'u':''), '@SEP@', $temp);
			$temp = str_replace('+', ' ', $temp);
			$temp = array_filter(explode('@SEP@', $temp));
			$split2 = array_merge($split2, $temp);
		}
	}
	return $unique ? array_unique($split2) : $split2;
}

function jeux_majuscules($texte) {
	return init_mb_string()?mb_strtoupper($texte,$GLOBALS['meta']['charset']):strtoupper($texte);
}

function jeux_minuscules($texte) {
	return init_mb_string()?mb_strtolower($texte,$GLOBALS['meta']['charset']):strtolower($texte);
}

// verifie si le $texte est bien dans la $liste de bonnes reponses
// les expression regulieres sont egalement traitees
function jeux_in_liste($texte, $liste=array()) {
	$texte = filtrer_entites($texte);
	$texte_m = jeux_minuscules($texte);
	foreach($liste as $expr) {
		// interpretation des expressions regulieres grace aux virgules : ,un +mot,i
		if(strncmp($expr, ',', 1)===0 && ($pos = strripos($expr, ','))>0) {
			if($GLOBALS['meta']['charset']=='utf-8' && strpos($expr, 'u', ++$pos)===false) $expr .= 'u';
			if(preg_match($expr, $texte)) return true;
		} elseif(strpos($expr, '/M')===($len=strlen($expr)-2)) {
			if(substr($expr, 0, $len)===$texte) return true;
		} else {
			$expr = jeux_minuscules($expr);
			// corriger_typo peut eviter un pb d'apostrophe par exemple
			if($expr===$texte_m || $expr===corriger_typo($texte_m)) 
				return true;
		}
	}
	return false;
}

function jeux_filtre_preg($array) {
	foreach($array as $key => $value)
		if(strncmp($value, ',', 1)===0 && ($pos = strripos($value, ','))>0) 
			$array[$key] = substr($value, 1, --$pos);
	return $array;
}

// retourne la boite de score et ajoute le resultat en base
function jeux_afficher_score($score, $total, $id_jeu=false, $resultat_long='', $categories='') {
	global $scoreMULTIJEUX;
	if(isset($scoreMULTIJEUX['config'])) {
		// mode 'multi_jeux' : enregistrement des scores dans la globale $scoreMULTIJEUX, mais pas en base
		$scoreMULTIJEUX['score'][] = $score;
		$scoreMULTIJEUX['total'][] = $total;
		$scoreMULTIJEUX['details'][] = $resultat_long;
		if(!jeux_config('scores_intermediaires', $scoreMULTIJEUX['config'])) return '';
	}
	elseif($id_jeu){
		// mode 'jeu simple'
		// ici, le jeu est decode et possede un id_jeu ; les scores peuvent passer en base.
		include_spip('base/jeux_ajouter_resultat');
		jeux_ajouter_resultat($id_jeu, $score, $total, $resultat_long);
	}
	include_spip('public/assembler');
	return recuperer_fond('fonds/jeu_score', 
		array('id_jeu'=>$id_jeu, 'score'=>$score, 'total'=>$total,
			'resultat_long'=>$resultat_long, 
			'commentaire'=>jeux_commentaire_score($categories, $score, $total)
		));
}

function jeux_commentaire_score($categ, $score, $total) {
	if (!strlen($categ)) return '';

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

// fonction qui retourne un bouton, faisant appel au fond fonds/bouton_{$item}.html
function jeux_bouton($item, $id_jeu = 0, $indexJeux = 0) {
	return recuperer_fond('fonds/bouton_'.$item, array('id_jeu' => $id_jeu, 'indexJeux' => $indexJeux));
}
// fonctions obsoletes
function jeux_bouton_reinitialiser() { return jeux_bouton('reinitialiser'); }
function jeux_bouton_recommencer() { return jeux_bouton('recommencer'); }
function jeux_bouton_corriger() { return jeux_bouton('corriger'); }
function jeux_bouton_rejouer() { return jeux_bouton('rejouer'); }

// liste les jeux trouves, si le module jeux/lejeu.php est present
function jeux_liste_les_jeux(&$texte) {
	global $jeux_caracteristiques;
	$liste = array();
	foreach($jeux_caracteristiques['SIGNATURES'] as $jeu => $signatures) {
		$ok = false;
		foreach($signatures as $s) $ok |= (strpos($texte, "[$s]")!==false);
		if ($ok) $liste[] = $jeu;
	}
	return array_unique($liste);
}

// decode les jeux si les modules jeux/lejeu.php sont presents
// retourne la liste unique des jeux trouves et inclut la bibliotheque si $indexJeux existe
function jeux_decode_les_jeux(&$texte, $indexJeux = NULL) {
	global $jeux_caracteristiques, $scoreMULTIJEUX;
	$liste = array();
	foreach($jeux_caracteristiques['SIGNATURES'] as $jeu=>$signatures) {
		$ok = false;
		foreach($signatures as $s) $ok |= (strpos($texte, "[$s]")!==false);
		if ($ok) {
		 $liste[] = $jeu;
		 if ($indexJeux) {
			if (!function_exists($fonc = 'jeux_'.$jeu))
				include_spip('jeux/'.$jeu);
			if (function_exists($fonc))
				$texte = $fonc($texte, $indexJeux, !isset($scoreMULTIJEUX['config']));
		 }
		}
	}
	return array_unique($liste);
}

// retourne les types de jeu trouves dans le $texte
function jeux_trouver_nom($texte) {
	global $jeux_caracteristiques;
	$liste = jeux_liste_les_jeux($texte);
	foreach($liste as $i => $jeu)
		$liste[$i] = $jeux_caracteristiques['TYPES'][$jeu];
	return join(', ', $liste);
}

function jeux_sans_balise($texte) {
  return str_replace(array(_JEUX_DEBUT,_JEUX_FIN), '', $texte);
}

// retourne le titre public, si le separateur [titre] est present
function jeux_trouver_titre_public($texte) {
  $texte = jeux_sans_balise($texte);
  $titre_public = false;
  // cas particulier des multi-jeux
  if($p = strpos($texte,'['._JEUX_MULTI_JEUX.']'))
	  $texte = substr($texte,0,$p);
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('la_totale', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre_public = $tableau[$i+1];
  }
  return $titre_public;
}

// appel de la balise #CONFIG_INTERNE
// si $param est une chaine non vide, alors la valeur d'un parametre en particulier est retournee
// si $param === '' alors un tableau associatif est retourne
// si $param === false alors un texte HTML est retourne
function jeux_trouver_configuration_complete($texte_jeu, $param = false) {
	global $jeux_caracteristiques;

	if(strpos($texte_jeu, _JEUX_DEBUT)!==false) 
		return "Balise &lt;JEUX/&gt; trouv&eacute;e => Pas encore trait&eacute; !";

	$liste_jeux = jeux_liste_les_jeux($texte_jeu);
	if(!count($liste_jeux)) return "Aucun jeu trouv&eacute;, sauf errer ?" . print_r($texte_jeu, 1);
	$nb_jeux = count($liste_jeux);

	// ici un texte de jeu est fourni
	// $nom_jeu = jeux_trouver_nom($texte_jeu);
	$config_interne = jeux_trouver_configuration_interne($texte_jeu, false, true);
	$config_defaut = jeux_trouver_configuration_defaut($liste_jeux[0], true);
//return print_r($config_defaut, 1);
	// verification des parametres de la configuration interne
	$config_interne = explode('<br/>', $config_interne);
	foreach ($config_interne as &$ligne) {
		// array(param, valeur, param original, ligne epuree)
		$arr = jeux_parse_ligne_config($ligne);
		if($arr[0] && $arr[3] && !isset($config_defaut[$arr[0]]))
				$ligne =  '/* ' . _T('jeux:erreur_inconnu') . ' : ' . $ligne . ' */';
	}
	sort($config_interne);
	$config_interne = join('<br/>', $config_interne);
	$config_defaut = jeux_trouver_configuration_defaut($liste_jeux[0], false);

	return '<p><strong>' . _T('jeux:titre_config_defaut') 
		. ' <small>(' . $jeux_caracteristiques['TYPES'][$liste_jeux[0]] . ')</small> :'
		. '</strong></p><p class="jeux_decaler_config">' . $config_defaut . '</p>'
		. ($config_interne ? '<p><strong>' . _T('jeux:titre_config_interne')
			. ' :</strong></p><p class="jeux_decaler_config">' . $config_interne . '</p>' : '');

}

// retourne la configuration interne d'un texte de jeu, si le separateur [config] est present
// si une option generale de configuration est cochee, elle prendra le dessus (sauf si $ignorer_CFG == true)
// si strlen($param) > 0 alors la valeur d'un parametre en particulier est retournee
// si $param === '' alors un tableau associatif est retourne
// si $param === false alors un texte HTML est retourne
function jeux_trouver_configuration_interne($texte, $param = false, $ignorer_CFG = false) {
  $texte = jeux_sans_balise($texte);
  $affiche_config_interne = array();
  $ok_param = false;
  $config_CFG = jeux_configuration_generale(true);

  // cas particulier des multi-jeux
  if($p = strpos($texte,'['._JEUX_MULTI_JEUX.']'))
	  $texte = substr($texte, 0, $p);
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('la_totale', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	if ($valeur ==_JEUX_CONFIG) {
		$parametres = jeux_parse_section_config($tableau[$i+1]);
		foreach ($parametres as $p) {
			// array($ligne epuree, $param_original, $param, $valeur, $ligne_originale)
			list($ligne, $p_user, $p_base, $valeur, $ligneOrig) = $p;
			if($p_base !== false) {
				// la configuration generale passe avant !
				if (!$ignorer_CFG && isset($config_CFG[$p_base]) && $valeur != $config_CFG[$p_base]) {
					$valeur = $config_CFG[$p_base];
					$ligne = "/* CFG */ $p_user = $valeur";
				}
				if ($param === '') {
					$ok_param[$p_base] = $valeur;
				} elseif ($param === false) {
					$affiche_config_interne[] = $ligne;
				} else {
					$param = jeux_verif_param_config($param);
					if($p_base == $param) $ok_param = $valeur;
				}
			} else {
				$affiche_config_interne[] = '/* ' . _T('jeux:erreur_syntaxe') . ' : ' . $ligneOrig . ' */';
			}
		}
	}
  }
  if ($param === false) {
	  sort($affiche_config_interne);
	  return join('<br/>', $affiche_config_interne);
  }
  return $ok_param;
}

// retourne la configuration par defaut d'un type jeu
function jeux_trouver_configuration_defaut($type_jeu, $type_array = false) {
	if (!function_exists($fonc = 'jeux_'.$type_jeu))
		include_spip('jeux/' . $type_jeu);
	if (function_exists($init = $fonc . '_init'))
		return jeux_trouver_configuration_interne('['._JEUX_CONFIG.']' . $init(), $type_array ? '' : false);
	return false;
}

// retourne la configuration generale du plugin (options par defaut gerees par CFG)
// retour un array() si $type_array est true
function jeux_configuration_generale($type_array = false) {
	$affiche_cfg = array();
	if(function_exists('lire_config') && is_array($liste_cfg = lire_config('jeux'))) {
		// liste des options disponibles par CFG
		foreach($liste_cfg as $o => $v) {
			$o = jeux_verif_param_config(preg_replace(',^cfg_,', '', $o));
			if($v === true || $v === 'on')
				$v = strtolower(_T('item_oui'));
			elseif($v === false || $v === 'off')
				$v = strtolower(_T('item_non'));
			$affiche_cfg[$o] = $type_array ? $v : "$o = $v";
		}
	}
	if($type_array)
		return $affiche_cfg;
	if(!$affiche_cfg) 
		$affiche_cfg = array(_T('jeux:jeu_vide'));
	return '[' . _JEUX_CONFIG . "]<br/>" . join("<br/>", $affiche_cfg);
}

// decoder une ligne de config (retrait des comentaires facon PHP et mise en forme du parametre)
// retour : array(param, valeur, param original, ligne epuree)
function jeux_parse_ligne_config($ligne) {
	$ligne = preg_replace(',\/\*(.*?)\*\/,', '', $ligne);
	$ligne = trim(preg_replace(',\/\/(.*)$,', '', $ligne));
	$arr = explode('=', $ligne, 2);
	if (count($arr) != 2) return array(false, false, false, strlen($ligne)? '/* ' . $ligne . '*/' : false);
	$arr[0] = trim($arr[0]); $arr[1] = trim($arr[1]);
	return array(jeux_verif_param_config($arr[0]), $arr[1], $arr[0], $arr[0] . ' = ' . $arr[1]);
}

// decoder une section de config
// retourne le tableau des parametres : array[] = array($ligne epuree, $param_original, $param, $valeur, $ligne_originale)
function jeux_parse_section_config($texte_config) {
	$arr_config = array();
	$texte_config = preg_split('/[\r\n]+/', $texte_config);
	foreach ($texte_config as $ligne) {
		// array(param, valeur, param original, ligne epuree)
		$arr = jeux_parse_ligne_config($ligne);
		if(strlen($arr[3])) {
			if ($arr[0])
				$arr_config[] = array($arr[3], $arr[2], $arr[0], $arr[1], $ligne);
			else
				$arr_config[] = array($arr[3], false, false, false, $ligne);
		}
	}
	return $arr_config;
}

// le parametre de config d'un jeu est insensible a la casse et ne comporte ni espace ni accent
function jeux_verif_param_config($param) {
	include_spip('inc/charsets');
	return strtolower(translitteration(str_replace(' ', '', $param)));
}


// pour placer des commentaires
function jeux_rem($rem, $index=false, $jeu='', $balise='div') {
 return code_echappement("<$balise class='jeux_rem' title='".$rem.($index!==false?'-#'.$index:'').(strlen($jeu)?" `".$jeu."`":'')."'></$balise>");
}

// pour inserer un css
function jeux_stylesheet($b) {
 $f = find_in_path("styles/$b.css");
 include_spip('inc/filtres');
 return $f?'<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />'."\n":'';
}

// pour inserer un css.html
function jeux_stylesheet_html($b) {
 $f = find_in_path("$b.css.html");
 $args = 'ltr=' . $GLOBALS['spip_lang_left'];
 return $f?'<link rel="stylesheet" type="text/css" href="'.generer_url_public("$b.css", $args)."\" />\n":'';
}

// pour inserer un js
function jeux_javascript($b) {
 $f = find_in_path("javascript/$b.js");
 if (!$f && @is_readable($s = _DIR_IMG_PACK .$b.'.js')) $f = $s;		// compatibilite avec 1.9.1
 return $f?'<script type="text/javascript" src="'.$f."\"></script>\n":'';
}

// renvoie un bloc depliable
function jeux_block_depliable($texte, $block) {
 if (!strlen($texte)) return '';
 return "<div class='jeux_deplie jeux_replie'>$texte</div><div class='jeux_deplie_contenu jeux_invisible'>$block</div>";
}

// renvoie le couple array(id, name) pour construire un input
// les index doivent etre positifs
function jeux_idname($indexJeux, $index=-1, $prefixe='', $index2=-1, $prefixe2='') {
	$indexJeux = str_replace('.','_',$indexJeux);
	$indexJeux = 'reponses' . $indexJeux;
	if($index<0) return array($indexJeux, $indexJeux);
	if($index2<0) return array($indexJeux.'-'.$prefixe.$index, $indexJeux.'['.$prefixe.$index.']');
	return array($indexJeux.'-'.$prefixe.$index.'-'.$prefixe2.$index2, $indexJeux.'['.$prefixe.$index.']['.$prefixe2.$index2.']');
}

// renvoie la reponse trimee du formulaire (NULL si n'existe pas)
function jeux_form_reponse($indexJeux, $index=-1, $prefixe='', $index2=-1, $prefixe2='') {
  	$reponse = _request('reponses'.str_replace('.','_',$indexJeux));
	if($index>=0 && is_array($reponse))
		$reponse = isset($reponse[$prefixe.$index])?$reponse[$prefixe.$index]:NULL;
	if($index2>=0 && is_array($reponse))
		$reponse = isset($reponse[$prefixe2.$index2])?$reponse[$prefixe2.$index2]:NULL;
	if(is_string($reponse) && strlen($reponse))
		$reponse = trim($reponse);
	return $reponse;
}

// indique si on doit corriger ou non (attention aux multi jeux)
function jeux_form_correction($indexJeux) {
	return intval(_request('correction'.intval($indexJeux)))?true:false;
}

// deux fonctions qui encadrent un jeu dans un formulaire
function jeux_form_debut($name, $indexJeux, $class="", $method="post", $action="", $insert="") {
	$id_jeu = intval(jeux_config('id_jeu'));
	$cvt = jeux_config('jeu_cvt')?'oui':'non';
	$hidden = "<div><input type='hidden' name='id_jeu' value='$id_jeu' />\n"
		."<input type='hidden' name='debut_index_jeux' value='$GLOBALS[debut_index_jeux]' />\n"
		."<input type='hidden' name='index_jeux' value='$indexJeux' />\n"
		."<input type='hidden' name='correction$indexJeux' value='1' />$insert</div>\n";
	if (jeux_config('jeu_cvt')) return $hidden;
	if (strlen($name)) $name=" id='$name$indexJeux'";
	if (strlen($class)) $class=" class='$class'";
	if (strlen($method)) $method=" method='$method'";
	/*if (strlen($action))*/ $action=" action='$action#JEU$indexJeux'";
	return "\n<form".$name.$class.$method.$action." >\n".$hidden;
}
function jeux_form_fin() {
	return jeux_config('jeu_cvt')?'':"\n</form>\n";
}

?>

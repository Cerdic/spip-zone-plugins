<?php
if (!isset($GLOBALS['spip_version_branche']) OR intval($GLOBALS['spip_version_branche'])<2){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FRIMOUSSES',(_DIR_PLUGINS.end($p)).'/');
}

function frimousses_liste_smileys() {
  
  /*Listes des images a associer aux smileys*/

  $les_smileys = array();
  $les_smileys[':-)*'] = 'face13-16.png';
  $les_smileys[':-)'] = 'face1-16.png'; 
  $les_smileys['o:)'] = 'face18-16.png'; 
  $les_smileys['O:)'] = 'face18-16.png'; 
  $les_smileys['0:)'] = 'face18-16.png'; 
  $les_smileys[':)'] = 'face1-16.png'; 
  $les_smileys['%-)'] = 'face2-16.png'; 
  $les_smileys[';-)'] = 'face3-16.png'; 
  $les_smileys[';)'] = 'face3-16.png'; 
  $les_smileys[':-('] = 'face4-16.png'; 
  $les_smileys[':('] = 'face4-16.png'; 
  $les_smileys[':-O'] = 'face5-16.png';
  $les_smileys[':O)'] = 'face7-16.png'; 
  $les_smileys[':O'] = 'face5-16.png';
  $les_smileys[':-D'] = 'face6-16.png'; 
  $les_smileys[':D'] = 'face6-16.png'; 
  $les_smileys[':o)'] = 'face7-16.png'; 
  $les_smileys[':0)'] = 'face7-16.png'; 
  $les_smileys[':0'] =  'face5-16.png';
  $les_smileys[':-|'] = 'face8-16.png'; 
  $les_smileys[':|'] = 'face8-16.png'; 
  $les_smileys[':-/'] = 'face9-16.png'; 
  $les_smileys[':/'] = 'face9-16.png'; 
  $les_smileys[':-p'] = 'face10-16.png'; 
  $les_smileys[':p'] = 'face10-16.png'; 
  $les_smileys[':\'-('] = 'face11-16.png'; 
  $les_smileys[':\'('] = 'face11-16.png'; 
  $les_smileys[':-...'] = 'face12-16.png'; 
  $les_smileys[':...'] = 'face12-16.png'; 
  $les_smileys[':-..'] = 'face12-16.png'; 
  $les_smileys[':..'] = 'face12-16.png'; 
  $les_smileys[':-.'] = 'face12-16.png'; 
  $les_smileys[':.'] = 'face12-16.png'; 
  $les_smileys[':-x'] = 'face14-16.png'; 
  $les_smileys[':x'] = 'face14-16.png'; 
  $les_smileys['B-)'] = 'face15-16.png'; 
  $les_smileys['B)'] = 'face15-16.png'; 
  $les_smileys[':-@'] = 'face16-16.png'; 
  $les_smileys[':@'] = 'face16-16.png'; 
  $les_smileys[':$'] = 'face17-16.png'; 
  $les_smileys[':-*'] = 'face19-16.png'; 
  $les_smileys[':*'] = 'face19-16.png'; 
  $les_smileys[':-!'] = 'face20-16.png'; 
  $les_smileys[':!'] = 'face20-16.png'; 
  $les_smileys['8-)'] = 'face21-16.png'; 
  $les_smileys['8)'] = 'face21-16.png'; 
  $les_smileys['MONK'] = 'face22-16.png'; 	 
  $les_smileys['|-)'] = 'face23-16.png'; 
  $les_smileys['|)'] = 'face23-16.png'; 
  $les_smileys['O-)'] = 'face24-16.png'; 
  $les_smileys['O)'] = 'face24-16.png'; 
  $les_smileys['ATTN'] = 'important-16.png'; 
  $les_smileys['SVNT'] = 'puce-16.png';  

  return $les_smileys;
}

// Filtre SMILEYS - 19 Dec. 2004
//
// pour toute suggestion, remarque, proposition d'ajout d'un
// smileys, etc ; reportez vous au forum de l'article :
// http://www.spip-contrib.net/Smileys-III-Un-point-d-entree-pour

function frimousses_pre_propre($chaine) {
	static $replace1 = null;
	static $replace2 = null;
	if (!$replace1 OR !$replace2){
		foreach(frimousses_liste_smileys() as $smiley => $file) {
			$alt = _T('smileys:'.$smiley);
		  $alt = attribut_html($alt);
			$smiley = preg_quote($smiley,'/');
			$r = "<img src=\"".find_in_path('frimousses/'.$file).'" width="16" height="16" alt="'.$alt.'" class="smiley" />';
			// 4 regexp simples qui accrochent sur le premier char
			// sont plus rapides qu'une regexp complexe qui oblige a des retour en arriere
			$replace1['/^'.$smiley.'/imsS'] = "<html>$r</html>";
			$replace1['/\s'.$smiley.'/imsS'] = "<html>&nbsp;$r</html>";
			$replace2['/^&nbsp;'.$smiley.'/imsS'] = "<html>$r</html>";
			$replace2['/&nbsp;'.$smiley.'/imsS'] = "<html>&nbsp;$r</html>";
		}
  }

	// pas de & dans les smileys donc on peut remplacer avec 1 OU 2 uniquement
    $chaine = preg_replace(array_keys($replace1),array_values($replace1),$chaine);
	if (strpos($chaine,'&')!==false)
		$chaine = preg_replace(array_keys($replace2),array_values($replace2),$chaine);

	return $chaine;
}

function balise_SMILEY_DISPO($p) {

  $p->code = '"<ul class=\"listes-items smileys\">';
  foreach(frimousses_liste_smileys() as $smiley => $file) {
		$alt = _T('smileys:'.$smiley);
		$alt = attribut_html($alt);
		$p->code .= "<li class=\\\"item smiley\\\"><span class=\\\"smiley_nom\\\">$smiley</span><img  class=\\\"smiley_image\\\" src=\\\"".find_in_path("frimousses/$file")."\\\" width=\\\"16\\\" height=\\\"16\\\" alt=\\\"$alt\\\"/><span class=\\\"smiley_alt\\\" />$alt</span></li>\n";
  }
  $p->code .= '</ul>"';
  $p->type = 'html';
  
  return $p;
}

?>
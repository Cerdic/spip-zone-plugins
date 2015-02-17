<?php
if (!isset($GLOBALS['spip_version_branche']) OR intval($GLOBALS['spip_version_branche'])<2){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FRIMOUSSES',(_DIR_PLUGINS.end($p)).'/');
}

function frimousses_liste_smileys() {
  
  /*Listes des images a associer aux smileys*/

	$les_smileys = array();
	$les_smileys[':-)*'] = 'smiley-kiss-16.png';
	$les_smileys[':-))'] = 'smiley-lol-16.png';
	$les_smileys[':-)'] = 'smiley-16.png';
	$les_smileys['o:)'] = 'smiley-angel-16.png';
	$les_smileys['O:)'] = 'smiley-angel-16.png';
	$les_smileys['0:)'] = 'smiley-angel-16.png';
	$les_smileys[':))'] = 'smiley-lol-16.png';
	$les_smileys[':)'] = 'smiley-16.png';
	$les_smileys['%-)'] = 'smiley-eek-16.png';
	$les_smileys[';-)'] = 'smiley-wink-16.png';
	$les_smileys[';)'] = 'smiley-wink-16.png';
	$les_smileys[':-(('] = 'smiley-sad-16.png';
	$les_smileys[':-('] = 'smiley-sad-16.png';
	$les_smileys[':('] = 'smiley-sad-16.png';
	$les_smileys[':-O'] = 'smiley-yell-16.png';
	$les_smileys[':O)'] = 'smiley-16.png';
	$les_smileys[':O'] = 'smiley-yell-16.png';
	$les_smileys[':-D'] = 'smiley-lol-16.png';
	$les_smileys[':D'] = 'smiley-lol-16.png';
	$les_smileys[':o)'] = 'smiley-16.png';
	$les_smileys[':0)'] = 'smiley-16.png';
	$les_smileys[':0'] =  'smiley-yell-16.png';
	$les_smileys[':-|'] = 'smiley-neutral-16.png';
	$les_smileys[':|'] = 'smiley-neutral-16.png';
	$les_smileys[':-/'] = 'smiley-confuse-16.png';
	$les_smileys[':/'] = 'smiley-confuse-16.png';
	$les_smileys[':-p'] = 'smiley-razz-16.png';
	$les_smileys[':-P'] = 'smiley-razz-16.png';
	$les_smileys[':p'] = 'smiley-razz-16.png';
	$les_smileys[':P'] = 'smiley-razz-16.png';
	$les_smileys[':\'-('] = 'smiley-cry-16.png';
	$les_smileys[':\'('] = 'smiley-cry-16.png';
	$les_smileys[':-...'] = 'smiley-red-16.png';
	$les_smileys[':...'] = 'smiley-red-16.png';
	$les_smileys[':-..'] = 'smiley-red-16.png';
	$les_smileys[':..'] = 'smiley-red-16.png';
	$les_smileys[':-.'] = 'smiley-red-16.png';
	$les_smileys[':.'] = 'smiley-red-16.png';
	$les_smileys[':-x'] = 'smiley-zipper-16.png';
	$les_smileys[':x'] = 'smiley-zipper-16.png';
	$les_smileys['B-)'] = 'smiley-cool-16.png';
	$les_smileys['B)'] = 'smiley-cool-16.png';
	$les_smileys[':-@'] = 'smiley-sleep-16.png';
	$les_smileys[':@'] = 'smiley-sleep-16.png';
	$les_smileys[':$'] = 'smiley-money-16.png';
	$les_smileys[':-*'] = 'smiley-kiss-16.png';
	$les_smileys[':*'] = 'smiley-kiss-16.png';
	$les_smileys[':-!'] = 'smiley-roll-16.png';
	$les_smileys[':!'] = 'smiley-roll-16.png';
	$les_smileys['8-)'] = 'smiley-eek-16.png';
	$les_smileys['8)'] = 'smiley-eek-16.png';
	$les_smileys['|-)'] = 'smiley-neutral-16.png';
	$les_smileys['|)'] = 'smiley-neutral-16.png';

	return $les_smileys;
}

// Filtre SMILEYS - 19 Dec. 2004
//
// pour toute suggestion, remarque, proposition d'ajout d'un
// smileys, etc ; reportez vous au forum de l'article :
// http://www.spip-contrib.net/Smileys-III-Un-point-d-entree-pour

function frimousses_pre_propre($chaine) {
	if (strpos($chaine, ':')===false && strpos($chaine, ')')===false) {return $chaine;}

	static $replace1 = null;
	static $replace2 = null;
	if (!$replace1 OR !$replace2){
		foreach(frimousses_liste_smileys() as $smiley => $file) {
			$alt = _T('smileys:'.$smiley);
		  $alt = attribut_html($alt);
			$smiley = preg_quote($smiley,'/');
			$r = "<img src=\"".find_in_path('frimousses/'.$file).'" width="16" height="16" alt="'.$alt.'" title="'.$alt.'" class="smiley" />';
			// 4 regexp simples qui accrochent sur le premier char
			// sont plus rapides qu'une regexp complexe qui oblige a des retour en arriere
			$replace1['/^'.$smiley.'/imsS'] = "<html>$r</html>";
			$replace1['/\s'.$smiley.'/imsS'] = "<html>&nbsp;$r</html>";
			$replace2['/^&nbsp;'.$smiley.'/imsS'] = "<html>$r</html>";
			$replace2['/&nbsp;'.$smiley.'/imsS'] = "<html>&nbsp;$r</html>";
		}
  }

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
		$p->code .= "<li class=\\\"item smiley\\\"> <span class=\\\"smiley_nom\\\">$smiley</span><img  class=\\\"smiley_image\\\" src=\\\"".find_in_path("frimousses/$file")."\\\" width=\\\"16\\\" height=\\\"16\\\" alt=\\\"$alt\\\"/> <span class=\\\"smiley_alt\\\" />$alt</span></li>\n";
  }
  $p->code .= '</ul>"';
  $p->type = 'html';
  
  return $p;
}

?>
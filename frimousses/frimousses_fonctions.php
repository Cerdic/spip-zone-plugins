<?php
if (!isset($GLOBALS['spip_version_branche']) OR intval($GLOBALS['spip_version_branche'])<2){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FRIMOUSSES',(_DIR_PLUGINS.end($p)).'/');
}

function frimousses_liste_smileys() {
  
  /*Listes des images a associer aux smileys*/

	$les_smileys = Array
		(
			'smiley-lol-16.png' => Array
				(
					':-))',
					':-D',
					':D',
					':))'
				)
			,
			'smiley-kiss-16.png' => Array
				(
					':-)*',
					':-*',
					':*'
				)
			,
			'smiley-16.png' => Array
				(
					':-)',
					':)',
					':O)',
					':o)',
					':0)'
				)
			,
			'smiley-angel-16.png' => Array
				(
					'o:)',
					'O:)',
					'0:)'
				)
			,
			'smiley-eek-16.png' => Array
				(
					'%-)',
					'8-)',
					'8)'
				)
			,
			'smiley-wink-16.png' => Array
				(
					';-)',
					';)'
				)
			,
			'smiley-sad-16.png' => Array
				(
					':-((',
					':-(',
					':('
				)
			,
			'smiley-yell-16.png' => Array
				(
					':-O',
					':O',
					':0'
				)
			,
			'smiley-neutral-16.png' => Array
				(
					':-|',
					':|',
					'|-)',
					'|)'
				)
			,
			'smiley-confuse-16.png' => Array
				(
					':-/',
					':/'
				)
			,
			'smiley-razz-16.png' => Array
				(
					':-p',
					':-P',
					':p ',
					':P '
				)
			,
			'smiley-cry-16.png' => Array
				(
					':\'-(',
					':\'(',
					':~('
				)
			,
			'smiley-red-16.png' => Array
				(
					':-...',
					':...',
					':-..',
					':..',
					':-.',
					':.'
				)
			,
			'smiley-zipper-16.png' => Array
				(
					':-x',
					':x'
				)
			,
			'smiley-cool-16.png' => Array
				(
					'B-)',
					'B)'
				)
			,
			'smiley-sleep-16.png' => Array
				(
					':-@',
					':@'
				)
			,
			'smiley-money-16.png' => Array
				(
					':$'
				)
			,
			'smiley-roll-16.png' => Array
				(
					':-!',
					':!'
				)

		);

	return $les_smileys;
}

// Filtre SMILEYS - 19 Dec. 2004
//
// pour toute suggestion, remarque, proposition d'ajout d'un
// smileys, etc ; reportez vous au forum de l'article :
// http://www.spip-contrib.net/Smileys-III-Un-point-d-entree-pour

function frimousses_pre_typo($chaine) {
	if (strpos($chaine, ':')===false && strpos($chaine, ')')===false) {return $chaine;}

	static $replace1 = null;
	static $replace2 = null;
	if (!$replace1 OR !$replace2){
		foreach(frimousses_liste_smileys() as $file => $smileys) {
			$alt = _T('smileys:'.$smileys[0]);
			$alt = attribut_html($alt);
			$r = "<img src=\"".find_in_path('frimousses/'.$file).'" width="16" height="16" alt="'.$alt.'" title="'.$alt.'" class="smiley" />';
			// 4 regexp simples qui accrochent sur le premier char
			// sont plus rapides qu'une regexp complexe qui oblige a des retour en arriere
			foreach($smileys as $index => $smiley) {
				$smiley = preg_quote($smiley, '/');
				$replace1['/^'.$smiley.'/imsS'] = "<html>$r</html>";
				$replace1['/\s'.$smiley.'/imsS'] = "<html>&nbsp;$r</html>";
				$replace2['/^&nbsp;'.$smiley.'/imsS'] = "<html>$r</html>";
				$replace2['/&nbsp;'.$smiley.'/imsS'] = "<html>&nbsp;$r</html>";
			}
		}
  }

  $chaine = preg_replace(array_keys($replace1),array_values($replace1),$chaine);
	if (strpos($chaine,'&')!==false)
		$chaine = preg_replace(array_keys($replace2),array_values($replace2),$chaine);

	return $chaine;
}

function balise_SMILEY_DISPO($p) {

  $p->code = '"<ul class=\"listes-items smileys\">';
  $frimousses = frimousses_liste_smileys();

  foreach($frimousses as $file => $smiley) {
		$alt = _T('smileys:'.$smiley[0]);
		$alt = attribut_html($alt);
		$smiley = "<span class=\\\"smiley_nom_variante\\\">".implode("</span> <span class=\\\"smiley_nom_variante\\\">", $smiley)."</span>";
		$p->code .= "<li class=\\\"item smiley\\\"><span class=\\\"smiley_nom\\\">$smiley</span> <img  class=\\\"smiley_image\\\" src=\\\"".find_in_path("frimousses/$file")."\\\" width=\\\"16\\\" height=\\\"16\\\" alt=\\\"$alt\\\" title=\\\"$alt\\\"/> <span class=\\\"smiley_alt\\\" />$alt</span></li>\n";
  }
  $p->code .= '</ul>"';
  $p->type = 'html';
  
  return $p;
}

?>
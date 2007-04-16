<?php

function coloration_code_traiter($regs) {
	global $spip_lang_right, $spip_lang_left;
	$rempl ='';

	$texte = $regs[0];
  if (preg_match_all(
		 ',<(cadre|code)[[:space:]]+class=("|\')(.*)\2([^>]*)>(.*)</\1>,Uims',
		 $texte, $matches, PREG_SET_ORDER))
	foreach ($matches as $regs) {
	  $code = echappe_retour($regs[5]);
	  $params = explode(' ', $regs[3]);
	  $language = array_shift($params);
	  $telecharge = 
		(PLUGIN_COLORATION_CODE_TELECHARGE || in_array('telecharge', $params))
	   && (strpos($code, "\n") !== false) && !in_array('sans_telecharge', $params);
	  if ($telecharge) {
	  // Gerer le fichier contenant le code au format texte
		$nom_fichier = md5($code);
		$dossier = sous_repertoire(_DIR_VAR, 'cache-code');
		$fichier = "$dossier$nom_fichier.txt";

		if (!file_exists($fichier)) {
			$handle = fopen($fichier, 'w');
			fwrite($handle, $code);
			fclose($handle);
		}
	  }
	 
	  $rempl = '<div class="coloration_code"><div style="text-align: $spip_lang_left;" class="spip_'.$regs[1].' '.$language.'">'.coloration_code_color(trim($code),$language, $regs[1]).'</div>';
	  if ($telecharge) {
	 	$rempl .= "<div class='".$regs[1]."_download' style='text-align: $spip_lang_right;'><a href='$fichier' style='font-family: verdana, arial, sans; font-weight: bold; font-style: normal;'>".
		  _T('colorationcode:telecharger').
	  		"</a></div>";
	  }
	  $rempl .= "</div>";
	  $texte = str_replace($regs[0],$rempl,$texte);
	} else {
		$texte = traiter_echap_cadre_dist($regs);
	}
  return $texte;
}


function traiter_echap_cadre($regs) {
	$echap = coloration_code_traiter($regs);

	return $echap;
}




?>
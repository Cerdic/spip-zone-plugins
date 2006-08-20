<?php

 define("_LG_ANCRE", 35);

	function AncresIntertitres_ancres_intertitres($texte) {
		$regexp = "/{{{(.*)}}}/UmsS";
		$texte = preg_replace_callback($regexp,
   create_function('$matches',
    'return AncresIntertitres_remplace_intertitre($matches);'),
   $texte);
		return $texte;
	}
	
	function AncresIntertitres_lien_de_retour($texte, $ancre_retour = "tdm") {
		$regexp = '/@@RETOUR_TDM@@/S';
		$rempl = '<a href="#'.$ancre_retour.'"><img src="' .
  find_in_path('tdm.png') . 
  '" alt="' .
   _T('tdm:retour_table_matiere') .
   '" title="' .
   _T('tdm:retour_table_matiere') .
   '" /></a>';
  $texte = preg_replace($regexp, $rempl, $texte);
		return $texte;
	}

	function AncresIntertitres_remplace_intertitre($matches) {
		static $cId = 0;
		$cId++;
		$url = translitteration(corriger_caracteres(
			supprimer_tags(supprimer_numero(extraire_multi(trim($matches[1]))))
		));
		$url = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $url);
		// S'il reste des caracteres non latins, utiliser l'id a la place
		if (preg_match(",[^a-zA-Z0-9 ],", $url)) {
			$url = "ancre$cId";
		}
		else {
			$mots = explode(' ', $url);
			$url = '';
			foreach ($mots as $mot) {
				if (!$mot) continue;
				$url2 = $url.'-'.$mot;
				if (strlen($url2) > _LG_ANCRE) {
					break;
				}
			$url = $url2;
			}
			$url = substr($url, 1);
			if (strlen($url) < 2) $url = "ancre$cId";
		}
		AncresIntertitres_table_matiere('', $url, $matches[1]);
		return '{{{ ['.$url.'<-] '.$matches[1].' @@RETOUR_TDM@@ }}}';
	}
	
	function AncresIntertitres_table_matiere($mode = '', $url = '', $titre ='') {
		static $tableau = array();
		if($mode == 'retour') return $tableau;
		$tableau[$url] = $titre;
		return '';
	}
	  
?>
<?php

//
// http://www.spip-contrib.net/spikini/VarianteContribAjouter-des-ID-aux-intertitres
//

	function AncresIntertitres_ancres_intertitres($texte) {
		$regexp = "/{{{[[:space:]]*(.+)[[:space:]]*}}}/";
		$texte = preg_replace_callback($regexp, create_function('$matches','return AncresIntertitres_remplace_intertitre($matches);'), $texte);
		return $texte;
	}
	
	function AncresIntertitres_remplace_intertitre($matches) {
		static $cId = 0;
		$cId++;
		$url = translitteration(corriger_caracteres(
			supprimer_tags(supprimer_numero(extraire_multi($matches[1])))
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
				if (strlen($url2) > 35) {
					break;
				}
			$url = $url2;
			}
			$url = substr($url, 1);
			if (strlen($url) < 2) $url = "ancre$cId";
		}
		AncresIntertitres_table_matiere('', $url, $matches[1]);
		return '{{{ ['.$url.'<-] '.$matches[1].' }}}';
	}
	
	function AncresIntertitres_table_matiere($mode = '', $url = '', $titre ='') {
		static $tableau = array();
		if($mode == 'retour') return $tableau;
		$tableau[$url] = $titre;
		return '';
	}
	
	function AncresIntertitres_compose_table_matiere($table_matiere, $avant, $apres) {
		$texte = '';
		if(!empty($table_matiere))
			foreach($table_matiere as $url => $titre)
				$texte .= $avant.'<a href="#'.$url.'">'.$titre.'</a>'.$apres."\n";
		return $texte;	
	}
  
?>

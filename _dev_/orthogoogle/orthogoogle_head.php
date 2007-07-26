<?php

	function orthogoogle_header_prive($flux){

		// determine le chemin des script √† charger
		$dir_spell = find_in_path('lib/googiespell_v4_0/googiespell');
		
		// lib:googiespell manquante
		if (!$dir_spell)
			return $flux;

		// si page de configuration, on ne charge rien
		//parse le flux en tant qu'xml
		$doc = new DOMDocument;
		$doc->loadHTML($flux);

		//charge une action xpath		
		$xpath = new DOMXPath($doc);
		$res=$xpath->query('/html/head/title[contains(.,"OrthoGoogle")]');
		
		if ($res->length > 0)
			return $flux;

		// si aucun champ √† traiter
		if (!lire_config('orthogoogle'))
			return $flux;

		$dir_spell .= '/';

		$AJS_url = $dir_spell.'AJS.js';

		$spelljs_url = $dir_spell.'googiespell.js';
		$spellmulti_url = $dir_spell.'googiespell_multiple.js';
		$cookiejs_url = $dir_spell.'cookiesupport.js';
		
		
		//determine le chemin du proxy pour acceder ‡†google speller
		$proxy_url = _DIR_PLUGIN_ORTHOGOOGLE.'sendReq.php';
		
		//determine le chemin de la mise en page
		$css_url = $dir_spell.'googiespell.css';
		
		//ins√©re dans le <head> les appels aux scripts
		$flux .= '<script type="text/javascript" src="'.$AJS_url.'"></script>
		<script type="text/javascript" src="'.$spelljs_url.'">	</script>
		<script type="text/javascript" src="'.$spellmulti_url.'">	</script>
		<script type="text/javascript" src="'.$cookiejs_url.'"></script>
		<link href="'.$css_url.'" rel="stylesheet" type="text/css" />';
	
		//definit la chaine des champs autoris√©s √† la correction (obtenu par cfg)
		$chaine = "";
		
		//parcours les infos sauvÈes, si l'Ètat "on" alors corrigeable
		foreach(lire_config('orthogoogle') as $key => $champ) {
			if ($champ = "on") {
				$chaine .= $key.","; 	
			}
		}
		//supprime la , finale
		$chaine = substr($chaine,0,strlen($chaine)-1);
		
		//applique le correcteur orthographique ‡ chaque textarea defini dans cfg
		$flux .='<script type="text/javascript">
		$(document).ready(function() {
			var chaine = "'.$chaine.'";
			//charge le correcteur pour chaque textarea identifi√©
		    var googie = new GoogieSpellMultiple("'.$dir_spell.'", "'.$proxy_url.'?lang=");
			//var googie = new GoogieSpell("'.$dir_spell.'", "'.$proxy_url.'?lang=");
			//traduit les messages
			googie.lang_chck_spell = "'._T("orthogoogle:lang_chck_spell").'";
			googie.lang_rsm_edt = "'._T("orthogoogle:lang_rsm_edt").'";
			googie.lang_close = "'._T("orthogoogle:lang_close").'";
			googie.lang_no_error_found = "'._T("orthogoogle:lang_no_error_found").'";
			googie.lang_revert = "'._T("orthogoogle:lang_revert").'";
			//definit les champs orthogooglisable			
			googie.decorateTextareas(chaine);
 		});
		</script>';
		
		return $flux;
	
	}
	
?>



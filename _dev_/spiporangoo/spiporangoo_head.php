<?php

	function spiporangoo_header_prive($flux){
		
		//determine le chemin des script à charger
		$AJS_url = find_in_path('googiespell_v4_0/googiespell/AJS.js');
		$spelljs_url = find_in_path('googiespell_v4_0/googiespell/googiespell.js');
		$spellmulti_url = find_in_path('googiespell_v4_0/googiespell/googiespell_multiple.js');
		$cookiejs_url = find_in_path('googiespell_v4_0/googiespell/cookiesupport.js');
		$dir_spell = find_in_path('googiespell_v4_0/googiespell/');
		
		
		//determine le chemin du proxy pour acceder à google speller
		$proxy_url = find_in_path('sendReq.php');
		
		//determine le chemin de la mise en page
		$css_url = find_in_path('googiespell_v4_0/googiespell/googiespell.css');
		
		//insére dans le <head> les appels aux scripts
		$flux .= '<script type="text/javascript" src="'.$AJS_url.'"></script>
		<script type="text/javascript" src="'.$spelljs_url.'">	</script>
		<script type="text/javascript" src="'.$spellmulti_url.'">	</script>
		<script type="text/javascript" src="'.$cookiejs_url.'"></script>
		<link href="'.$css_url.'" rel="stylesheet" type="text/css" />';
	
		//applique le correcteur orthographique à chaque textarea trouvé
		$flux .='<script type="text/javascript">
		$(document).ready(function() {
	        $("textarea").addClass("textarea"); // affecte la classe textarea
			//recherche les textarea présent dans la page
			var chaine = "";
			$("textarea").each(function(i){
					if ($(this).attr(\'id\')) {
						chaine += $(this).attr(\'id\') + ",";							 
					} else {
						//$(this).attr(\'id\',$(this).attr(\'name\'));
					}					
			}
			);
			chaine = chaine.substr(0,chaine.length-1);
			//charge le correcteur pour chaque textarea identifié
		    var googie5 = new GoogieSpellMultiple("'.$dir_spell.'/", "'.$proxy_url.'?lang=");
			googie5.decorateTextareas(chaine);
 		});
		</script>';
		
		return $flux;
	
	}

?>
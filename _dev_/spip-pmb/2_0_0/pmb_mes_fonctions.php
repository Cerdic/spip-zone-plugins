<?php
include_spip('base/pmb_tables');

function extraire_element_distant ($url, $selector) {
			$resultat_recherche_locale = copie_locale($url,'auto');
			if($resultat_recherche_locale != false) {
					$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
					
					$resultat_recherche_html = str_replace("addtags.php", "#PMB_URL_BASEaddtags.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("avis.php", "#PMB_URL_BASEavis.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("./do_resa.php", "#PMB_URL_BASEdo_resa.php", $resultat_recherche_html);
					
					$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
					
					require(find_in_path('simple_html_dom.php'));
					$htmldom = str_get_html($resultat_recherche_html);
					
					foreach($htmldom->find($selector) as $e) {
  		 			 	return $e->innertext;
					}
					
					
			}
		}


?>
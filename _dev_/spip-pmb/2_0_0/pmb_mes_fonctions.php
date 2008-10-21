<?php
include_spip('base/pmb_tables');

function extraire_element_distant ($url_base, $file, $selector) {
			$resultat_recherche_locale = copie_locale($url_base.$file,'auto');
			if($resultat_recherche_locale != false) {
					$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
					
					$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
					
					$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
					
					require(find_in_path('simple_html_dom.php'));
					$htmldom = str_get_html($resultat_recherche_html);
					
					foreach($htmldom->find($selector) as $e) {
  		 			 	return $e->innertext;
					}
					
					
			}
		}

// retourne le champ d'une notice pmb parmi le titre, descriptif.. 
function pmb_notice_extraire ($id_notice, $url_base) {
	$tableau_resultat = Array();
	$resultat_recherche_locale = copie_locale($url_base."index.php?lvl=notice_display&id=".$id_notice,'auto');
	if($resultat_recherche_locale != false) {
		$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
		
					$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
					
					$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
					
					
					require(find_in_path('simple_html_dom.php'));
					
					$htmldom = str_get_html($resultat_recherche_html);
					
					
					$tableau_resultat['titre_complet'] = $htmldom->find('span.header_title', 0)->innertext;
					$tableau_resultat['auteur'] = substr($htmldom->find('#drag_noti_'.$id_notice, 0)->innertext, strrpos($htmldom->find('#drag_noti_'.$id_notice, 0)->innertext, '/')+1); 
					
					$tablechamps = $htmldom->find('#notice table table tr');
					foreach($tablechamps as $tr) {
						$libelle = htmlentities($tr->find('td',0)->innertext);
						$valeur = $tr->find('td',1)->innertext;
						if (strpos($libelle, 'Titre de s')) $tableau_resultat['serie'] = $valeur; 
						if (strpos($libelle, 'Titre')) $tableau_resultat['titre'] = $valeur; 
						else if (strpos($libelle, 'Type de document')) $tableau_resultat['type'] = $valeur; 
						else if (strpos($libelle, 'Editeur')) $tableau_resultat['editeur'] = $valeur; 
						else if (strpos($libelle, 'Auteurs')) $tableau_resultat['lesauteurs'] = $valeur; 
						else if (strpos($libelle, 'de publication')) $tableau_resultat['annee_publication'] = $valeur; 
						else if (strpos($libelle, 'Collection')) $tableau_resultat['collection'] = $valeur; 
						else if (strpos($libelle, 'Importance')) $tableau_resultat['importance'] = $valeur; 
						else if (strpos($libelle, 'Présentation')) $tableau_resultat['presentation'] = $valeur; 
						else if (strpos($libelle, 'Format')) $tableau_resultat['format'] = $valeur; 
						else if (strpos($libelle, 'Importance')) $tableau_resultat['importance'] = $valeur; 
						else if (strpos($libelle, 'ISBN')) $tableau_resultat['isbn'] = $valeur; 
						else if (strpos($libelle, 'Prix')) $tableau_resultat['prix'] = $valeur; 
						else if (strpos($libelle, 'Langues')) $tableau_resultat['langues'] = $valeur; 
						else if (strpos($libelle, 'sum')) $tableau_resultat['resume'] = $valeur; 
						
					}
						

	}
	return $tableau_resultat;
			
}
function pmb_notice_champ ($tableau_resultat, $champ) {
	return $tableau_resultat[$champ];
}

?>
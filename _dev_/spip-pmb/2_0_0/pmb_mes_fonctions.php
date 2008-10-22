<?php
include_spip('base/pmb_tables');

function pmb_charger_page ($url_base, $file) {
	$resultat_recherche_html = "";
	$resultat_recherche_locale = copie_locale($url_base."index.php?lvl=search_result&user_query=".$recherche,'auto');
	if($resultat_recherche_locale != false) {
		$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
		
		$resultat_recherche_html = str_replace("page=", "pmb_page=", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
	}
	return $resultat_recherche_html;		
					

}

function pmb_editeur_extraire($id_editeur, $url_base, $pmb_page=1) {
	$tab_resultat = Array();
	$resultat_recherche_locale = copie_locale($url_base."index.php?lvl=publisher_see&page=".$pmb_page."&id=".$id_editeur,'auto');
	if($resultat_recherche_locale != false) {
		$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
		
					$resultat_recherche_html = str_replace("page=", "pmb_page=", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
					
					$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
					
					
					require(find_in_path('simple_html_dom.php'));
					
					$htmldom = str_get_html($resultat_recherche_html);
					//info générales sur l'éditeur					
					$tab_resultat[0] = Array();
					$tab_resultat[0]['titre_editeur'] = $htmldom->find('#aut_see h3',0)->innertext;
					$tab_resultat[0]['collections_editeur'] = $htmldom->find('#aut_see ul',0)->outertext;
					$tab_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
					$infos_editeur = $htmldom->find('#aut_see p');
					$tab_resultat[0]['infos_editeur'] = '';
					foreach($infos_editeur as $p_editeur) {
						$tab_resultat[0]['infos_editeur'] .= $p_editeur->outertext;
					}
					

					//ouvrages de l'éditeur
					$resultats_recherche = $htmldom->find('.child');
					$i = 1;
					foreach($resultats_recherche as $res) {
						$id_notice = intval(substr($res->id,2));
						$tab_resultat[$i] = Array();
						$tab_resultat[$i]['id'] = $id_notice;	
						$tab_resultat[$i]['logo_src'] = $res->find('table img',0)->src; 
											

						$tablechamps = $res->find('table table tr');
						foreach($tablechamps as $tr) {
							$libelle = htmlentities($tr->find('td',0)->innertext);
							$valeur = $tr->find('td',1)->innertext;
							if (strpos($libelle, 'Titre de s')) $tab_resultat[$i]['serie'] = $valeur; 
							if (strpos($libelle, 'Titre')) $tab_resultat[$i]['titre'] = $valeur; 
							else if (strpos($libelle, 'Type de document')) $tab_resultat[$i]['type'] = $valeur; 
							else if (strpos($libelle, 'Editeur')) $tab_resultat[$i]['editeur'] = $valeur; 
							else if (strpos($libelle, 'Auteurs')) $tab_resultat[$i]['lesauteurs'] = $valeur; 
							else if (strpos($libelle, 'de publication')) $tab_resultat[$i]['annee_publication'] = $valeur; 
							else if (strpos($libelle, 'Collection')) $tab_resultat[$i]['collection'] = $valeur; 
							else if (strpos($libelle, 'Importance')) $tab_resultat[$i]['importance'] = $valeur; 
							else if (strpos($libelle, 'Présentation')) $tab_resultat[$i]['presentation'] = $valeur; 
							else if (strpos($libelle, 'Format')) $tab_resultat[$i]['format'] = $valeur; 
							else if (strpos($libelle, 'Importance')) $tab_resultat[$i]['importance'] = $valeur; 
							else if (strpos($libelle, 'ISBN')) $tab_resultat[$i]['isbn'] = $valeur; 
							else if (strpos($libelle, 'Prix')) $tab_resultat[$i]['prix'] = $valeur; 
							else if (strpos($libelle, 'Langues')) $tab_resultat[$i]['langues'] = $valeur; 
							else if (strpos($libelle, 'sum')) $tab_resultat[$i]['resume'] = $valeur; 
						
						}
						$i++;
			
					}
	}
	return $tab_resultat;
}

function pmb_recherche_extraire($recherche, $url_base, $look_FIRSTACCESS=0, $look_ALL=0, $look_AUTHOR=0, $look_PUBLISHER=0, $look_COLLECTION=0, $look_SUBCOLLECTION=0, $look_CATEGORY=0, $look_INDEXINT=0, $look_KEYWORDS=0, $look_TITLE=0, $look_ABSTRACT=0) {
	$tableau_resultat = Array();
	

	$resultat_recherche_locale = copie_locale($url_base."index.php?lvl=search_result&look_ALL=".$look_ALL."&look_FIRSTACCESS=".$look_FIRSTACCESS."&look_AUTHOR=".$look_AUTHOR."&look_PUBLISHER=".$look_PUBLISHER."&look_COLLECTION=".$look_COLLECTION."&look_CATEGORY=".$look_CATEGORY."&look_INDEXINT=".$look_INDEXINT."&look_KEYWORDS=".$look_KEYWORDS."&look_ABSTRACT=".$look_ABSTRACT."&user_query=".$recherche,'auto');
	if($resultat_recherche_locale != false) {
		$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
		
					$resultat_recherche_html = str_replace("page=", "pmb_page=", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
					
					$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
					
					
					require(find_in_path('simple_html_dom.php'));
					
					$htmldom = str_get_html($resultat_recherche_html);
					$tab_resultat[0] = Array();
					$tab_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
										

					$resultats_recherche = $htmldom->find('.child');
					$i = 1;
					foreach($resultats_recherche as $res) {
						$id_notice = intval(substr($res->id,2));
						$tableau_resultat[$i] = Array();
						$tableau_resultat[$i]['id'] = $id_notice;	
						$tableau_resultat[$i]['logo_src'] = $res->find('table img',0)->src; 
											

						$tablechamps = $res->find('table table tr');
						foreach($tablechamps as $tr) {
							$libelle = htmlentities($tr->find('td',0)->innertext);
							$valeur = $tr->find('td',1)->innertext;
							if (strpos($libelle, 'Titre de s')) $tableau_resultat[$i]['serie'] = $valeur; 
							if (strpos($libelle, 'Titre')) $tableau_resultat[$i]['titre'] = $valeur; 
							else if (strpos($libelle, 'Type de document')) $tableau_resultat[$i]['type'] = $valeur; 
							else if (strpos($libelle, 'Editeur')) $tableau_resultat[$i]['editeur'] = $valeur; 
							else if (strpos($libelle, 'Auteurs')) $tableau_resultat[$i]['lesauteurs'] = $valeur; 
							else if (strpos($libelle, 'de publication')) $tableau_resultat[$i]['annee_publication'] = $valeur; 
							else if (strpos($libelle, 'Collection')) $tableau_resultat[$i]['collection'] = $valeur; 
							else if (strpos($libelle, 'Importance')) $tableau_resultat[$i]['importance'] = $valeur; 
							else if (strpos($libelle, 'Présentation')) $tableau_resultat[$i]['presentation'] = $valeur; 
							else if (strpos($libelle, 'Format')) $tableau_resultat[$i]['format'] = $valeur; 
							else if (strpos($libelle, 'Importance')) $tableau_resultat[$i]['importance'] = $valeur; 
							else if (strpos($libelle, 'ISBN')) $tableau_resultat[$i]['isbn'] = $valeur; 
							else if (strpos($libelle, 'Prix')) $tableau_resultat[$i]['prix'] = $valeur; 
							else if (strpos($libelle, 'Langues')) $tableau_resultat[$i]['langues'] = $valeur; 
							else if (strpos($libelle, 'sum')) $tableau_resultat[$i]['resume'] = $valeur; 
						
						}
						$i++;
			
					}	
	}
	return $tableau_resultat;

}


// retourne un tableau associatif contenant tous les champs d'une notice 
function pmb_notice_extraire ($id_notice, $url_base) {
	$tableau_resultat = Array();
	$resultat_recherche_locale = copie_locale($url_base."index.php?lvl=notice_display&id=".$id_notice,'auto');
	if($resultat_recherche_locale != false) {
		$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
		
					$resultat_recherche_html = str_replace("page=", "pmb_page=", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
					$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
					
					$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
					
					require(find_in_path('simple_html_dom.php'));
					
					$htmldom = str_get_html($resultat_recherche_html);
					
					
					$tableau_resultat['titre_complet'] = $htmldom->find('span.header_title', 0)->innertext;
					$tableau_resultat['auteur'] = substr($htmldom->find('#drag_noti_'.$id_notice, 0)->innertext, strrpos($htmldom->find('#drag_noti_'.$id_notice, 0)->innertext, '/')+1); 
					$tableau_resultat['logo_src'] = $htmldom->find('#notice table img',0)->src; 
					$tableau_resultat['exemplaires'] = $htmldom->find('.exemplaires',0)->outertext;
					if ($tmp = $htmldom->find('.autres_lectures',0)) {
						$tableau_resultat['autres_lecteurs'] = $tmp->next_sibling()->outertext;
					}				
					
					
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


function extraire_element_distant ($url_base, $file, $selector) {
			$resultat_recherche_locale = copie_locale($url_base.$file,'auto');
			if($resultat_recherche_locale != false) {
					$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
					
					$resultat_recherche_html = str_replace("page=", "pmb_page=", $resultat_recherche_html);
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


?>
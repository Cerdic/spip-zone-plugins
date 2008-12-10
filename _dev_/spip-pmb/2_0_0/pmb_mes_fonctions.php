<?php
include_spip('base/pmb_tables');

function pmb_transformer_nav_bar($nav_bar) {
	//si une seule page, on retourne vide
	if (strpos($nav_bar,"1/1")) return "";

	//sinon, on transforme les liens vers les images locales
	$nav_bar = str_replace("./images/first-grey.gif", find_in_path("img/pmb-first-grey.gif"), $nav_bar);
	$nav_bar = str_replace("./images/prev-grey.gif", find_in_path("img/pmb-prev-grey.gif"), $nav_bar);
	$nav_bar = str_replace("./images/first.gif", find_in_path("img/pmb-first.gif"), $nav_bar);
	$nav_bar = str_replace("./images/prev.gif", find_in_path("img/pmb-prev.gif"), $nav_bar);
	$nav_bar = str_replace("./images/next-grey.gif", find_in_path("img/pmb-next-grey.gif"), $nav_bar);
	$nav_bar = str_replace("./images/last-grey.gif", find_in_path("img/pmb-last-grey.gif"), $nav_bar);
	$nav_bar = str_replace("./images/next.gif", find_in_path("img/pmb-next.gif"), $nav_bar);
	$nav_bar = str_replace("./images/last.gif", find_in_path("img/pmb-last.gif"), $nav_bar);
	return $nav_bar;
}


function pmb_charger_page ($url_base, $file, $mode='auto') {
	$resultat_recherche_locale = copie_locale($url_base.$file,$mode);
	if($resultat_recherche_locale != false) {
		$resultat_recherche_html = unicode2charset(charset2unicode(file_get_contents($resultat_recherche_locale), 'iso-8859-1'),'utf-8');
		
		$resultat_recherche_html = str_replace("page=", "pmb_page=", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("addtags.php", $url_base."addtags.php", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("avis.php", $url_base."avis.php", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("./do_resa.php", $url_base."do_resa.php", $resultat_recherche_html);
		$resultat_recherche_html = str_replace("index.php?lvl=", "index.php?page=", $resultat_recherche_html);
		require(find_in_path('simple_html_dom.php'));
		$htmldom = str_get_html($resultat_recherche_html);	

	}
	return $htmldom;		
					

}

function pmb_accueil_extraire($url_base, $mode='auto') {
	$tableau_resultat = Array();
	
	if ($htmldom = pmb_charger_page($url_base, "index.php",$mode)) {
			$resultats_recherche = $htmldom->find('#location-container td');
			$i=0;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = $res->find('a',1)->outertext;				
				
				$i++;
			}	
	}
	return $tableau_resultat;

}

function pmb_section_extraire($id_section, $id, $url_base, $pmb_page=1, $mode='auto') {
	$tableau_resultat = Array();
	
	if ($htmldom = pmb_charger_page($url_base, "index.php?lvl=section_see&page=".$pmb_page."&location=".$id_section."&id=".$id,$mode)) {
			$tableau_resultat[0] = Array();
			$tableau_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
			$tableau_resultat[0]['nav_bar'] = pmb_transformer_nav_bar($tableau_resultat[0]['nav_bar']);

			$tableau_resultat[0]['titre_section'] = $htmldom->find('#aut_details h3',0)->innertext;
			
			$resultats_recherche = $htmldom->find('#aut_details_container table td');
			$i = 1;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = $res->find('a', 1)->outertext;
				$i++;
			}	
	}
	return $tableau_resultat;

}

function pmb_serie_extraire($id_serie, $url_base, $pmb_page=1, $mode='auto') {
	$tableau_resultat = Array();
	
	if ($htmldom = pmb_charger_page($url_base, "index.php?lvl=serie_see&page=".$pmb_page."&id=".$id_serie,$mode)) {
			$tableau_resultat[0] = Array();
			$tableau_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
			$tableau_resultat[0]['nav_bar'] = pmb_transformer_nav_bar($tableau_resultat[0]['nav_bar']);
			$tableau_resultat[0]['titre_serie'] = $htmldom->find('#aut_see h3',0)->innertext;
			
			$resultats_recherche = $htmldom->find('.child');
			$tableau_resultat[0]['nb_resultats'] = count($resultats_recherche);
			$i = 1;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = Array();				
				pmb_parser_notice($res, $tableau_resultat[$i]);
				$i++;
			}	
	}
	return $tableau_resultat;

}

function pmb_collection_extraire($id_collection, $url_base, $pmb_page=1, $mode='auto') {
	$tableau_resultat = Array();
	
	if ($htmldom = pmb_charger_page($url_base, "index.php?lvl=coll_see&page=".$pmb_page."&id=".$id_collection,$mode)) {
			$tableau_resultat[0] = Array();
			$tableau_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
			$tableau_resultat[0]['nav_bar'] = pmb_transformer_nav_bar($tableau_resultat[0]['nav_bar']);
			$tableau_resultat[0]['titre_collection'] = $htmldom->find('#aut_see h3',0)->innertext;
			$tableau_resultat[0]['collections_infos'] = $htmldom->find('#aut_see ul',0)->outertext;
			
			$resultats_recherche = $htmldom->find('.child');
			$tableau_resultat[0]['nb_resultats'] = count($resultats_recherche);
			$i = 1;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = Array();				
				pmb_parser_notice($res, $tableau_resultat[$i]);
				$i++;
			}	
	}
	return $tableau_resultat;

}

function pmb_editeur_extraire($id_editeur, $url_base, $pmb_page=1, $mode='auto') {
	$tableau_resultat = Array();
	
	if ($htmldom = pmb_charger_page($url_base, "index.php?lvl=publisher_see&page=".$pmb_page."&id=".$id_editeur,$mode)) {
			$tableau_resultat[0] = Array();
			$tableau_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
			$tableau_resultat[0]['nav_bar'] = pmb_transformer_nav_bar($tableau_resultat[0]['nav_bar']);
			$tableau_resultat[0]['titre_editeur'] = $htmldom->find('#aut_see h3',0)->innertext;
			$tableau_resultat[0]['collections_editeur'] = $htmldom->find('#aut_see ul',0)->outertext;
			$infos_editeur = $htmldom->find('#aut_see p');
			$tableau_resultat[0]['infos_editeur'] = '';
			foreach($infos_editeur as $p_editeur) {
				$tableau_resultat[0]['infos_editeur'] .= $p_editeur->outertext;
			}
			
			$resultats_recherche = $htmldom->find('.child');
			$tableau_resultat[0]['nb_resultats'] = count($resultats_recherche);
			$i = 1;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = Array();				
				pmb_parser_notice($res, $tableau_resultat[$i]);
				$i++;
			}	
	}
	return $tableau_resultat;

}

function pmb_auteur_extraire($id_auteur, $url_base, $pmb_page=1, $mode='auto') {
	$tableau_resultat = Array();
	
	if ($htmldom = pmb_charger_page($url_base, "index.php?lvl=author_see&page=".$pmb_page."&id=".$id_auteur,$mode)) {
			$tableau_resultat[0] = Array();
			$tableau_resultat[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
			$tableau_resultat[0]['nav_bar'] = pmb_transformer_nav_bar($tableau_resultat[0]['nav_bar']);
			$tableau_resultat[0]['titre_auteur'] = $htmldom->find('#aut_see h3',0)->innertext;
			
			$resultats_recherche = $htmldom->find('.child');
			$tableau_resultat[0]['nb_resultats'] = count($resultats_recherche);
			$i = 1;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = Array();				
				pmb_parser_notice($res, $tableau_resultat[$i]);
				$i++;
			}	
	}
	return $tableau_resultat;

}

function pmb_recherche_extraire($recherche, $url_base, $look_FIRSTACCESS='', $look_ALL='', $look_AUTHOR='', $look_PUBLISHER='', $look_COLLECTION='', $look_SUBCOLLECTION='', $look_CATEGORY='', $look_INDEXINT='', $look_KEYWORDS='', $look_TITLE='', $look_ABSTRACT='', $surligne='', $typdoc='', $ok='',$mode='auto') {
	$tableau_resultat = Array();
	$url_page = "index.php?lvl=search_result";
	if ($surligne) $url_page.="&surligne=".$surligne;
	if ($typdoc) $url_page.="&typdoc=".$typdoc;
	if ($ok) $url_page.="&ok=".$ok;
	if ($look_ALL) $url_page.="&look_ALL=".$look_ALL;
	if ($look_FIRSTACCESS) $url_page.="&look_FIRSTACCESS=".$look_FIRSTACCESS;
	if ($look_AUTHOR) $url_page.="&look_AUTHOR=".$look_AUTHOR;
	if ($look_PUBLISHER) $url_page.="&look_PUBLISHER=".$look_PUBLISHER;
	if ($look_COLLECTION) $url_page.="&look_COLLECTION=".$look_COLLECTION;
	if ($look_CATEGORY) $url_page.="&look_CATEGORY=".$look_CATEGORY;
	if ($look_INDEXINT) $url_page.="&look_INDEXINT=".$look_INDEXINT;
	if ($look_KEYWORDS) $url_page.="&look_KEYWORDS=".$look_KEYWORDS;
	if ($look_ABSTRACT) $url_page.="&look_ABSTRACT=".$look_ABSTRACT;
	if ($recherche) $url_page.="&user_query=".$recherche;


	if ($htmldom = pmb_charger_page($url_base, $url_page,$mode)) {
			$tableau_resultat[0] = Array();
			$tableau_resultatt[0]['nav_bar'] = $htmldom->find('.navbar',0)->outertext;
			$tableau_resultat[0]['nav_bar'] = pmb_transformer_nav_bar($tableau_resultat[0]['nav_bar']);

			$resultats_recherche = $htmldom->find('.child');
			$tableau_resultat[0]['nb_resultats'] = count($resultats_recherche);
			$i = 1;
			foreach($resultats_recherche as $res) {
				$tableau_resultat[$i] = Array();				
				pmb_parser_notice($res, $tableau_resultat[$i]);
				$i++;
			}	
	}
	return $tableau_resultat;
}

//d�coupage d'une notice au d�part dans un div de class .child
function pmb_parser_notice ($localdom, &$tresultat) {
	$id_notice = intval(substr($localdom->id,2));
	$tresultat['id'] = $id_notice;	
	$tresultat['logo_src'] = $localdom->find('table img',0)->src; 
	$tresultat['exemplaires'] = $localdom->find('.exemplaires',0)->outertext;
	if ($tmp = $localdom->find('.autres_lectures',0)) {
			$tresultat['autres_lecteurs'] = $tmp->next_sibling()->outertext;
	}				
	$tablechamps = $localdom->find('table table tr');
	foreach($tablechamps as $tr) {
		$libelle = htmlentities($tr->find('td',0)->innertext);
		$valeur = $tr->find('td',1)->innertext;
		if (strpos($libelle, 'Titre de s')) $tresultat['serie'] = $valeur; 
		if (strpos($libelle, 'Titre')) $tresultat['titre'] = $valeur; 
		else if (strpos($libelle, 'Type de document')) $tresultat['type'] = $valeur; 
		else if (strpos($libelle, 'Editeur')) $tresultat['editeur'] = $valeur; 
		else if (strpos($libelle, 'Auteurs')) $tresultat['lesauteurs'] = $valeur; 
		else if (strpos($libelle, 'de publication')) $tresultat['annee_publication'] = $valeur; 
		else if (strpos($libelle, 'Collection')) $tresultat['collection'] = $valeur; 
		else if (strpos($libelle, 'Importance')) $tresultat['importance'] = $valeur; 
		else if (strpos($libelle, 'Pr�sentation')) $tresultat['presentation'] = $valeur; 
		else if (strpos($libelle, 'Format')) $tresultat['format'] = $valeur; 
		else if (strpos($libelle, 'Importance')) $tresultat['importance'] = $valeur; 
		else if (strpos($libelle, 'ISBN')) $tresultat['isbn'] = $valeur; 
		else if (strpos($libelle, 'Prix')) $tresultat['prix'] = $valeur; 
		else if (strpos($libelle, 'Langues')) $tresultat['langues'] = $valeur; 
		else if (strpos($libelle, 'sum')) $tresultat['resume'] = $valeur; 
	}

}

// retourne un tableau associatif contenant tous les champs d'une notice 
function pmb_notice_extraire ($id_notice, $url_base, $mode='auto') {
	$tableau_resultat = Array();
	if ($htmldom = pmb_charger_page($url_base, "index.php?lvl=notice_display&seule=1&id=".$id_notice, $mode)) {
		 pmb_parser_notice($htmldom->find('.child',0), $tableau_resultat);	
	}
	return $tableau_resultat;
			
}


function pmb_notice_champ ($tableau_resultat, $champ) {
	return $tableau_resultat[$champ];
}
function pmb_tableau2_valeur ($tableau_resultat, $indice1, $indice2) {
	return $tableau_resultat[$indice1][$indice2];
}
/*mettre le champ de recherche au format de pmb */
function pmb_prepare_recherche ($recherche) {
	$recherche = str_replace("+"," ",$recherche);
	$recherche = str_replace(" ","%20",$recherche);
	//$recherche = htmlentities($recherche);
	//$recherche = unicode2charset(charset2unicode($recherche, 'utf-8'),'iso-8859-1');
	return $recherche;
}

/* fonction str_replace avec l'ordre des parametres compatible spip */
function pmb_remplacer ($chaine, $p1, $p2) {
	return str_replace($p1,$p2,$chaine);
}
?>
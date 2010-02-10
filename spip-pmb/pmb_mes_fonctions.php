<?php
/*************************************************************************************/
/*                                                                                   */
/*      Portail web pour PMB	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                     */
/*	email : info@openstudio.fr		        	                             	 */
/*      web : http://www.openstudio.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/

include_spip('base/pmb_tables');



function pmb_section_extraire($id_section, $url_base='') {
	$tableau_sections = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	//récupérer les infos sur la section parent
	$section_parent = $ws->pmbesOPACGeneric_get_section_information($id_section);
	$tableau_sections[0] = Array();
	$tableau_sections[0]['section_id'] = $section_parent->section_id;
	$tableau_sections[0]['section_location'] = $section_parent->section_location;
	$tableau_sections[0]['section_caption'] = $section_parent->section_caption;
	$tableau_sections[0]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section_parent->section_image;

	$tab_sections = $ws->pmbesOPACGeneric_list_sections($id_section);
	$cpt = 1;
	if (is_array($tab_sections)) {
		    foreach ($tab_sections as $section) {
			  $tableau_sections[$cpt] = Array();
			  $tableau_sections[$cpt]['section_id'] = $section->section_id;
			  $tableau_sections[$cpt]['section_location'] = $section->section_location;
			  $tableau_sections[$cpt]['section_caption'] = $section->section_caption;
			  $tableau_sections[$cpt]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section->section_image;

			  
			  $cpt++;
		    }
	}
	
	return $tableau_sections;
}
function pmb_location_extraire($id_location, $url_base='') {
	$tableau_locationsections = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	$tab_locations = $ws->pmbesOPACGeneric_get_location_information_and_sections($id_location);
	//récupérer les infos sur la localisation parent
	$tableau_locationsections[0] = Array();
	$tableau_locationsections[0]['location_id'] = $tab_locations['location']->location_id;
	$tableau_locationsections[0]['location_caption'] = $tab_locations['location']->location_caption;

	$cpt = 1;
	if (is_array($tab_locations['sections'])) {
		foreach ($tab_locations['sections'] as $section) {
		      $tableau_locationsections[$cpt] = Array();
		      $tableau_locationsections[$cpt]['section_id'] = $section->section_id;
		      $tableau_locationsections[$cpt]['section_location'] = $section->section_location;
		      $tableau_locationsections[$cpt]['section_caption'] = $section->section_caption;
		      $tableau_locationsections[$cpt]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section->section_image;

		      
		      $cpt++;
		}
	}
	return $tableau_locationsections;
}
function pmb_liste_afficher_locations($url_base) {
	$tableau_sections = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	$tab_locations = $ws->pmbesOPACGeneric_list_locations();
	$cpt = 0;
	if (is_array($tab_locations)) {
		foreach ($tab_locations as $location) {
		      $tableau_locations[$cpt] = Array();
		      $tableau_locations[$cpt]['location_id'] = $location->location_id;
		      $tableau_locations[$cpt]['location_caption'] = $location->location_caption;
		      $cpt++;
		}
	}
	return $tableau_locations;
}

function pmb_notices_section_extraire($id_section, $url_base, $debut=0, $fin=5) {
	$tableau_resultat = Array();
	
	$search = array();
	$search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);
			
	pmb_ws_charger_wsdl($ws, $url_base);
	try {	
			$tableau_resultat[0] = Array();
					
			$r=$ws->pmbesOPACAnonymous_advancedSearch($search);
			
			$searchId=$r["searchId"];
			$tableau_resultat[0][' '] = $r["nbResults"];
	    
			 //$r=$ws->pmbesOPACAnonymous_fetchSearchRecords($searchId,$debut,$fin,"serialized_unimarc","utf8");
			 $r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$fin,"utf8");
			  $i = 1;
			  if (is_array($r)) {
			      foreach($r as $value) {
					$tableau_resultat[$i] = Array();				
				    
					//pmb_ws_parser_notice_serialisee($value['noticeId'], $value['noticeContent'], $tableau_resultat[$i]);
					pmb_ws_parser_notice_array($value, $tableau_resultat[$i]);
					$i++;
			      }
			  }
		

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 

	return $tableau_resultat;
}



function pmb_collection_extraire($id_collection, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	pmb_ws_charger_wsdl($ws, $url_base);
	try {
	      $result = $ws->pmbesCollections_get_collection_information_and_notices($id_collection,$id_session);
	      if ($result) {
		  $tableau_resultat['collection_id'] = $result['information']->collection_id;
		  $tableau_resultat['collection_name'] = $result['information']->collection_name;
		  $tableau_resultat['collection_parent'] = $result['information']->collection_parent;
		  $tableau_resultat['collection_issn'] = $result['information']->collection_issn;
		  $tableau_resultat['collection_web'] = $result['information']->collection_web;
		   $tableau_resultat['notice_ids'] = Array();

		$liste_notices = Array();
		  $cpt=0;
		  if (is_array($result['notice_ids'])) {
			      foreach($result['notice_ids'] as $cle=>$valeur) {
				if (($cpt>=$debut) && ($cpt<$nbresult+$debut)) $liste_notices[] = $valeur;
				$cpt++;
			      }
		  }
		  pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);
		  $tableau_resultat['notice_ids'][0]['nb_resultats'] = $cpt;

		  $cpt=0;
		  if (is_array($liste_notices)) {
			foreach($liste_notices as $notice) {
			    $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
			    $cpt++;
			  }
		  }
		}
	      

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tableau_resultat;
}

function pmb_editeur_extraire($id_editeur, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	pmb_ws_charger_wsdl($ws, $url_base);
	try {
	      $result = $ws->pmbesPublishers_get_publisher_information_and_notices($id_editeur,$id_session);
	      if ($result) {
		  $tableau_resultat['publisher_id'] = $result['information']->publisher_id;
		  $tableau_resultat['publisher_name'] = $result['information']->publisher_name;
		  $tableau_resultat['publisher_address1'] = $result['information']->publisher_address1;
		  $tableau_resultat['publisher_address2'] = $result['information']->publisher_address2;
		  $tableau_resultat['publisher_zipcode'] = $result['information']->publisher_zipcode;
		  $tableau_resultat['publisher_city'] = $result['information']->publisher_city;
		  $tableau_resultat['publisher_country'] = $result['information']->publisher_country;
		  $tableau_resultat['publisher_web'] = $result['information']->publisher_web;
		  $tableau_resultat['publisher_comment'] = $result['information']->publisher_comment;
		   $tableau_resultat['notice_ids'] = Array();

		  $liste_notices = Array();
		  $cpt=0;
		  if (is_array($result['notice_ids'])) {
			foreach($result['notice_ids'] as $cle=>$valeur) {
			  if (($cpt>=$debut) && ($cpt<$nbresult+$debut)) $liste_notices[] = $valeur;
			  $cpt++;
			}
		  }
		  pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);
		  $tableau_resultat['notice_ids'][0]['nb_resultats'] = $cpt;

		  $cpt=0;
		  if (is_array($liste_notices)) {
			foreach($liste_notices as $notice) {
			  $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
			  $cpt++;
			}
		  }
		}
	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tableau_resultat;

}

function pmb_auteur_extraire($id_auteur, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	pmb_ws_charger_wsdl($ws, $url_base);
	try {
	      $result = $ws->pmbesAuthors_get_author_information_and_notices($id_auteur,$id_session);
	      if ($result) {
		  $tableau_resultat['author_id'] = $result['information']->author_id;
		  $tableau_resultat['author_type'] = $result['information']->author_type;
		  $tableau_resultat['author_name'] = $result['information']->author_name;
		  $tableau_resultat['author_rejete'] = $result['information']->author_rejete;
		  if ($result['information']->author_rejete) {
		      $tableau_resultat['author_nomcomplet'] =  $tableau_resultat['author_rejete'].' '.$tableau_resultat['author_name'];
		  } else {
		      $tableau_resultat['author_nomcomplet'] = $tableau_resultat['author_name'];
		  }

		  $tableau_resultat['author_see'] = $result['information']->author_see;
		  $tableau_resultat['author_date'] = $result['information']->author_date;
		  $tableau_resultat['author_web'] = $result['information']->author_web;
		  $tableau_resultat['author_comment'] = $result['information']->author_comment;
		  $tableau_resultat['author_lieu'] = $result['information']->author_lieu;
		  $tableau_resultat['author_ville'] = $result['information']->author_ville;
		  $tableau_resultat['author_pays'] = $result['information']->author_pays;
		  $tableau_resultat['author_subdivision'] = $result['information']->author_subdivision;
		  $tableau_resultat['author_numero'] = $result['information']->author_numero;
		  $tableau_resultat['notice_ids'] = Array();

		  $liste_notices = Array();
		  $cpt=0;
		  if (is_array($result['notice_ids'])) {
			foreach($result['notice_ids'] as $cle=>$valeur) {
			  if (($cpt>=$debut) && ($cpt<$nbresult+$debut)) $liste_notices[] = $valeur;
			  $cpt++;
			}
		  }
		  pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);
		   $tableau_resultat['notice_ids'][0]['nb_resultats'] = $cpt;
		  $cpt=0;
		  if (is_array($liste_notices)) {
			foreach($liste_notices as $notice) {
			  $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
			  $cpt++;
			}
		   }
		}
	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tableau_resultat;

}

function pmb_recherche_extraire($recherche='', $url_base, $look_ALL='', $look_AUTHOR='', $look_PUBLISHER='', $look_COLLECTION='', $look_SUBCOLLECTION='', $look_CATEGORY='', $look_INDEXINT='', $look_KEYWORDS='', $look_TITLE='', $look_ABSTRACT='', $id_section='', $debut=0, $fin=5, $typdoc='',$id_location='') {
	$tableau_resultat = Array();
	//$recherche = strtolower($recherche);
	$search = array();
	$searchType = 0;	

	if ($recherche=='*') $recherche='';
	
	if ($look_ALL) {
		  if ($recherche) $search[] = array("inter"=>"or","field"=>42,"operator"=>"BOOLEAN", "value"=>$recherche);	
		  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
		  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);								
		  if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
	} else {
		if ($look_TITLE) {
			  $searchType = 1;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>1,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_AUTHOR) {
			  $searchType = 2;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>2,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
	    
		if ($look_PUBLISHER) {
			  $searchType = 3;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>3,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_COLLECTION) {
			  $searchType = 4;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>4,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_ABSTRACT) {
			  if ($recherche) $search[] = array("inter"=>"or","field"=>10,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"AND","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
	  
		if ($look_CATEGORY) {
			  $searchType = 6;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>11,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_INDEXINT) {
			  if ($recherche) $search[] = array("inter"=>"or","field"=>12,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_KEYWORDS) {
			  if ($recherche) $search[] = array("inter"=>"","field"=>13,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
		if ((!$look_TITLE) && (!$look_AUTHOR) && (!$look_PUBLISHER) && (!$look_COLLECTION) && (!$look_ABSTRACT) && (!$look_CATEGORY) && (!$look_INDEXINT) && (!$look_KEYWORDS)) {
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
	}
	
		 
	//récupérer le résultat d'une recherchevia les webservices
	
	global $gtresultat;
	$gtresultat = array();
	
	
	
	pmb_ws_charger_wsdl($ws, $url_base);
	try {	
			$tableau_resultat[0] = Array();
					
			//cas d'une recherche simple 
			if (($look_ALL)&&(!$id_section)&&(!$typdoc)){
			  $r=$ws->pmbesOPACAnonymous_simpleSearch($searchType,$recherche);
			} else {
			  $r=$ws->pmbesOPACAnonymous_advancedSearch($search);
			}
			$searchId=$r["searchId"];
			$tableau_resultat[0]['nb_resultats'] = $r["nbResults"];
	    
			//R�cup�ration des 10 premiers r�sultats
			/*Les formats peuvent-�tre :
				pmb_xml_unimarc pour du xml.
				json_unimarc pour du javascript.
				serialized_unimarc pour du php.
				header, isbd, isbd_suite pour du texte.
				dc, oai_dc pour du dublin core.
				convert:truc pour un passage pas admin/convert dans le format truc.
				autre: renvoi l'id de la notice.
			*/ 
			  //$r=$ws->pmbesOPACAnonymous_fetchSearchRecords($searchId,$debut,$fin,"serialized_unimarc","utf8");
			  $r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$fin,"utf8");
			  $i = 1;
			  if (is_array($r)) {
			      foreach($r as $value) {
				    $tableau_resultat[$i] = Array();				
				
				    //pmb_ws_parser_notice_serialisee($value['noticeId'], $value['noticeContent'], $tableau_resultat[$i]);
				    pmb_ws_parser_notice_array($value, $tableau_resultat[$i]);
				    $i++;
			      }
			  }
		

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 

	return $tableau_resultat;
}



    // Traitement des balises ouvrantes
    function fonctionBaliseOuvrante($parseur, $nomBalise, $tableauAttributs)
    {
        // En fait... nous nous conteterons de mémoriser le nom de la balise
        // afin d'en tenir compte dans la fonction "fonctionTexte"

        global $derniereBaliseRencontree;
         global $dernierAttributRencontre;
       global $dernierTypeTrouve;
       global $dernierIdTrouve;

        $derniereBaliseRencontree = $nomBalise;
  
        $dernierAttributRencontre = $tableauAttributs;
	
    }
   
    // Rraitement des balises fermantes
    function fonctionBaliseFermante($parseur, $nomBalise)
    {
        // On oublie la dernière balise rencontrée
        global $derniereBaliseRencontree;
         global $dernierAttributRencontre;
       global $dernierTypeTrouve;
       global $dernierIdTrouve;

        $derniereBaliseRencontree = "";
    }

    // Traitement du texte
    // qui est appelé par le "parseur"
    function fonctionTexte($parseur, $texte)
    {
        global $derniereBaliseRencontree;
         global $dernierAttributRencontre;
       global $dernierTypeTrouve;
       global $dernierIdTrouve;
    global $gtresultat;

        // Selon les cas, nous affichons le texte
        // ou nous proposons un lien
        // ATTENTION: Par défaut les noms des balises sont
        //            mises en majuscules
       //echo("<br />fonctionTexte=".$derniereBaliseRencontree);
        switch ($derniereBaliseRencontree) {
            case "F": 
		   foreach($dernierAttributRencontre as $cle=>$attr) {
			if ($cle=="C") $dernierTypeTrouve = $attr;
			if ($cle=="ID") $dernierIdTrouve = $attr;
		  }
              break;

            case "S":
               foreach($dernierAttributRencontre as $cle=>$attr) {
			if ($cle=="C") $dernierSousTypeTrouve = $attr;
		}

		if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "a")) $gtresultat['isbn'] .= $texte;
		if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "b")) $gtresultat['reliure'] .= $texte;
		if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "d")) $gtresultat['prix'] .= $texte;
		
		if (($dernierTypeTrouve == "101") && ($dernierSousTypeTrouve == "a")) $gtresultat['langues'] .= $texte;
		
		if (($dernierTypeTrouve == "102") && ($dernierSousTypeTrouve == "a")) $gtresultat['pays'] .= $texte;
		
		if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "a")) $gtresultat['titre'] .= $texte;
		if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "f")) $gtresultat['auteur'] .= $texte;
		
		if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "c")) $gtresultat['editeur'] .= $texte;
		if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "a")) $gtresultat['editeur'] .= ' ('.$texte.')';
		if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "d")) $gtresultat['annee_publication'] .= $texte;
		if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "c")) $gtresultat['id_editeur'] = $dernierIdTrouve;
		
		if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "a")) $gtresultat['importance'] .= $texte;
		if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "c")) $gtresultat['presentation'] .= $texte;
		if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "d")) $gtresultat['format'] .= $texte;
		
		if (($dernierTypeTrouve == "225") && ($dernierSousTypeTrouve == "a")) $gtresultat['collection'] .= $texte;
		if (($dernierTypeTrouve == "225") && ($dernierSousTypeTrouve == "a")) $gtresultat['id_collection'] = $dernierIdTrouve;
		
		if (($dernierTypeTrouve == "330") && ($dernierSousTypeTrouve == "a")) $gtresultat['resume'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", str_replace("\n","<br />", $texte))));
		
		if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "a")) $gtresultat['lesauteurs'] .= $texte;
		if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "b")) $gtresultat['lesauteurs'] = $texte." ".$gtresultat['lesauteurs'];
		if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "a")) $gtresultat['id_auteur'] = $dernierIdTrouve;
		
		

		
                break;
        }         
    }

//parsing xml d'une notice
function pmb_ws_parser_notice_xml($id_notice, $value, &$tresultat) {

	    include_spip("/inc/filtres_images");
	    global $gtresultat;
	    global $indice_exemplaire;
	   $gtresultat = array();
	
	    // Création du parseur XML
	    $parseurXML = xml_parser_create();

	    // Je précise le nom des fonctions à appeler
	    // lorsque des balises ouvrantes ou fermantes sont rencontrées
	    xml_set_element_handler($parseurXML, "fonctionBaliseOuvrante"
					      , "fonctionBaliseFermante");

	    // Je précise le nom de la fonction à appeler
	    // lorsque du texte est rencontré
	    xml_set_character_data_handler($parseurXML, "fonctionTexte");

	   $gtresultat['tab_exemplaires'] = Array();
	  
	   $gtresultat['exemplaires'] = "<table cellpadding='2' class='exemplaires' width='100%'>
		    <tr><th class='expl_header_expl_cb'>Code barre</th><th class='expl_header_expl_cote'>Cote</th><th class='expl_header_location_libelle'>Localisation</th><th class='expl_header_tdoc_libelle'>Support</th><th class='expl_header_section_libelle'>Section</th><th>Disponibilité</th></tr>";

	    // Ouverture du fichier
	    xml_parse($parseurXML, $value, true);
	  
	    $gtresultat['exemplaires'] .= "</table>";
	    // echo("<br/><br />version brute : <br/><br />".$value);
	    xml_parser_free($parseurXML);

	    if ($gtresultat['lesauteurs'] == "")
		  $gtresultat['lesauteurs'] = $gtresultat['auteur'];
	     $gtresultat['logo_src'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac")."/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F!!isbn!!.08.MZZZZZZZ.jpg&noticecode=".str_replace("-","",$gtresultat['isbn']);


	    //cas où il n'y a pas d'image pmb renvoie un carré de 1 par 1 transparent.
	    $tmp_img = image_reduire("<img src=\"".copie_locale($gtresultat['logo_src'])."\" />", 130, 0);
	    if (strpos($tmp_img, "L1xH1") !== false)  $gtresultat['logo_src'] = "";
	    
	    $gtresultat['id'] = $id_notice;
	    

	    $tresultat = $gtresultat;
}

function pmb_recuperer_champs_recherche($langue=0) {
	$tresultat = Array();
	
	pmb_ws_charger_wsdl($ws, $url_base);
	try {
	     $result = $ws->pmbesSearch_getAdvancedSearchFields('opac|search_fields',$langue,true);
	     $cpt=0;
	     if (is_array($result)) {
			      foreach ($result as $res) {
					    $tresultat[$cpt] = Array();
					    $tresultat[$cpt]['id'] = $res->id;
					    $tresultat[$cpt]['label'] = $res->label;
					    $tresultat[$cpt]['type'] = $res->type;
					    $tresultat[$cpt]['operators'] = $res->operators;
					    $tresultat[$cpt]['values'] = Array();
					    $cpt2=0;
					    if (is_array($res->values)) {
						    foreach ($res->values as $value) {
							$tresultat[$cpt]['values'][$cpt2]['value_id'] = $value->value_id;
							$tresultat[$cpt]['values'][$cpt2]['value_caption'] = $value->value_caption;
							$cpt2++;
						    }
					    }
					    $cpt++;
				}
	      }
	    

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tresultat;
}
//parsing d'une notice sérialisée
function pmb_ws_parser_notice_serialisee($id_notice, $value, &$tresultat) {
	    include_spip("/inc/filtres_images");
	    $indice_exemplaire = 0;
	    $tresultat = Array();
	
	    $noticecontent = Array();
	    $unserialized = $value; 
	    $unserialized = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $unserialized );
	    
	    $noticecontent = unserialize($unserialized);
	    foreach ( $noticecontent as $c1=>$v1) {
	      //echo("<br />C1 -> ".$c1."=".$v1);
	      foreach ( $v1 as $c2=>$v2) {
		    //echo("<br />C2 -> ".$c2."=".$v2);
		    foreach ( $v2 as $c3=>$v3) {
			   if ($c3=="c") $dernierTypeTrouve = $v3;
			   if ($c3=="id") $dernierIdTrouve = $v3;
			    foreach ( $v3 as $c4=>$v4) {
				//echo("<br />attr=".$dernierTypeTrouve.",".$v4['c'].",".$v4['value']);
				$dernierSousTypeTrouve = $v4['c'];
				$texte = $v4['value'];
				if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "a")) $tresultat['isbn'] .= $texte;
				if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "b")) $tresultat['reliure'] .= $texte;
				if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "d")) $tresultat['prix'] .= $texte;
				
				if (($dernierTypeTrouve == "101") && ($dernierSousTypeTrouve == "a")) $tresultat['langues'] .= $texte;
				
				if (($dernierTypeTrouve == "102") && ($dernierSousTypeTrouve == "a")) $tresultat['pays'] .= $texte;
				
				if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "a")) $tresultat['titre'] .= $texte;
				if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "f")) $tresultat['auteur'] .= $texte;
				
				if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "c")) $tresultat['editeur'] .= $texte;
				if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "a")) $tresultat['editeur'] .= ' ('.$texte.')';
				if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "c")) $tresultat['id_editeur'] = $dernierIdTrouve;
				if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "d")) $tresultat['annee_publication'] .= $texte;
				
				if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "a")) $tresultat['importance'] .= $texte;
				if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "c")) $tresultat['presentation'] .= $texte;
				if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "d")) $tresultat['format'] .= $texte;
				
				if (($dernierTypeTrouve == "225") && ($dernierSousTypeTrouve == "a")) $tresultat['collection'] .= $texte;
				if (($dernierTypeTrouve == "225") && ($dernierSousTypeTrouve == "a")) $tresultat['id_collection'] = $dernierIdTrouve;
				
				if (($dernierTypeTrouve == "330") && ($dernierSousTypeTrouve == "a")) $tresultat['resume'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", $texte)))));
				
				if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "a")) $tresultat['lesauteurs'] .= $texte;
				if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "b")) $tresultat['lesauteurs'] = $texte." ".$tresultat['lesauteurs'];
				if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "a")) $tresultat['id_auteur'] = $dernierIdTrouve;
				
				
				
			    }
		    }
	      }
	    }

	    if ($tresultat['lesauteurs'] == "")
		  $tresultat['lesauteurs'] = $tresultat['auteur'];
	     $tresultat['logo_src'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac")."/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F!!isbn!!.08.MZZZZZZZ.jpg&noticecode=".str_replace("-","",$tresultat['isbn']);

	     //cas où il n'y a pas d'image pmb renvoie un carré de 1 par 1 transparent.
	    $tmp_img = image_reduire("<img src=\"".copie_locale($tresultat['logo_src'])."\" />", 130, 0);
	    if (strpos($tmp_img, "L1xH1") !== false)  $gtresultat['logo_src'] = "";

	    $tresultat['id'] = $id_notice;
}
//parsing d'une notice sérialisée
function pmb_ws_parser_notice_array($value, &$tresultat) {
	    include_spip("/inc/filtres_images");
	    
	    $indice_exemplaire = 0;
	    $tresultat = Array();
	    $id_notice = $value->id;
	    if (is_array($value->f)){
	      foreach ( $value->f as $c1=>$v1) {
		  if (is_array($v1->item)){
	      
		      foreach ( $v1->item as $c2=>$v2) {
			      if ($v2->key=="c") $dernierTypeTrouve = $v2->value;
			      if ($v2->key=="id") $dernierIdTrouve = $v2->value;
			      if (is_array($v2->value)){
				  foreach ( $v2->value as $c4=>$v4) {
							    $dernierSousTypeTrouve=$v4['c'];
							    $texte = $v4['value'];
							    if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "a")) $tresultat['isbn'] .= $texte;
							    if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "b")) $tresultat['reliure'] .= $texte;
							    if (($dernierTypeTrouve == "010") && ($dernierSousTypeTrouve == "d")) $tresultat['prix'] .= $texte;
							    
							    if (($dernierTypeTrouve == "101") && ($dernierSousTypeTrouve == "a")) $tresultat['langues'] .= $texte;
							    
							    if (($dernierTypeTrouve == "102") && ($dernierSousTypeTrouve == "a")) $tresultat['pays'] .= $texte;
							    
							    if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "a")) $tresultat['titre'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$texte))))));
							    if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "e")) $tresultat['soustitre'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$texte))))));

							    if (($dernierTypeTrouve == "200") && ($dernierSousTypeTrouve == "f")) $tresultat['auteur'] .= $texte;
							    
							    if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "c")) $tresultat['editeur'] .= $texte;
							    if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "a")) $tresultat['editeur'] .= ' ('.$texte.')';
							    if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "a")) $tresultat['id_editeur'] = $dernierIdTrouve;
							    if (($dernierTypeTrouve == "210") && ($dernierSousTypeTrouve == "d")) $tresultat['annee_publication'] .= $texte;
							    
							    if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "a")) $tresultat['importance'] .= $texte;
							    if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "c")) $tresultat['presentation'] .= $texte;
							    if (($dernierTypeTrouve == "215") && ($dernierSousTypeTrouve == "d")) $tresultat['format'] .= $texte;
							    
							    if (($dernierTypeTrouve == "225") && ($dernierSousTypeTrouve == "a")) $tresultat['collection'] .= $texte;
							    if (($dernierTypeTrouve == "225") && ($dernierSousTypeTrouve == "a")) $tresultat['id_collection'] = $dernierIdTrouve;
							    
							    if (($dernierTypeTrouve == "330") && ($dernierSousTypeTrouve == "a")) $tresultat['resume'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$texte))))));
							    
							    if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "a")) {
										$tresultat['id_auteur'] = $dernierIdTrouve;
										if ($avantDernierTypeTrouve == $dernierTypeTrouve){
										      $tresultat['liensauteurs'].="</a>, ";
										}
										$tresultat['liensauteurs'].="<a href=\"?page=author_see&amp;id=".$dernierIdTrouve."\">".$texte;
										$tresultat['lesauteurs'] .= $texte;
										$avantDernierTypeTrouve = $dernierTypeTrouve;
										
							    }
							    if (($dernierTypeTrouve == "700") && ($dernierSousTypeTrouve == "b")) {
										$tresultat['lesauteurs'] = $texte." ".$tresultat['lesauteurs'];
										$tresultat['liensauteurs'] .= " ".$texte;
							    }
							    if (($dernierTypeTrouve == "701") && ($dernierSousTypeTrouve == "a")) {
										$tresultat['id_auteur2'] = $dernierIdTrouve;
										if ($avantDernierTypeTrouve == $dernierTypeTrouve){
										      $tresultat['liensauteurs2'].="</a>, ";
										}
										$tresultat['liensauteurs2'].="<a href=\"?page=author_see&amp;id=".$dernierIdTrouve."\">".$texte;
										$tresultat['lesauteurs2'] .= $texte;
										$avantDernierTypeTrouve = $dernierTypeTrouve;
										
							    }
							    if (($dernierTypeTrouve == "701") && ($dernierSousTypeTrouve == "b")) {
										$tresultat['lesauteurs2'] = $texte." ".$tresultat['lesauteurs2'];
										$tresultat['liensauteurs2'] .= " ".$texte;
							    }
							    if (($dernierTypeTrouve == "702") && ($dernierSousTypeTrouve == "a")) {
										$tresultat['id_auteur3'] = $dernierIdTrouve;
										if ($avantDernierTypeTrouve == $dernierTypeTrouve){
										      $tresultat['liensauteurs3'].="</a>, ";
										}
										$tresultat['liensauteurs3'].="<a href=\"?page=author_see&amp;id=".$dernierIdTrouve."\">".$texte;
										$tresultat['lesauteurs3'] .= $texte;
										$avantDernierTypeTrouve = $dernierTypeTrouve;
										
							    }
							    if (($dernierTypeTrouve == "702") && ($dernierSousTypeTrouve == "b")) {
										$tresultat['lesauteurs3'] = $texte." ".$tresultat['lesauteurs3'];
										$tresultat['liensauteurs3'] .= " ".$texte;
							    }
							    
							    
				  }
			    }
		      }
		  }
	     }
	    }
	    
	    if ($tresultat['lesauteurs'] == "")
		  $tresultat['lesauteurs'] = $tresultat['auteur'];
	     $tresultat['logo_src'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac")."/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F!!isbn!!.08.MZZZZZZZ.jpg&noticecode=".str_replace("-","",$tresultat['isbn']);

	    //si pas de numéro isbn (exemple jouets ludothèque) il n'y aura pas de logo
	     if ($tresultat['isbn'] == '') $tresultat['logo_src'] = '';
	     //cas où il n'y a pas d'image pmb renvoie un carré de 1 par 1 transparent.
	   /* $tmp_img = image_reduire("<img src=\"".copie_locale($tresultat['logo_src'])."\" />", 130, 0);
	    if (strpos($tmp_img, "L1xH1") !== false)  $gtresultat['logo_src'] = "";
	    */
	    $tresultat['id'] = $id_notice;
	    

	  
}

function pmb_ws_autres_lecteurs($id_notice) {

	$tresultat = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	
	try {	
	     if ($ws->pmbesOPACGeneric_is_also_borrowed_enabled()) {
		$r=$ws->pmbesOPACGeneric_also_borrowed($id_notice,0);
		$listenotices = Array();
		if (is_array($r)) {
		    if (is_array($r)) {
			foreach ($r as $notice) {
			    $listenotices[] = $notice['notice_id'];
			}
		    }
		}
		if (count($listenotices)>0) {
		      pmb_ws_recuperer_tab_notices ($listenotices, $ws, $tresultat);
		}
	    }
	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tresultat;
}
function pmb_ws_documents_numeriques ($id_notice, $id_session=0) {

	$tresultat = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	
	try {	
		$r=$ws->pmbesNotices_listNoticeExplNums($id_notice, $id_session);
		$cpt = 0;
		if (is_array($r)) {
			foreach ($r as $docnum) {
			    $tresultat[$cpt] = Array();
			    $tresultat[$cpt]['name'] = str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$docnum->name))))));
			    $tresultat[$cpt]['mimetype'] = $docnum->mimetype;
			    $tresultat[$cpt]['url'] = $docnum->url;
			    $tresultat[$cpt]['downloadUrl'] = $docnum->downloadUrl;
			    
			    $cpt++;
		      }
		}

	} catch (SoapFault $fault) {
		print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tresultat;

}

function pmb_ws_dispo_exemplaire($id_notice, $id_session=0) {
  
	$tresultat = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	
	try {	
	     $r=$ws->pmbesItems_fetch_notice_items($id_notice, $id_session);
	      $cpt = 0;
	      if (is_array($r)) {
			foreach ($r as $exemplaire) {
			    $tresultat[$cpt] = Array();
			    $tresultat[$cpt]['id'] = $exemplaire->id;
			    $tresultat[$cpt]['cb'] = $exemplaire->cb;
			    $tresultat[$cpt]['cote'] = $exemplaire->cote;
			    $tresultat[$cpt]['location_id'] = $exemplaire->location_id;
			    $tresultat[$cpt]['location_caption'] = $exemplaire->location_caption;
			    $tresultat[$cpt]['section_id'] = $exemplaire->section_id;
			    $tresultat[$cpt]['section_caption'] = $exemplaire->section_caption;
			    $tresultat[$cpt]['statut'] = $exemplaire->statut;
			    $tresultat[$cpt]['support'] = $exemplaire->support;
			    $tresultat[$cpt]['situation'] = $exemplaire->situation;
			    
			    $cpt++;
		      }
		}
		

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tresultat;
}

//récuperer une notice en xml via les webservices
function pmb_ws_recuperer_notice ($id_notice, &$ws, &$tresultat) {
	
	try {	
	$listenotices = array(''.$id_notice);
	$tresultat['id'] = $id_notice;
		  //$r=$ws->pmbesNotices_fetchNoticeList($listenotices,"serialized_unimarc","utf8",true,false);
		  $r=$ws->pmbesNotices_fetchNoticeListArray($listenotices,"utf8",true,false);
		  if (is_array($r)) {
		      foreach($r as $value) {
			      //pmb_ws_parser_notice_serialisee($id_notice, $value, $tresultat);
			      pmb_ws_parser_notice_array($value, $tresultat);
			}
		  }
		

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 

	

}
//récuperer une notice en xml via les webservices
function pmb_ws_recuperer_tab_notices ($listenotices, &$ws, &$tresultat) {
	
	
	try {	
	
	$tresultat['id'] = $id_notice;
		  //$r=$ws->pmbesNotices_fetchNoticeList($listenotices,"serialized_unimarc","utf8",true,false);
		  $r=$ws->pmbesNotices_fetchNoticeListArray($listenotices,"utf8",true,false);
		  $cpt=0;
		  if (is_array($r)) {
		      foreach($r as $value) {
			    $tresultat[$cpt] = Array();
			    //pmb_ws_parser_notice_serialisee($id_notice, $value, $tresultat[$cpt]);
			    pmb_ws_parser_notice_array($value, $tresultat[$cpt]);
			    $cpt++;
			}
		  }
		

	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 

	

}

//charger les webservices
function pmb_ws_charger_wsdl(&$ws, $url_base) {
	try {
		$ws=new SoapClient(lire_config("spip_pmb/wsdl","http://tence.bibli.fr/pmbws/PMBWsSOAP_1?wsdl"));
	  } catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 

}
function pmb_ws_liste_tri_recherche() {
	//retourne un tableau contenant la liste des tris possibles
	/* Exemple de retour:
	  Array
	  (
	  [0] => Array
	  (
	  [sort_name] => text_1
	  [sort_caption] => Titre
	  )
	  
	  [1] => Array
	  (
	  [sort_name] => num_2
	  [sort_caption] => Indexation décimale
	  )
	  
	  [2] => Array
	  (
	  [sort_name] => text_3
	  [sort_caption] => Auteur
	  )
	...
      )*/
	$tresultat = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	
	try {	
	     $tresultat=$ws->pmbesSearch_get_sort_types();
	 
	} catch (SoapFault $fault) {
		print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tresultat;
}

// retourne un tableau associatif contenant tous les champs d'une notice 
function pmb_notice_extraire ($id_notice, $url_base, $mode='auto') {
	$tableau_resultat = Array();
	
	pmb_ws_charger_wsdl($ws, $url_base);
	pmb_ws_recuperer_notice($id_notice, $ws, $tableau_resultat);
	return $tableau_resultat;
			
}


// retourne un tableau associatif contenant tous les champs d'un tableau d'id de notices 
function pmb_tabnotices_extraire ($tabnotices, $url_base, $mode='auto') {
	$tableau_resultat = Array();
	$listenotices = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	if (is_array($tabnotices)) {
		foreach($tabnotices as $cle=>$valeur){
		    $listenotices[] = $valeur;
		}
	}
	
	pmb_ws_recuperer_tab_notices ($listenotices, $ws, $tableau_resultat);
	return $tableau_resultat;
			
}

// retourne un tableau associatif contenant les prêts en cours
function pmb_prets_extraire ($session_id, $url_base, $type_pret=0) {
	$tableau_resultat = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	try{
	      $loans = $ws->pmbesOPACEmpr_list_loans($session_id, $type_pret);
	      $liste_notices = Array();
	      $cpt = 0;
	      if (is_array($loans)) {
		foreach ($loans as $loan) {
			$tableau_resultat[$cpt] = Array();
			$tableau_resultat[$cpt]['empr_id'] = $loan->empr_id;
			$liste_notices[] = $loan->notice_id;
			$tableau_resultat[$cpt]['notice_id'] = $loan->notice_id;
			$tableau_resultat[$cpt]['bulletin_id'] = $loan->bulletin_id;
			$tableau_resultat[$cpt]['expl_id'] = $loan->expl_id;
			$tableau_resultat[$cpt]['expl_cb'] = $loan->expl_cb;
			$tableau_resultat[$cpt]['expl_support'] = $loan->expl_support;
			$tableau_resultat[$cpt]['expl_location_id'] = $loan->expl_location_id;
			$tableau_resultat[$cpt]['expl_location_caption'] = $loan->expl_location_caption;
			$tableau_resultat[$cpt]['expl_section_id'] = $loan->expl_section_id;
			$tableau_resultat[$cpt]['expl_section_caption'] = $loan->expl_section_caption;
			$tableau_resultat[$cpt]['expl_libelle'] = $loan->expl_libelle;
			$tableau_resultat[$cpt]['loan_startdate'] = $loan->loan_startdate;
			$tableau_resultat[$cpt]['loan_returndate'] = $loan->loan_returndate;
			
			$cpt++;
		  }
	      }
	      if ($cpt>0) {
		    $tableau_resultat['notice_ids'] = Array();
		    pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);  
	      }
	      $cpt=0;
	      if (is_array($liste_notices)) {
		foreach($liste_notices as $notice) {
		      $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
		      $cpt++;
		  }
	      }
	} catch (SoapFault $fault) {
		//print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
	} 
	return $tableau_resultat;
			
}

function pmb_reservations_extraire($pmb_session, $url_base) {
	$tableau_resultat = Array();
	pmb_ws_charger_wsdl($ws, $url_base);
	$reservations = $ws->pmbesOPACEmpr_list_resas($pmb_session);
	$liste_notices = Array();
	
	$cpt = 0;
	if (is_array($reservations)) {
		foreach ($reservations as $reservation) {
		      $tableau_resultat[$cpt] = Array();
		      $tableau_resultat[$cpt]['resa_id'] = $reservation->resa_id;
		      $tableau_resultat[$cpt]['empr_id'] = $reservation->empr_id;
		      $tableau_resultat[$cpt]['notice_id'] = $reservation->notice_id;
		      $liste_notices[] = $reservation->notice_id;
		      $tableau_resultat[$cpt]['bulletin_id'] = $reservation->bulletin_id;
		      $tableau_resultat[$cpt]['resa_rank'] = $reservation->resa_rank;
		      $tableau_resultat[$cpt]['resa_dateend'] = $reservation->resa_dateend;
		      $tableau_resultat[$cpt]['resa_retrait_location_id '] = $reservation->resa_retrait_location_id ;
		      $tableau_resultat[$cpt]['resa_retrait_location'] = $reservation->resa_retrait_location;
		  
		      $cpt++;
		}
	}
	if ($cpt>0) {
	      $tableau_resultat['notice_ids'] = Array();
	      pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);  
	}
	$cpt=0;
	if (is_array($liste_notices)) {
		foreach($liste_notices as $notice) {
		    $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
		    $cpt++;
		}
	}
	return $tableau_resultat;

}
function pmb_tester_session($pmb_session, $id_auteur, $url_base) {
	
	//tester si la session pmb est toujours active
	pmb_ws_charger_wsdl($ws, $url_base);
	

	try {
	      if ($ws->pmbesOPACEmpr_get_account_info($pmb_session)) {
	  	return 1;
	      } else {
		 $m = sql_updateq('spip_auteurs_pmb', array(
				      'pmb_session' => ''),
				      "id_auteur=".$id_auteur);
		return 0;
	      }

	} catch (SoapFault $fault) {
		$m = sql_updateq('spip_auteurs_pmb', array(
				      'pmb_session' => ''),
				      "id_auteur=".$id_auteur);
		return 0;
	}
}
function pmb_reserver_ouvrage($session_id, $notice_id, $bulletin_id, $location, $url_base) {
	pmb_ws_charger_wsdl($ws, $url_base);
	return $ws->pmbesOPACEmpr_add_resa($session_id, $notice_id, $bulletin_id, $location);
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
	return $recherche;
}

/* fonction str_replace avec l'ordre des parametres compatible spip */
function pmb_remplacer ($chaine, $p1, $p2) {
	return str_replace($p1,$p2,$chaine);
}
function contient($texte, $findme) {
	return (strpos($texte, $findme) !== false);
}
function extraire_attribut_url($url,$attribut) {
		if ($url) {
		  preg_match('`'.$attribut.'=[0-9]+$`',$url, $result);		
		  return(substr($result[0], 3));
		}
		return '';
}

?>
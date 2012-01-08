<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/*************************************************************************************/
/*                                                                                   */
/*      Portail web pour PMB                                                         */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@openstudio.fr                                                   */
/*      web : http://www.openstudio.fr                                               */
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


include_spip('inc/config');

// charger les fonctions pour le compilateur SPIP
// boucles (PMB:NOTICES) ...
include_spip('public/pmb');


/**
 * Depile un element de tableau et renvoie le tableau (pas l'element depile !)
 * 
 * #SET{total,#GET{tableau/0/nb_resultat}}
 * #SET{tableau,#GET{tableau}|depile}
 * #SET{tableau,#GET{tableau}|depile{0}}
 *
 * @param array Tableau a depiler
 * @param string Eventuellement cle du tableau a enlever, sinon prend la premiere (array_shift)
 * @return array Tableau depossede d'une cle...
**/
function depile($tableau, $cle=null) {
	if (!is_array($tableau)) {
		return array();
	}
	if (is_null($cle)) {
		array_shift($tableau);
	} else {
		if (!is_array($cle) and !is_object($cle)) {
			unset($tableau[$cle]);
		}
	}
	return $tableau;
}


function pmb_section_extraire($id_section) {
	$tableau_sections = Array();
	try {
		//récupérer les infos sur la section parent
		$ws = pmb_webservice();
		$section_parent = $ws->pmbesOPACGeneric_get_section_information($id_section);
		$tableau_sections[0] = Array();
		$tableau_sections[0]['section_id'] = $section_parent->section_id;
		$tableau_sections[0]['section_location'] = $section_parent->section_location;
		$tableau_sections[0]['section_caption'] = $section_parent->section_caption;
		$tableau_sections[0]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section_parent->section_image;

		$tab_sections = $ws->pmbesOPACGeneric_list_sections($id_section);
		pmb_extraire_sections_infos($tableau_sections, $tab_sections);
	} catch (Exception $e) {
		 echo 'Exception reçue (1): ',  $e->getMessage(), "\n";
	}
	return $tableau_sections;
}


function pmb_location_extraire($id_location) {
	$tableau_locationsections = Array();
	try {
		$ws = pmb_webservice();
		$tab_locations = $ws->pmbesOPACGeneric_get_location_information_and_sections($id_location);
		//récupérer les infos sur la localisation parent
		$tableau_locationsections[0] = Array();
		$tableau_locationsections[0]['location_id'] = $tab_locations->location->location_id;
		$tableau_locationsections[0]['location_caption'] = $tab_locations->location->location_caption;

		pmb_extraire_sections_infos($tableau_locationsections, $tab_locations->sections);
	} catch (Exception $e) {
		echo 'Exception reçue (2) : ',  $e->getMessage(), "\n";
	}
	return $tableau_locationsections;
}


function pmb_extraire_sections_infos(&$tableau, $sections) {
	$cpt = 1;
	if (is_array($sections)) {
		foreach ($sections as $section) {
			$tableau[$cpt] = Array();
			$tableau[$cpt]['section_id']		= $section->section_id;
			$tableau[$cpt]['section_location']	= $section->section_location;
			$tableau[$cpt]['section_caption']	= $section->section_caption;
			$tableau[$cpt]['section_image']		= lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section->section_image;
			$cpt++;
		}
	}
}


function pmb_liste_afficher_locations() {
	$tableau_sections = Array();
	try {
		$ws = pmb_webservice();
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
	} catch (Exception $e) {
		 echo 'Exception reçue (3) : ',  $e->getMessage(), "\n";
	}
	return $tableau_locations;
}

/* aucune occurrence ? */
function pmb_notices_section_extraire($id_section, $debut=0, $fin=5) {
	$tableau_resultat = Array();
	
	$search = array();
	$search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);

	try {
		$ws = pmb_webservice();
		$r=$ws->pmbesOPACAnonymous_advancedSearch($search);
		$searchId=$r["searchId"];
		$nb = $r["nbResults"];
		//$r=$ws->pmbesOPACAnonymous_fetchSearchRecords($searchId,$debut,$fin,"serialized_unimarc","utf-8");
		$r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$fin,"utf-8");
		if (is_array($r)) {
			$tableau_resultat = array_map('pmb_ws_parser_notice', $r);
		}
		array_unshift($tableau_resultat, array('nb_resultats' => $nb));
	} catch (Exception $e) {
		 echo 'Exception reçue (4) : ',  $e->getMessage(), "\n";
	} 

	return $tableau_resultat;
}



function pmb_collection_extraire($id_collection, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	try {
		$ws = pmb_webservice();
		$result = $ws->pmbesCollections_get_collection_information_and_notices($id_collection,$id_session);
		if ($result) {
			$tableau_resultat['collection_id']     = $result->information->collection_id;
			$tableau_resultat['collection_name']   = $result->information->collection_name;
			$tableau_resultat['collection_parent'] = $result->information->collection_parent;
			$tableau_resultat['collection_issn']   = $result->information->collection_issn;
			$tableau_resultat['collection_web']    = $result->information->collection_web;
			$tableau_resultat['notice_ids']        = Array();

			pmb_extraire_resultats($result, $tableau_resultat, $debut, $nbresult);
		}
	
	} catch (Exception $e) {
		 echo 'Exception reçue (5) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;
}


/**
 * Calculer le total des elements
 * Extraire la pagination
 * Et calculer les valeurs des resultats
 *
 * @param 
 * @return 
**/
function pmb_extraire_resultats($result, &$tableau_resultat, $debut, $nbresult) {
	$liste_notices = Array();
	$cpt=0;
	if (is_array($result->notice_ids)) {
		$cpt = count($result->notice_ids);
		$liste_notices = array_slice($result->notice_ids, $debut, $nbresult);
	}
	$tableau_resultat['notice_ids'] = pmb_ws_recuperer_tab_notices($liste_notices);
	array_unshift($tableau_resultat['notice_ids'], array('nb_resultats' => $cpt));
	#pmb_remettre_id_dans_resultats($tableau_resultat, $liste_notices);
}

/**
 * A priori du code mort...
 * Mais on le mutualise dans une fonction
 *
**/
function pmb_remettre_id_dans_resultats(&$tabreau_resultat, $liste_notices) {
	if (is_array($liste_notices)) {
		foreach($liste_notices as $cle => $notice) {
			$tableau_resultat['notice_ids'][$cle]['id'] = $notice;
		}
	}
}


function pmb_editeur_extraire($id_editeur, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();

	try {
		$ws = pmb_webservice();
		$result = $ws->pmbesPublishers_get_publisher_information_and_notices($id_editeur,$id_session);
		if ($result) {
			$tableau_resultat['publisher_id']       = $result->information->publisher_id;
			$tableau_resultat['publisher_name']     = $result->information->publisher_name;
			$tableau_resultat['publisher_address1'] = $result->information->publisher_address1;
			$tableau_resultat['publisher_address2'] = $result->information->publisher_address2;
			$tableau_resultat['publisher_zipcode']  = $result->information->publisher_zipcode;
			$tableau_resultat['publisher_city']     = $result->information->publisher_city;
			$tableau_resultat['publisher_country']  = $result->information->publisher_country;
			$tableau_resultat['publisher_web']      = $result->information->publisher_web;
			$tableau_resultat['publisher_comment']  = $result->information->publisher_comment;
			$tableau_resultat['notice_ids'] = Array();

			pmb_extraire_resultats($result, $tableau_resultat, $debut, $nbresult);
		}
	}catch (Exception $e) {
		echo 'Exception reçue (6) : ',  $e->getMessage(), "\n";
	}
	return $tableau_resultat;

}

function pmb_auteur_extraire($id_auteur, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	try {
		$ws = pmb_webservice();
		$result = $ws->pmbesAuthors_get_author_information_and_notices($id_auteur,$id_session);
		if ($result) {
			$tableau_resultat['author_id']     = $result->information->author_id;
			$tableau_resultat['author_type']   = $result->information->author_type;
			$tableau_resultat['author_name']   = $result->information->author_name;
			$tableau_resultat['author_rejete'] = $result->information->author_rejete;
			if ($result->information->author_rejete) {
				$tableau_resultat['author_nomcomplet'] =  $tableau_resultat['author_rejete'].' '.$tableau_resultat['author_name'];
			} else {
				$tableau_resultat['author_nomcomplet'] = $tableau_resultat['author_name'];
			}

			$tableau_resultat['author_see']      = $result->information->author_see;
			$tableau_resultat['author_date']     = $result->information->author_date;
			$tableau_resultat['author_web']      = $result->information->author_web;
			$tableau_resultat['author_comment']  = $result->information->author_comment;
			$tableau_resultat['author_lieu']     = $result->information->author_lieu;
			$tableau_resultat['author_ville']    = $result->information->author_ville;
			$tableau_resultat['author_pays']     = $result->information->author_pays;
			$tableau_resultat['author_subdivision'] = $result->information->author_subdivision;
			$tableau_resultat['author_numero']      = $result->information->author_numero;
			$tableau_resultat['notice_ids'] = Array();

			pmb_extraire_resultats($result, $tableau_resultat, $debut, $nbresult);
		}
	} catch (Exception $e) {
		 echo 'Exception reçue (7) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;

}

function pmb_recherche_extraire($recherche='', $look_ALL='', $look_AUTHOR='', $look_PUBLISHER='', $look_COLLECTION='', $look_SUBCOLLECTION='', $look_CATEGORY='', $look_INDEXINT='', $look_KEYWORDS='', $look_TITLE='', $look_ABSTRACT='', $id_section='', $debut=0, $fin=5, $typdoc='',$id_location='') {
	$tableau_resultat = Array();
	//$recherche = strtolower($recherche);
	$search = array();
	$searchType = 0;
	$type_recherche=0;

	if ($recherche=='*') {
		$recherche='';
	}

	if ($typdoc)		$search[] = array("inter"=>"and", "field"=>15, "operator"=>"EQ", "value"=>$typdoc);
	if ($id_section)	$search[] = array("inter"=>"and", "field"=>17, "operator"=>"EQ", "value"=>$id_section);
	if ($id_location)	$search[] = array("inter"=>"and", "field"=>16, "operator"=>"EQ", "value"=>$id_location);
	
	if ($look_ALL) {
		if ($recherche) $search[] = array("inter"=>"or","field"=>42,"operator"=>"BOOLEAN", "value"=>$recherche);
	} else {
		if ($look_TITLE) {
			$searchType = 1;
			if ($recherche) $search[] = array("inter"=>"or","field"=>1,"operator"=>"BOOLEAN", "value"=>$recherche);
		}

		if ($look_AUTHOR) {
			$searchType = 2;
			if ($recherche) $search[] = array("inter"=>"or","field"=>2,"operator"=>"BOOLEAN", "value"=>$recherche);
		}
	    
		if ($look_PUBLISHER) {
			$searchType = 3;
			if ($recherche) $search[] = array("inter"=>"or","field"=>3,"operator"=>"BOOLEAN", "value"=>$recherche);
		}

		if ($look_COLLECTION) {
			$searchType = 4;
			if ($recherche) $search[] = array("inter"=>"or","field"=>4,"operator"=>"BOOLEAN", "value"=>$recherche);
		}

		if ($look_ABSTRACT) {
			if ($recherche) $search[] = array("inter"=>"or","field"=>10,"operator"=>"BOOLEAN", "value"=>$recherche);
		}
	  
		if ($look_CATEGORY) {
			$searchType = 6;
			if ($recherche) $search[] = array("inter"=>"or","field"=>11,"operator"=>"BOOLEAN", "value"=>$recherche);
		}

		if ($look_INDEXINT) {
			if ($recherche) $search[] = array("inter"=>"or","field"=>12,"operator"=>"BOOLEAN", "value"=>$recherche);
		}

		if ($look_KEYWORDS) {
			if ($recherche) $search[] = array("inter"=>"","field"=>13,"operator"=>"BOOLEAN", "value"=>$recherche);
		}
	}

	try {
		$ws = pmb_webservice();
		$tableau_resultat[0] = Array();

		//cas d'une recherche simple 
		if (($look_ALL)&&(!$id_section)&&(!$typdoc)){
			$r = $ws->pmbesOPACAnonymous_simpleSearch($searchType,$recherche);
		/*
		} else if (($look_ALL)&&($id_section)&&(!$typdoc)){
			$r=$ws->pmbesSearch_simpleSearchLocalise($searchType,$recherche,$id_location,$id_section);
		*/
		} else {
			try {
				$r=$ws->pmbesOPACAnonymous_advancedSearch($search);
			} catch (Exception $e) {
				echo 'Exception reçue (8) : ',  $e->getMessage(), "\n";
			}
		}
		
		$searchId=$r->searchId;
		$nb = $r->nbResults;
		$r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$fin,"utf-8");
		if (is_array($r)) {
			$tableau_resultat = array_map('pmb_ws_parser_notice', $r);
		}
		array_unshift($tableau_resultat, array('nb_resultats' => $nb));
	} catch (Exception $e) {
		echo 'Exception reçue (8) : ',  $e->getMessage(), "\n";
	}
	
	return $tableau_resultat;
}


function pmb_recuperer_champs_recherche($langue=0) {
	$tresultat = Array();
	
	try {
		$ws = pmb_webservice();
		$result = $ws->pmbesSearch_getAdvancedSearchFields('opac|search_fields',$langue,true);
		$cpt=0;
		if (is_array($result)) {
			foreach ($result as &$res) {
				$tresultat[$cpt] = Array();
				$tresultat[$cpt]['id'] = $res->id;
				$tresultat[$cpt]['label'] = $res->label;
				$tresultat[$cpt]['type'] = $res->type;
				$tresultat[$cpt]['operators'] = $res->operators;
				$tresultat[$cpt]['values'] = Array();
				$cpt2=0;
				if (is_array($res->values)) {
					foreach ($res->values as &$value) {
						$tresultat[$cpt]['values'][$cpt2]['value_id'] = $value->value_id;
						$tresultat[$cpt]['values'][$cpt2]['value_caption'] = $value->value_caption;
						$cpt2++;
					}
				}
				$cpt++;
			}
		}
	} catch (Exception $e) {
		 echo 'Exception reçue (9) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;
}


/**
 * Analyse un tableau de proprietes UNIMARC
 * Contrairement aux apparences, ces numeros
 * et cles signifient quelque chose !
 * 
 * http://www.bnf.fr/fr/professionnels/anx_formats/a.unimarc_manuel_format_bibliographique.html
 * http://www.bnf.fr/documents/UNIMARC%28B%29_conversion.pdf
 * 
 * @param array $value Tableau UNIMARC a traduire
 * @return array Tableau traduit
**/
function pmb_ws_parser_notice($value) {
	
	// mise en cache des resultats en fonction de $value
	static $resultats = array();
	// on utilise le cache s'il est la.
	$hash = md5(serialize($value));
	if (isset($resultats[$hash])) {
		return $resultats[$hash];
	}
	
	$id_notice = $value->id;
	
	include_spip("inc/filtres_images");
	$indice_exemplaire = 0;
	$tresultat = Array();
	$authors_700 = array();
	$authors_701 = array();
	$authors_702 = array();
	
	if (isset($value->f) && is_array($value->f)) {
		foreach($value->f as $a_field_f) {
			$field_type = $a_field_f->c;
			$field_id = $a_field_f->id;
			
			if (isset($a_field_f->s) && is_array($a_field_f->s)) {
				foreach($a_field_f->s as $a_field_s) {
					$field_subtype = $a_field_s->c;
					$field_value = $a_field_s->value;
					
					switch($field_type) {
						case '010': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['isbn'] .= $field_value;
									break;
								}
								case 'b': {
									$tresultat['reliure'] .= $field_value;
									break;
								}
								case 'd': {
									$tresultat['prix'] .= $field_value;
									break;
								}
							}
							break;
						}
						case '101': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['langues'] .= $field_value;
									break;
								}
							}
							break;
						}
						case '102': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['pays'] .= $field_value;
									break;
								}
							}
							break;
						}
						case '200': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['titre'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$field_value))))));
									break;
								}
								case 'e': {
									$tresultat['soustitre'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$field_value))))));
									break;
								}
								case 'f': {
									$tresultat['auteur'] .= $field_value;
									break;
								}
							}
							break;
						}
						case '210': {
							switch($field_subtype) {
								case 'c': {
									$tresultat['editeur'] .= $field_value;
									break;
								}
								case 'a': {
									$tresultat['editeur'] .= ' ('.$field_value.')';
									$tresultat['id_editeur'] = $field_id;
									break;
								}
								case 'd': {
									$tresultat['annee_publication'] .= $field_value;
									break;
								}
							}
							break;
						}
						case '215': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['importance'] .= $field_value;
									break;
								}
								case 'c': {
									$tresultat['presentation'] .= $field_value;
									break;
								}
								case 'd': {
									$tresultat['format'] .= $field_value;
									break;
								}
							}
							break;
						}
						case '225': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['collection'] .= $field_value;
									$tresultat['id_collection'] = $field_id;
									break;
								}
							}
							break;
						}
						case '330': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['resume'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$field_value))))));
									break;
								}
							}
							break;
						}
						case '700': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['id_auteur'] = $field_id;
									$authors_700[] = "<a href=\"?page=author_see&amp;id=".$field_id."\">".$field_value."</a>";
									$tresultat['lesauteurs'] .= $field_value;
									break;
								}
								case 'b': {
									$tresultat['lesauteurs'] = $field_value." ".$tresultat['lesauteurs'];
									$tresultat['liensauteurs'] .= " ".$field_value;
									break;
								}
							}
							break;
						}
						case '701': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['id_auteur2'] = $field_id;
									$authors_701[] = "<a href=\"?page=author_see&amp;id=".$field_id."\">".$field_value."</a>";
									$tresultat['lesauteurs2'] .= $field_value;
									break;
								}
								case 'b': {
									$tresultat['lesauteurs2'] = $field_value." ".$tresultat['lesauteurs'];
									$tresultat['liensauteurs2'] .= " ".$field_value;
									break;
								}
							}
							break;
						}
						case '702': {
							switch($field_subtype) {
								case 'a': {
									$tresultat['id_auteur3'] = $field_id;
									$authors_702[] = "<a href=\"?page=author_see&amp;id=".$field_id."\">".$field_value."</a>";
									$tresultat['lesauteurs3'] .= $field_value;
									break;
								}
								case 'b': {
									$tresultat['lesauteurs3'] = $field_value." ".$tresultat['lesauteurs'];
									$tresultat['liensauteurs3'] .= " ".$field_value;
									break;
								}
							}
							break;
						}
					}

				}
			}
			
		}
	}
	
	$tresultat['liensauteurs']  = implode(', ', $authors_700);
	$tresultat['liensauteurs2'] = implode(', ', $authors_701);
	$tresultat['liensauteurs3'] = implode(', ', $authors_702);

	if ($tresultat['lesauteurs'] == "") {
		$tresultat['lesauteurs'] = $tresultat['auteur'];
	}
	$tresultat['logo_src'] = rtrim(lire_config("spip_pmb/url","http://tence.bibli.fr/opac"),'/')."/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F!!isbn!!.08.MZZZZZZZ.jpg&noticecode=".str_replace("-","",$tresultat['isbn']);

	//si pas de numéro isbn (exemple jouets ludothèque) il n'y aura pas de logo
	if ($tresultat['isbn'] == '') $tresultat['logo_src'] = '';

	$tresultat['id'] = $id_notice;

	// on stocke en cache
	$resultats[$hash] = $tresultat;
	return $tresultat;
}


/**
 * Retourne la liste des notices
 * ayant etes empruntees par les autres lecteurs
 * ayant empruntes la ou les notices en parametres
 *
 * @param array identifiant(s) de notice
 * @return array identifiant(s) de notices en relation
**/
function pmb_ws_ids_notices_autres_lecteurs($ids_notice) {
	if (!is_array($ids_notice)) {
		$ids_notice = array($ids_notice);
	}

	$listenotices = Array();

	try {
		$ws = pmb_webservice();
		if ($ws->pmbesOPACGeneric_is_also_borrowed_enabled()) {
			foreach ($ids_notice as $id_notice) {
				$r = $ws->pmbesOPACGeneric_also_borrowed($id_notice, 0);
				if (is_array($r)) {
					foreach ($r as $notice) {
						$listenotices[] = $notice['notice_id'];
					}
				}
			}
			$listenotices = array_unique($listenotices);
		}
	}catch (Exception $e) {
		echo 'Exception reçue (10) : ',  $e->getMessage(), "\n";
	}
	
	return $listenotices;
}




function pmb_ws_documents_numeriques ($id_notice, $id_session=0) {

	$tresultat = Array();

	try {
		$ws = pmb_webservice();
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

	} catch (Exception $e) {
		 echo 'Exception reçue (11) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;

}

function pmb_ws_dispo_exemplaire($id_notice, $id_session=0) {
  
	$tresultat = Array();

	try {
		$ws = pmb_webservice();
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

	} catch (Exception $e) {
		 echo 'Exception reçue (12) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;
}


// récuperer une notice en xml via les webservices
// les parser, et stocker en cache statique car cette fonction
// est utilisee par toutes les boucles (PMB:NOTICES)
function pmb_ws_recuperer_tab_notices($listenotices) {

	if (!is_array($listenotices)) {
		return false;
	}
	
	// on met en cache le resultat et on utilise le cache.
	// afin d'optimiser si plusieurs boucles sont utilisees.
	static $notices = array();
	$wanted = $listenotices;
	// ce qu'on a trouve...
	$res = array();
	
	foreach ($listenotices as $c=>$l) {
		if (isset($notices[$l])) {
			$res[$c] = $notices[$l];
			unset($wanted[$c]);
		}
	}

	// si on a tout trouve, on s'en va...
	if (!count($wanted)) {
		return $res;
	}

	// sinon on complete ce qui manque en interrogeant PMB
	try {
		$ws = pmb_webservice();
		$r=$ws->pmbesNotices_fetchNoticeListArray($wanted,"utf-8",true,false);
		if (is_array($r)) {
			$r = array_map('pmb_ws_parser_notice', $r);
			// on complete notre tableau de resultat
			// avec nos trouvailles
			foreach ($r as $notice) {
				$key = array_search($notice['id'], $listenotices);
				if ($key !== false) {
					$notices[ $notice['id'] ] = $res[$key] = $notice;
				}
			}
		}
	} catch (Exception $e) {
		echo 'Exception reçue (14) : ',  $e->getMessage(), "\n";
	}
	
	return $res;
}


/**
 * Charge le web service
 * qui permet de requeter sur notre PMB
 * 
 * @return object WebService pour PMB
**/
function pmb_webservice() {
	static $ws = null;
	if ($ws) {
		return $ws;
	}

	try {
		$rpc_type = lire_config("spip_pmb/rpc_type","soap");
		if ($rpc_type == "soap") {
			ini_set("soap.wsdl_cache_enabled", "0");
			$ws = new SoapClient(lire_config("spip_pmb/wsdl", "http://tence.bibli.fr/pmbws/PMBWsSOAP_1?wsdl"), array("features" => SOAP_SINGLE_ELEMENT_ARRAYS, 'encoding' => 'iso8859-1'));
		}
		else {
			include_spip('jsonRPCClient');
			$ws = new jsonRPCClient(lire_config("spip_pmb/jsonrpc", ""), false);
		}
	}
	catch (Exception $e) {
		echo 'Exception reçue (15) : ',  $e->getMessage(), "\n";
	} 

	return $ws;
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
	
	try {
		$ws = pmb_webservice();
		$tresultat=$ws->pmbesSearch_get_sort_types();
	} catch (Exception $e) {
		echo 'Exception reçue (16) : ',  $e->getMessage(), "\n";
	}
	return $tresultat;
}

// retourne un tableau associatif contenant tous les champs d'une notice 
function pmb_notice_extraire ($id_notice) {
	$tableau_resultat = pmb_ws_recuperer_tab_notices(array((string)$id_notice));
	$notice = array_shift($tableau_resultat);
	return $notice;
}


// retourne un tableau associatif contenant tous les champs d'un tableau d'id de notices 
function pmb_tabnotices_extraire ($tabnotices) {
	$listenotices = Array();
	if (is_array($tabnotices)) {
		$listenotices = array_values($tabnotices);
	}

	return pmb_ws_recuperer_tab_notices($listenotices);
}

// retourne un tableau associatif contenant les prêts en cours
function pmb_prets_extraire ($session_id, $type_pret=0) {
	$tableau_resultat = Array();
	try{
		$ws = pmb_webservice();
		$loans = $ws->pmbesOPACEmpr_list_loans($session_id, $type_pret);
		$liste_notices = Array();
		$cpt = 0;
		if (is_array($loans)) {
			foreach ($loans as $loan) {
				$liste_notices[] = $loan->notice_id;
				$tableau_resultat[$cpt] = Array();
				$tableau_resultat[$cpt]['empr_id'] = $loan->empr_id;
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
			$tableau_resultat['notice_ids'] = pmb_ws_recuperer_tab_notices($liste_notices);  
		}
		#pmb_remettre_id_dans_resultats(&$tabreau_resultat, $liste_notices);
		
	} catch (Exception $e) {
		 echo 'Exception reçue (17) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;
			
}

function pmb_reservations_extraire($pmb_session) {
	$tableau_resultat = Array();
	
	$ws = pmb_webservice();
	$reservations = $ws->pmbesOPACEmpr_list_resas($pmb_session);
	$liste_notices = Array();
	
	$cpt = 0;
	if (is_array($reservations)) {
		foreach ($reservations as $reservation) {
			$liste_notices[] = $reservation->notice_id;
			$tableau_resultat[$cpt] = Array();
			$tableau_resultat[$cpt]['resa_id']		= $reservation->resa_id;
			$tableau_resultat[$cpt]['empr_id']		= $reservation->empr_id;
			$tableau_resultat[$cpt]['notice_id']	= $reservation->notice_id;
			$tableau_resultat[$cpt]['bulletin_id']	= $reservation->bulletin_id;
			$tableau_resultat[$cpt]['resa_rank']	= $reservation->resa_rank;
			$tableau_resultat[$cpt]['resa_dateend']	= $reservation->resa_dateend;
			$tableau_resultat[$cpt]['resa_retrait_location_id ']	= $reservation->resa_retrait_location_id ;
			$tableau_resultat[$cpt]['resa_retrait_location']		= $reservation->resa_retrait_location;

			$cpt++;
		}
	}
	if ($cpt>0) {
		$tableau_resultat['notice_ids'] = pmb_ws_recuperer_tab_notices($liste_notices);  
	}
	#pmb_remettre_id_dans_resultats(&$tabreau_resultat, $liste_notices)
	return $tableau_resultat;

}

/**
 *  tester si la session pmb est toujours active
**/
function pmb_tester_session($pmb_session, $id_auteur) {
	try {
		$ws = pmb_webservice();
		if ($ws->pmbesOPACEmpr_get_account_info($pmb_session)) {
			return 1;
		} else {
			$m = sql_updateq('spip_auteurs_pmb', array('pmb_session' => ''), "id_auteur=".$id_auteur);
			return 0;
		}
	} catch (Exception $e) {
		$m = sql_updateq('spip_auteurs_pmb', array('pmb_session' => ''), "id_auteur=".$id_auteur);
		return 0;
	}
}


function pmb_reserver_ouvrage($session_id, $notice_id, $bulletin_id, $location) {

	$result= Array();

	$ws = pmb_webservice();
	$result = $ws->pmbesOPACEmpr_add_resa($session_id, $notice_id, $bulletin_id, $location);

	if (!$result->success) {
		if ($result->error == "no_session_id") return "La réservation n'a pas pu être réalisée pour la raison suivante : pas de session";
		else if ($result->error == "no_empr_id") return "La réservation n'a pas pu être réalisée pour la raison suivante : pas d'id emprunteur";
		else if ($result->error == "check_empr_exists") return "La réservation n'a pas pu être réalisée pour la raison suivante : id emprunteur inconnu";
		else if ($result->error == "check_notice_exists") return "La réservation n'a pas pu être réalisée pour la raison suivante : Notice inconnue";
		else if ($result->error == "check_quota") return "La réservation n'a pas pu être réalisée pour la raison suivante : violation de quotas: Voir message complémentaire";
		else if ($result->error == "check_resa_exists") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document déjà réservé par ce lecteur";
		else if ($result->error == "check_allready_loaned") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document déjà emprunté par ce lecteur";
		else if ($result->error == "check_statut") return "La réservation n'a pas pu être réalisée pour la raison suivante : Pas de document prêtable";
		else if ($result->error == "check_doc_dispo") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document disponible, mais non réservable";
		else if ($result->error == "check_localisation_expl") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document non réservable dans les localisations autorisées";
		else if ($result->error == "resa_no_create") return "La réservation n'a pas pu être réalisée pour la raison suivante : échec de l'enregistrement de la résevation";
		else return "La réservation n'a pas pu être réalisée pour la raison suivante : ".$result->error;
	} else {
		return "Votre réservation a été enregistrée";
	}
/* Description des entrées:

	session_id type string        Le numéro de session
	notice_id type integer        l'id de la notice
	bulletin_id type integer        l'id du bulletin
	location type integer        la localisation de retrait ni applicable
	Description des retours:

	success type boolean        Un boolean indiquant le succès éventuel de l'opération
	error type string        Code d'erreur si la réservation n'est pas effectuée:
	no_session_id (pas de session)
	no_empr_id (pas d'id emprunteur)
	check_empr_exists (id emprunteur inconnu)
	check_notice_exists (Notice inconnue)
	check_quota (violation de quotas: Voir message complémentaire)
	check_resa_exists (Document déjà réservé par ce lecteur)
	check_allready_loaned (Document déjà emprunté par ce lecteur)
	check_statut (Pas de document prêtable)
	check_doc_dispo (Document disponible, mais non réservable)
	check_localisation_expl (Document non réservable dans les localisations autorisées)
	resa_no_create (échec de l'enregistrement de la résevation)
	message type string        Message d'information complémentaire
*/
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


?>

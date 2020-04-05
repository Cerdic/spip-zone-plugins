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
 * Recupere les informations de locations racine,
 * c'est a dire la liste des centres documentaires geres par ce PMB
 * 
 * @return array
 * 		Tableau contenant pour chaque lieu la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_locations_racine() {

	static $locations = array();
	if (count($locations)) {
		return $locations;
	}

	try {
		$ws = pmb_webservice();
		$r = $ws->pmbesOPACGeneric_list_locations();
		if (is_array($r)) {
			foreach ($r as $index => $location) {
				$locations[] = array(
					'id_location' => $location->location_id,
					'titre'       => $location->location_caption,
				);
			}
		}
	} catch (Exception $e) {
		 echo 'Exception reçue (3) : ',  $e->getMessage(), "\n";
	}
	return $locations;
}

/**
 * Retourne la liste de tous les rayonnages d'un lieu racine
 *
 * @param array $ids_location
 * 		Les parents
 * 
 * @return array
 * 		Tableau contenant pour chaque section la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_sections_depuis_locations_ids($ids_location) {
	$res = pmb_extraire_abstract_ids('locations', $ids_location,
		'pmbesOPACGeneric_get_location_information_and_sections');
		
	return pmb_get_enfants($res, 'sections');
}

/**
 * Partie d'extraction d'une location
 * dans un resultat de requete a PMB
**/
function pmb_extraire_resultat_locations_id($ws_result) {
	$r = $ws_result;

	//infos de la location
	//récupérer les infos sur la localisation parent
	$l = array();
	$l['id_location_parent'] = $r->location->location_id;
	$l['titre_parent']       = $r->location->location_caption;
	$l['sections'] = array();

	if (count($r->sections)) {
		foreach ($r->sections as $section) {
			// enlever l'image par defaut
			$l['sections'][] = $s = pmb_extraire_section($section);
			// on ne met pas en cacheces sections ci
			// parce qu'on ne sait pas si elles ont ou non des enfants...
			// on laissera pmb_extraire_sections_depuis_sections_ids decider...
			#pmb_cacher('sections', $section->section_id, $s, true);
		}
	}
	return $l;
}


/**
 * Retourne la liste de tous les sous rayonnages d'un rayonnage...
 *
 * @param array $ids_section
 * 		Les parents
 * 
 * @return array
 * 		Tableau contenant pour chaque section la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_sections_depuis_sections_ids($ids_section) {
	$res = pmb_extraire_sections_ids($ids_section);
	return pmb_get_enfants($res, 'sections');
}


/**
 * Retourne les rayonnages demandes...
 *
 * @param array $ids_section
 * 		Les demandes
 * 
 * @return array
 * 		Tableau contenant pour chaque section la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_sections_ids($ids_section) {
	return pmb_extraire_abstract_ids('sections', $ids_section, 'pmbesOPACGeneric_get_section_information');
}

/**
 * Partie d'extraction d'une section
 * dans un resultat de requete a PMB
**/
function pmb_extraire_resultat_sections_id($ws_result, $id_section) {
	$r = $ws_result;
	$s = pmb_extraire_section($r);
	// recherche de sous sections
	$ws = pmb_webservice();
	$sections = $ws->pmbesOPACGeneric_list_sections($id_section);
	if (count($sections)) {
		foreach ($sections as $section) {
			$s['sections'][] = $ss = pmb_extraire_section($section);
			#pmb_cacher('sections_simples', $ss['id_section'], $ss, true);
		}
	}
	return $s;
}


/**
 * Retourne des petites infos sur les sections issues de requetes a pmb 
**/
function pmb_extraire_section($section) {
	// enlever l'image par defaut
	$image = (false !== strpos($section->section_image,'rayonnage') ? '' : $section->section_image);
	$s = array(
		'id_section'   => $section->section_id,
		'titre'        => $section->section_caption,
		'image'        => $image,
	);
	return $s;
}


/**
 * Recupere les informations de notices
 * dont les identifiants sont fournis par
 * le tableau $ids_notices.
 *
 * Chaque identifiant calcule est mis en cache
 * pour eviter des requetes intempestives
 * sur un hit de page et permettre d'utiliser plusieurs
 * fois une boucle telle que (PMB:NOTICES){id_notice}
 * sans avoir besoin de tout recalculer.
 *
 * Notons que l'on ne peut recuperer que les champs "exportables"
 * dans PMB.
 *
 * @param array $ids_notices
 * 		Tableau d'id de notices a recupereer
 * @return array
 * 		Tableau contenant pour chaque notice la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_notices_ids($ids_notices) {
	if (!is_array($ids_notices)) {
		return array();
	}

	// retrouver les infos en cache
	list($res, $wanted) = pmb_cacher('notices', $ids_notices);

	// si on a tout trouve, on s'en va...
	if (!count($wanted)) {
		return $res;
	}

	// sinon on complete ce qui manque en interrogeant PMB
	try {
		$ws = pmb_webservice();
		$r=$ws->pmbesNotices_fetchNoticeListArray($wanted,"utf-8",true,false);
		if (is_array($r)) {
			// s'il n'y a pas de résultats suffisants, ça retourne (parfois ?) 
			// un tableau complété jusqu'à 100 entrées, mais avec des infos vides.
			// on les enlèves, ces vides, avant d'analyser chaque contenu retourné.
			$r = array_filter($r);
			$r = array_map('pmb_ws_parser_notice', $r);

			// on complete notre tableau de resultat
			// avec nos trouvailles
			foreach ($r as $notice) {
				$key = array_search($notice['id'], $ids_notices);
				if ($key !== false) {
					$res[$key] = $notice;
					pmb_cacher('notices', $notice['id'], $notice, true);
				}
			}
		}
	} catch (Exception $e) {
		echo 'Exception reçue (14) : ',  $e->getMessage(), "\n";
	}
	ksort($res);
	return $res;
}





/**
 * Recupere les informations des collections
 * dont les identifiants sont fournis par
 * le tableau $ids_collection.
 * 
 * @param array $ids_collection
 * 		Tableau d'id de collections a recupereer
 * @return array
 * 		Tableau contenant pour chaque collection la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_collections_ids($ids_collection) {
	return pmb_extraire_abstract_ids('collections', $ids_collection, 'pmbesCollections_get_collection_information_and_notices');
}

/**
 * Partie d'extraction d'une collection
 * dans un resultat de requete a PMB
**/ 
function pmb_extraire_resultat_collections_id($ws_result) {
	$r = $ws_result;
	//infos de la collection
	$c = array();
	$c['id_collection']  = $r->information->collection_id;
	$c['titre']          = $r->information->collection_name;
	$c['id_parent']      = $r->information->collection_parent;
	$c['issn']           = $r->information->collection_issn;
	$c['web']            = $r->information->collection_web;
	$c['ids_notice']     = $r->notice_ids;
	return $c;
}




/**
 * Recupere les informations des editeurs
 * dont les identifiants sont fournis par
 * le tableau $ids_editeur.
 *
 * @param array $ids_editeur
 * 		Tableau d'id des editeurs a recupereer
 * @return array
 * 		Tableau contenant pour chaque editeur la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_editeurs_ids($ids_editeur) {
	return pmb_extraire_abstract_ids('editeurs', $ids_editeur, 'pmbesPublishers_get_publisher_information_and_notices');
}

/**
 * Partie d'extraction d'un editeur
 * dans un resultat de requete a PMB
**/ 
function pmb_extraire_resultat_editeurs_id($ws_result) {
	$r = $ws_result;
	$e = array();
	//infos de l'editeur
	$e['id_editeur']   = $r->information->publisher_id;
	$e['nom']          = $r->information->publisher_name;
	$e['adresse1']     = $r->information->publisher_address1;
	$e['adresse2']     = $r->information->publisher_address2;
	$e['code_postal']  = $r->information->publisher_zipcode;
	$e['ville']        = $r->information->publisher_city;
	$e['pays']         = $r->information->publisher_country;
	$e['web']          = $r->information->publisher_web;
	$e['commentaire']  = $r->information->publisher_comment;
	$e['ids_notice']   = $r->notice_ids;
	return $e;
}




/**
 * Fonction d'abstraction pour mutualiser les
 * codes de selections en interrogeant PMB.

 * Chaque identifiant calcule (pour chaque objet) est mis en cache
 * pour eviter des requetes intempestives
 * sur un hit de page et permettre d'utiliser plusieurs
 * fois une boucle telle que (PMB:EDITEURS){id_editeur}
 * sans avoir besoin de tout recalculer.
 * 
 * @param string $objet
 * 		L'objet (pluriel) 'auteurs'
 * 		necessite une fonction pmb_extraire_resultat_$objet_id()
 * 		soit donc ici pmb_extraire_resultat_auteur_id()
 *
 * @param array $ids_objets
 * 		Les ids a obtenir les infos
 * 
 * @param string $ws_methode
 * 		Methode la classe de webservice de PMB a interroger
 * 		pour obtenir les resultats
 * 
 * @param string $extraire_fonction
 * 		Fonction pour parser chaque resultat obtenu
 * 		Par defaut : pmb_extraire_resultat_{$objet}_id
 *
 * @return array
 * 		Un tableau cle/valeur par element touve
 * 
**/
function pmb_extraire_abstract_ids($objet, $ids_objet, $ws_methode, $extraire_fonction='') {
	if (!is_array($ids_objet)) {
		return array();
	}
	
	// retrouver les infos en cache
	list($res, $wanted) = pmb_cacher($objet, $ids_objet);

	// si on a tout trouve, on s'en va...
	if (!count($wanted)) {
		return $res;
	}

	try {
		$ws = pmb_webservice();
		foreach ($wanted as $id_objet) {
			// second parametre $id_session n'etait pas utilise... kesako ?
			// et pas present partout...
			$r = $ws->$ws_methode($id_objet);
			if ($r) {
				if (!$extraire_fonction) {
					$extraire_fonction = 'pmb_extraire_resultat_' . $objet . '_id';
				}
				$infos = $extraire_fonction($r, $id_objet); // id_objet, aucasou
				
				$key = array_search($id_objet, $ids_objet);
				$res[$key] = $infos;
				pmb_cacher($objet, $id_objet, $infos, true);
			}
		}

	} catch (Exception $e) {
		 echo 'Exception reçue (7) : ',  $e->getMessage(), "\n";
	}

	return $res;
}




/**
 * Petite fonction d'aide pour recuperer d'une liste de tableaux
 * les valeurs compilees d'une cle de ces tableaux
 * 
 * entree :
 * 		tab[0][cle] = [n] (tableau...)
 * 		tab[1][cle] = [n]
 * 		tab[i][cle] = [n]
 * 
 * sortie :
 * 		tab[n] (merge de tous les tableaux [n])
 *
**/
function pmb_get_enfants($tableau, $cle) {
	$enfants = array();
	foreach ($tableau as $t) {
		if (isset($t[$cle]) and is_array($t[$cle])) {
			$enfants = array_merge($enfants, $t[$cle]);
		}
	}
	return $enfants;
}



/**
 * Recupere les informations d'auteurs
 * dont les identifiants sont fournis par
 * le tableau $ids_auteurs.
 *
 * Chaque identifiant calcule est mis en cache
 * pour eviter des requetes intempestives
 * sur un hit de page et permettre d'utiliser plusieurs
 * fois une boucle telle que (PMB:AUTEURS){id}
 * sans avoir besoin de tout recalculer.
 *
 * Notons que l'on ne peut recuperer que les champs "exportables"
 * dans PMB.
 *
 * @param array $ids_auteurs
 * 		Tableau d'id d'auteurs a recupereer
 * @return array
 * 		Tableau contenant pour chaque auteur la liste des champs
 * 		que l'on a pu recuperer.
**/
function pmb_extraire_auteurs_ids($ids_auteur) {
	return pmb_extraire_abstract_ids('auteurs', $ids_auteur, 'pmbesAuthors_get_author_information_and_notices');
}

/**
 * Partie d'extraction d'un auteur
 * dans un resultat de requete a PMB
**/ 
function pmb_extraire_resultat_auteurs_id($ws_result) {
	$r = $ws_result;
	$a = array();
	$a['id_auteur']       = $r->information->author_id;
	$a['id_type_auteur']  = $r->information->author_type;
	$a['nom']             = $r->information->author_name;
	$a['prenom']          = $r->information->author_rejete;
	if ($r->information->author_rejete) {
		$a['nomcomplet'] =  $a['prenom'].' '.$a['nom'];
	} else {
		$a['nomcomplet'] = $a['nom'];
	}
	// what's 'see' ?
	$a['id_voir']     = $r->information->author_see;
	$a['date']        = $r->information->author_date;
	$a['web']         = $r->information->author_web;
	$a['commentaire'] = $r->information->author_comment;
	$a['lieu']        = $r->information->author_lieu;
	$a['ville']       = $r->information->author_ville;
	$a['pays']        = $r->information->author_pays;
	$a['subdivision'] = $r->information->author_subdivision;
	$a['numero']      = $r->information->author_numero;
	$a['ids_notice']  = $r->notice_ids;
	return $a;
}





/**
 * Retourne la liste des notices
 * ayant etes empruntees par les autres lecteurs
 * ayant empruntes la ou les notices en parametres
 *
 * @param array identifiant(s) de notice
 * @return array identifiant(s) de notices en relation
**/
function pmb_ids_notices_autres_lecteurs($ids_notice) {
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



/**
 * Retourne la liste des documents d'une notice
 *
 * @param string $ids_notice
 * 		Liste des identifiants de notices dont les documents sont a recuperer
 * 
 * @return array
 * 		Liste des documents
**/
function pmb_extraire_documents_ids($ids_notice) {

	// retrouver les infos en cache
	list($res, $wanted) = pmb_cacher('documents', $ids_notice);

	// si on a tout trouve, on s'en va...
	if (!count($wanted)) {
		return pmb_get_enfants($res, 'documents');
	}
	
	try {
		$ws = pmb_webservice();
		foreach ($ids_notice as $id_notice) {
			// 2e. parametre $id_session non ulitise
			$r=$ws->pmbesNotices_listNoticeExplNums($id_notice);
			$documents = array();
			if (is_array($r)) {
				foreach ($r as $document) {
					$d = array();
					$d['titre']         = pmb_nettoyer_caracteres($document->name);
					$d['id_document'] = $document->id;
					$d['id_notice']   = $id_notice;
					$d['mimetype']    = $document->mimetype;
					$d['image']       = (strpos($d['mimetype'],'image')!==false)?'oui':'non';
					$d['source']                = $document->url;
					$d['source_telechargement'] = $document->downloadUrl;
					$documents[] = $d;
				}
			}
			// remettre dans les demandes et sauver en cache
			$key = array_search($id_notice, $ids_notice);
			$res[$key] = array('documents'=>$documents);
			pmb_cacher('documents', $id_notice, $res[$key], true);
		}

	} catch (Exception $e) {
		 echo 'Exception reçue (11) : ',  $e->getMessage(), "\n";
	} 

	// si on a tout trouve, on s'en va...
	return pmb_get_enfants($res, 'documents');
	
}


/**
 * Retourne la liste des exemplaires (item)
 * d'une notice (identifiant d'un ouvrage/document)
 *
 * @param string $ids_notice
 * 		Liste des identifiants de notices dont les exemplaires sont a recuperer
 * 
 * @return array
 * 		Liste des exemplaires
**/
function pmb_extraire_exemplaires_ids($ids_notice) {
	# meme fonctionnement que pmb_extraire_documents_ids()
	if (!$ids_notice) {
		return array();
	}
	$res = Array();

	try {
		$ws = pmb_webservice();
		foreach ($ids_notice as $id_notice) {
			// second parametre $id_session non utilise
			$exemplaires = $ws->pmbesItems_fetch_notice_items($id_notice);
			if (is_array($exemplaires)) {
				foreach ($exemplaires as $exemplaire) {
					$e = Array();
					$e['id']               = $exemplaire->id;
					$e['id_notice']        = $id_notice;
					$e['code_barre']       = $exemplaire->cb;
					$e['cote']             = $exemplaire->cote;
					$e['id_location']      = $exemplaire->location_id;
					$e['titre_location']   = $exemplaire->location_caption;
					$e['id_section']       = $exemplaire->section_id;
					$e['titre_section']    = $exemplaire->section_caption;
					$e['statut']           = $exemplaire->statut;
					$e['support']          = $exemplaire->support;
					$e['situation']        = $exemplaire->situation;
					$res[] = $e;
				}
			}
		}
	} catch (Exception $e) {
		 echo 'Exception reçue (12) : ',  $e->getMessage(), "\n";
	}
	
	return $res;
}


/**
 * Retourne la liste des prets
 * d'un auteur pmb authentifie
 *
 * @param string $ids_pmb_session
 * 		Sessions pmb de/des auteurs
 *
 * @param int $type_pret
 * 		Type de reponse :
 * 		1 : les prets en cours
 * 		0 : les prets en retard
 * 
 * @return array
 * 		Liste des prets
**/
function pmb_extraire_prets_ids($ids_pmb_session, $type_pret=0) {
	$ids_pmb_session = array_filter($ids_pmb_session);
	if (!$ids_pmb_session) {
		return array();
	}
	$prets = array();
	$notices = Array();
	$ws = pmb_webservice();
	
	foreach ($ids_pmb_session as $id_pmb_session) {
		$les_prets = $ws->pmbesOPACEmpr_list_loans($id_pmb_session, $type_pret);
		if (is_array($les_prets)) {
			foreach ($les_prets as $pret) {
				$p = array();
				$p['id_emprunteur']             = $pret->empr_id;
				$p['id_notice']                 = $pret->notice_id;
				$p['id_bulletin']               = $pret->bulletin_id;
				$p['exemplaire_id']             = $pret->expl_id;
				$p['exemplaire_code_barre']     = $pret->expl_cb;
				$p['exemplaire_support']        = $pret->expl_support;
				$p['exemplaire_id_location']    = $pret->expl_location_id;
				$p['exemplaire_titre_location'] = $pret->expl_location_caption;
				$p['exemplaire_id_section']     = $pret->expl_section_id;
				$p['exemplaire_titre_section']  = $pret->expl_section_caption;
				$p['exemplaire_libelle']        = $pret->expl_libelle;
				$p['date_debut']                = $pret->loan_startdate;
				$p['date_retour']               = $pret->loan_returndate;
				$notices[] = $pret->notice_id;
				$prets[] = $p;
			}
		}
	}
	if ($prets) {
		// on integre les informations des notices
		$notices = pmb_extraire_notices_ids($notices);
		// (pas tres optimise... mais y en a pas beaucoup)
		foreach ($prets as $c=>$p) {
			foreach ($notices as $n) {
				if ($p['id_notice'] == $n['id_notice']) {
					$prets[$c]['notice'] = $n;
					break;
				}
			}
		}
	}
	return $prets;
}

/**
 * Retourne les reservations demandes...
 *
 * @param array $ids_pmb_session
 * 		Les demandes, des 'pmb_session' (table spip_auteurs_pmb)
 * 
 * @return array
 * 		Tableau contenant les reservations
 * 		Et dans 'notice' de chaque reservation
 * 		les informations de la notice correspondante.
**/
function pmb_extraire_reservations_ids($ids_pmb_session) {
	$ids_pmb_session = array_filter($ids_pmb_session);
	if (!$ids_pmb_session) {
		return array();
	}
	
	$resas = array();
	$notices = Array();
	$ws = pmb_webservice();
	
	foreach ($ids_pmb_session as $id_pmb_session) {
		$reservations = $ws->pmbesOPACEmpr_list_resas($id_pmb_session);
		if (is_array($reservations)) {
			foreach ($reservations as $reservation) {
				$r = array();
				$r['id_reservation']      = $reservation->resa_id;
				$r['id_emprunteur']       = $reservation->empr_id;
				$r['id_notice']           = $reservation->notice_id;
				$r['id_bulletin']         = $reservation->bulletin_id;
				$r['rank']                = $reservation->resa_rank;
				$r['date_fin']            = $reservation->resa_dateend;
				$r['retrait_id_location'] = $reservation->resa_retrait_location_id ;
				$r['retrait_location']    = $reservation->resa_retrait_location;
				$notices[] = $reservation->notice_id;
				$resas[] = $r;
			}
		}
	}
	if ($resas) {
		// on integre les informations des notices
		$notices = pmb_extraire_notices_ids($notices);
		// (pas tres optimise... mais y en a pas beaucoup)
		foreach ($resas as $c=>$r) {
			foreach ($notices as $n) {
				if ($r['id_notice'] == $n['id_notice']) {
					$resas[$c]['notice'] = $n;
					break;
				}
			}
		}
	}
	return $resas;
}




/**
 * Retourne la liste des identifiants de notices trouvees 
 *
 * @param Array $demande
 * 		Description des différentes criteres de recherche (cle/valeur)
 * 		- recherche
 *
 * @param int $nbTotal
 * 		Sera renseigne par le nombre total de resultats trouves
 *
 * @param int $debut
 * 		Position du premier resultat renvoye
 *
 * @param int $nombre
 * 		Nombre de resultats renvoyes
 *
 * @return array
 * 		Ids des notices trouvees
**/
function pmb_ids_notices_recherches($demande, &$nbTotal, $debut=0, $nombre=5, $pagination=false) {

	$recherche          = isset($demande['recherche']) ? $demande['recherche'] : '';
	$id_section         = isset($demande['id_section']) ? $demande['id_section'] : 0;
	$id_section_parent  = isset($demande['id_section_parent']) ? $demande['id_section_parent'] : 0;
	$id_location        = isset($demande['id_location']) ? $demande['id_location'] : 0;
	$typdoc             = isset($demande['type_document']) ? $demande['type_document'] : '';
	$look               = isset($demande['look']) ? $demande['look'] : array();

	if ($id_section_parent) {
		$id_location = $id_section_parent; // a n'y rien comprendre...
	}

	$recherche = str_replace("+"," ",$recherche);

	if (!$look) $look = array();
	$look = array_flip($look);
	$tous = true;


	// si tout coche
	if (isset($look['ALL'])) {
		$tous = true;
	// ou tout pas coche mais autre chose de coche...
	} elseif (count($look)) {
		$tous = false;
	}
	
	if ($recherche=='*') {
		$recherche='';
	}
	
	$searchType = 0;
	$ids = array();
	$debut;   // indice du premier resultat
	$nombre;  // nombre de resultats

	// types de recherches
	$types = array(
		'TITLE' => 1,
		'AUTHOR' => 2,
		'PUBLISHER' => 3,
		'COLLECTION' => 4,
		'CATEGORY' => 6,
	);
	// je reproduis a peu pres tel que le code d'avant
	// mais c'est assez etrange que si plusieurs choses
	// sont cochees, c'est le dernier type qui est pris
	// (le plus eleve)
	foreach ($types as $nom=>$index) {
		if (isset($look[$nom]) and $look[$nom]) {
			$searchType = $index;
		}
	}

	// ajout des criteres de recherche.
	if ($recherche) {
		$search[] = array("inter"=>"or", "field"=>42, "operator"=>"BOOLEAN", "value"=>$recherche);

		$look_correspondances = array(
			'TITLE' => 1,
			'AUTHOR' => 2,
			'PUBLISHER' => 3,
			'COLLECTION' => 4,
			'ABSTRACT' => 10,
			'CATEGORY' => 11,
			'INDEXINT' => 12,
			'KEYWORDS' => 13,
		);
		foreach ($look_correspondances as $nom=>$id) {
			if (isset($look[$nom]) and $look[$nom]) {
				$search[] = array("inter"=>"or", "field"=>$id, "operator"=>"BOOLEAN", "value"=>$recherche);
			}
		}
	}

	if ($typdoc)		$search[] = array("inter"=>"and", "field"=>15, "operator"=>"EQ", "value"=>$typdoc);
	if ($id_section)	$search[] = array("inter"=>"and", "field"=>17, "operator"=>"EQ", "value"=>$id_section);
	if ($id_location)	$search[] = array("inter"=>"and", "field"=>16, "operator"=>"EQ", "value"=>$id_location);

	try {
		$ws = pmb_webservice();

		// demande de recherche...
		if ($tous and !$id_section and !$id_location) {
			$r = $ws->pmbesOPACAnonymous_simpleSearch($searchType,$recherche);
/* 
		} else if (($tous AND $id_section AND !$typdoc)){
			$r=$ws->pmbesSearch_simpleSearchLocalise($searchType,$recherche,$id_location,$id_section);
*/
		} else {
			$r=$ws->pmbesOPACAnonymous_advancedSearch($search);
		}

		// on obtient une cle specifique a PMB avec le nombre de resultats... c'est tout.
		$searchId=$r->searchId;
		$nbTotal = $r->nbResults;

		// pas de nombre... pas la peine de continuer !
		if ($nbTotal) {
			// le critere de tri semble dependre de ce qui a ete defini dans PMB dans une table 'tris'
			// l'inconvenient ici, c'est que PMB retourne tous les champs
			// et pas uniquement l'identifiant, ce qui est inutile et charge la connexion
			// et le travail de PMB, mais il n'y a pas de moyen a cette heure ci pour ne demmander que les ids.
			$r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$nombre,"utf-8");
			if (is_array($r)) {
				foreach ($r as $n) {
					$ids[] = $n->id;
				}
			}
			// la, on fait un truc rigolo pour SPIP...
			// on remplit de 0 avant le debut, et apres la fin de la pagination
			// jusqu'au nombre de resultats, pour faire croire que tous les elements
			// sont la.
			// 15 resultats, debut = 5, nb = 5
			// 0 0 0 0 0 2 4 5 12 18 0 0 0 0
			if ($pagination) {
				$vide = array_fill(0, $nbTotal, 0); // que des 0
				array_splice($vide, $debut, $nombre, $ids);
				$ids = $vide;
			}
		}
	} catch (Exception $e) {
		echo 'Exception reçue (8) : ',  $e->getMessage(), "\n";
	}

	return $ids;
}



/**
 * Retourne la liste de toutes les options de recherche
 * (il semblerait !)
**/
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
	

	include_spip('inc/unimarc');

	$tresultat = Array();
	$id_notice = $value->id;

	if (isset($value->f) && is_array($value->f)) {
		foreach($value->f as $informations) {
			#echo "\n<pre>"; print_r($informations); echo "</pre>";
			if ($res = pmb_parse_unimarc($informations)) {
				foreach ($res as $r) {
					$cle = $r['cle'];
					
					// pour chaque variable retournee, on la stocke
					// dans le tableau de resultat.
					// il se peut qu'il y ait des traitements a effectuer
					// tel que merger des champs s'ils existaient deja.
					
					if (isset($r['params']) AND isset($r['params']['@post_traitements'])) {
						$tr = $r['params']['@post_traitements'];
						
						// il n'existait aucune valeur avant ?
						if (!isset($tresultat[$cle]) or !$tresultat[$cle]) {
							$tresultat[$cle] = $r['valeur'];
						} else {
							// inter : equivalent de {', '} en spip
							// placer_avant : place le resultat avant ou apres l'ancien.
							$before = isset($tr['placer_avant']) and $tr['placer_avant'];
							$inter = isset($tr['inter']) ? $tr['inter'] : '';
							
							if ($inter) {
								if ($before) {
									$tresultat[$cle] = $r['valeur'] . $inter . $tresultat[$cle] ;
								} else {
									$tresultat[$cle] .= $inter . $r['valeur'];
								}
							}
						}
					} else {
						$tresultat[$cle] = $r['valeur'];
					}
				}
			} else {
				#echo "\n<pre>"; print_r($informations); echo "</pre>";
			}
		}
	}

	// si pas d'auteur, on prend le responsable
	if (empty($tresultat['lesauteurs'])) {
		$tresultat['lesauteurs'] = isset($tresultat['auteur']) ? $tresultat['auteur'] : '';
	}

	// tous les auteurs et tous les liens d'auteurs
	// pour se simplifier un peu...
	foreach (array('lesauteurs', 'liensauteurs') as $quoi) {
		$tous = array();
		foreach (
			array(
				$quoi.'_personne'    , $quoi.'_personne2'    , $quoi.'_personne3', 
				$quoi.'_collectivite', $quoi.'_collectivite2', $quoi.'_collectivite3', 
		) as $a) {
			if (isset($tresultat[$a]) and $tresultat[$a]) {
				$tous[] = $tresultat[$a];
			}
		}
		$tresultat['tous'.$quoi] = implode(', ', $tous);
	}


	// si pas de logo, mais isbn, on tente une demande a amazon
	if (!isset($tresultat['source_logo']) OR !$tresultat['source_logo']) {
		if (isset($tresultat['isbn']) and $tresultat['isbn']) {
			// je me demande si un copie local de l'url d'amazon irait pas plus vite directement...
			$tresultat['source_logo'] =
				rtrim(lire_config("spip_pmb/url","http://tence.bibli.fr/opac"),'/')
				 . "/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F!!isbn!!.08.MZZZZZZZ.jpg&noticecode="
				 . str_replace("-","",$tresultat['isbn']);
		}
	}

	$tresultat['id'] = $tresultat['id_notice'] = $id_notice;

	// on stocke en cache
	$resultats[$hash] = $tresultat;
	return $tresultat;
}



/**
 * Sauve ou restaure un cache d'information
 * sur la base [$type_cache][$id] = valeur
 *
 * @param string $type
 * 		Le type de cache (notices, auteurs, ...)
 * @param int|array $id
 * 		L'identifiant desire ou une liste d'identifiants desires
 * @param mixed $valeur
 * 		La valeur a stocker si stockage demande
 * @param bool $set
 * 		Est-ce un stockage demande ?
 * @return mixed
 * 		Si stockage : true
 * 		Sinon,
 * 		si $id simple, sa $valeur enregistree
 * 		si $id tableau, retourne array($res, $wanted),
 * 		2 tableaux contenants les resultats trouves et ceux non trouves
 * 		en conservant les cles du tableau $id en entree.
**/
function pmb_cacher($type, $id, $valeur=null, $set=false) {
	static $cache = array();
	if (!isset($cache[$type])) {
		// Par ailleurs, l'id 0, s'il se produit est incongru est
		// provient du hack pour la pagination de ce plugin.
		$cache[$type] = array(0 => array());
	}
	
	// mise en cache
	if ($set) {
		$cache[$type][$id] = $valeur;
		return true;
	}
	
	// lecture du cache
	// 1 seul element demande
	if (!is_array($id)) {
		return $cache[$type][$id];
	}
	
	// n elements demandes...
	// id est un tableau d'id
	// on retourne un tableau $res et $wanted
	// contenant ce qu'on a et ce qu'on n'a pas.
	$wanted = $id;
	// ce qu'on a trouve...
	$res = array();
	
	foreach ($id as $c=>$l) {
		if (isset($cache[$type][$l])) {
			$res[$c] = $cache[$type][$l];
			unset($wanted[$c]);
		}
	}
	return array($res, $wanted);
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
			include_spip('inc/jsonRPCClient');
			$ws = new jsonRPCClient(lire_config("spip_pmb/jsonrpc", ""), false);
			$ws->setAuthenticationType("http");// quelles sont les autres methodes...
			$ws->setCredentials(lire_config("spip_pmb/jsonrpc_user", ""),lire_config("spip_pmb/jsonrpc_pw", ""));
		}
	}
	catch (Exception $e) {
		echo 'Exception reçue (15) : ',  $e->getMessage(), "\n";
	} 

	return $ws;
}


/**
 * Retourne un tableau contenant la liste des tris possibles
 * (non utilisee)
**/
function pmb_ws_liste_tri_recherche() {
	//
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


/**
 * Reserve pour un auteur identifie a pmb une notice
 *
**/
function pmb_reserver_ouvrage($session_id, $notice_id, $bulletin_id, $location) {

	$result= Array();

	$ws = pmb_webservice();
	$result = $ws->pmbesOPACEmpr_add_resa($session_id, $notice_id, $bulletin_id, $location);
	if (!$result->status) {
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


/**
 * Nettoyer des caracteres etranges
 * qui proviennent des requetes de PMB... 
 *
 * @param string $valeur
 * 		Chaine a nettoyer
 * @return string
 * 		Chaine nettoyee
**/
function pmb_nettoyer_caracteres($valeur) {
	$valeur = stripslashes($valeur);
	$valeur = str_replace(
		array("", "", "", "",  ""),
		array("'", "&oelig;", "\"", "\"", "&euro;"), $valeur);
	return $valeur;
}

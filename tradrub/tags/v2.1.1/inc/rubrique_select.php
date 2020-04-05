<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser'); // necessaire si appel de l'espace public

// Recupere les donnees d'un article pour composer un formulaire d'edition
// (utilise par formulaire_editer_objet_charger)
// id_rubrique = numero de rubrique existant
// id_parent = ou veut-on l'installer (pas obligatoire)
// lier_trad = l'associer a la rubrique au numero $lier_trad
// new=oui = article a creer si on valide le formulaire
// 
function inc_rubrique_select_dist($id_rubrique, $id_parent=0, $lier_trad=0) {
	global $connect_id_rubrique, $spip_lang; 

	if (is_numeric($id_rubrique)) {
		$row = sql_fetsel("*", "spip_rubriques", "id_rubrique=". sql_quote($id_rubrique));
		return $row;
	}

	// id_rubrique non numerique, c'est une demande de creation.
	// Si c'est une demande de nouvelle traduction, init specifique
	if ($lier_trad){
		$row = rubrique_select_trad($lier_trad, $id_parent);
		$row['statut'] = ''; // le nouvel article n'a pas encore de statut !
	}
	else {
		$row['titre'] = '';//filtrer_entites(_T('info_nouvel_article'));
		//$row['onfocus'] = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		$row['id_parent'] = $id_parent;
	}

	// appel du script a la racine, faut choisir 
	// admin restreint ==> sa premiere rubrique
	// autre ==> la derniere rubrique cree
	if (!$row['id_parent']) {
		if ($connect_id_rubrique)
			$row['id_parent'] = $id_parent = $connect_id_rubrique[0]; 
		else {
			$row_rub = sql_fetsel("id_parent", "spip_rubriques", "", "", "id_rubrique DESC", 1);
			$row['id_parent'] = $id_parent = $row_rub['id_parent'];
		}
		if (!autoriser('creerrubriquedans', 'rubrique', $row['id_parent'] )){
			// manque de chance, la rubrique n'est pas autorisee, on cherche un des secteurs autorises
			$res = sql_select("id_rubrique", "spip_rubriques", "id_parent=0");
			while (!autoriser('creerrubriquedans', 'rubrique', $row['id_parent'] ) && $row_rub = sql_fetch($res)){
				$row['id_parent'] = $id_parent = $row_rub['id_rubrique'];
			}
		}
	}

	// recuperer le secteur, pour affecter les bons champs extras
	if (!$row['id_secteur']) {
		$row_rub = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . sql_quote($id_parent));
		$row['id_secteur'] = $row_rub;
	}

	return $row;
}

//
// Si un article est demande en creation (new=oui) avec un lien de trad,
// on initialise les donnees de maniere specifique
//
function rubrique_select_trad($lier_trad, $id_parent=0) {
	// Recuperer les donnees de l'article original
	$row = sql_fetsel("*", "spip_rubriques", "id_rubrique=" . sql_quote($lier_trad));
	if ($row) {
		$row['titre'] = filtrer_entites(_T('info_nouvelle_traduction')).' '.$row["titre"];

	} else $row = array();
	
	if ($id_parent) {
		$row['id_parent'] = $id_parent;
		return $row;
	}
	
	$id_parent = $row['id_parent'];
	// Regler la langue, si possible, sur celle du redacteur
	// Cela implique souvent de choisir une rubrique ou un secteur
	if (in_array($GLOBALS['spip_lang'],
	explode(',', $GLOBALS['meta']['langues_multilingue']))) {
		// langue changeante par rubrique
		if ($GLOBALS['meta']['multi_rubriques'] == 'oui') {
			// Sinon, chercher la rubrique la plus adaptee pour
			// accueillir l'article dans la langue du traducteur
			if ($GLOBALS['meta']['multi_secteurs'] == 'oui') {
				$id_parent_parent = 0;
			} else {
				// on cherche une rubrique soeur dans la bonne langue
				$row_rub = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=$id_parent");
				$id_parent_parent = $row_rub['id_parent'];
			}
			$row_rub = sql_fetsel("id_rubrique", "spip_rubriques", "lang='".$GLOBALS['spip_lang']."' AND id_parent=" . sql_quote($id_parent_parent));
			if ($row_rub)
				$row['id_parent'] = $row_rub['id_rubrique'];
		}
	}
	return $row;
}

?>

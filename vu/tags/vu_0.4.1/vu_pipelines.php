<?php


function vu_header_prive($flux){
	$flux = vu_insert_head($flux);
	return $flux;
}

// Pipeline. Pour l'entete des pages de l'espace prive
function vu_insert_head($flux)
{
	// Insertion dans l'entete des pages 'vu' d'un appel la feuille de style dediee
	$flux .= "<link rel='stylesheet' href='"._DIR_VU_PRIVE."vu_style_prive.css' type='text/css' media='all' />\n";

	return $flux;
}

// Pipeline. Pour ajouter du contenu aux formulaires CVT du core.
function vu_editer_contenu_objet($flux){
	// Concernant le formulaire CVT 'editer_groupe_mot', on veut faire apparaitre les nouveaux objets
	if ($flux['args']['type']=='groupe_mot') {
		// Si le formulaire concerne les groupes de mots-cles, alors recupere le resultat
		// de la compilation du squelette 'inc-groupe-mot-vu.html' qui contient les lignes
		// a ajouter au formulaire CVT,
		$vu_gp_objets = recuperer_fond('formulaires/inc-groupe-mot-vu', $flux['args']['contexte']);
		// que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
		$flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $vu_gp_objets."\n".'$1', $flux['data']);
	}
	return $flux;
}

// Pipeline. Pour associer un libelle (etiquette) aux types d'objets.
// Dans la page listant tous les groupes de mots (exec/mots_tous),
// il est indique pour chacun d'entre eux les objets sur lesquels 
// ils s'appliquent (ex : '> Articles'). Pour que cela fonctionne,
// il est necessaire que ces objets aient un libelle, au risque sinon
// d'afficher '> info_table'. 
function vu_libelle_association_mots($flux){
	// On recupere le flux, ici le tableau des libelles,
	// et on ajoute nos trois objets.
	$flux['vu_annonces'] = 'vu:info_vu_libelle_annonce';
	$flux['vu_evenements'] = 'vu:info_vu_libelle_evenement';
	$flux['vu_publications'] = 'vu:info_vu_libelle_publication';

	return $flux;
}

// Pipeline. Pour permettre la recherche dans les nouveaux objets
function vu_rechercher_liste_des_champs($tables){
	// Prendre en compte les champs des annonces
	$tables['vu_annonce']['titre'] = 3;
	$tables['vu_annonce']['annonceur'] = 3;
	$tables['vu_annonce']['type'] = 3;
	$tables['vu_annonce']['descriptif'] = 3;
	$tables['vu_annonce']['source_nom'] = 3;
	// Prendre en compte les champs des evenements
	$tables['vu_evenement']['titre'] = 3;
	$tables['vu_evenement']['organisateur'] = 3;
	$tables['vu_evenement']['lieu_evenement'] = 3;
	$tables['vu_evenement']['type'] = 3;
	$tables['vu_evenement']['descriptif'] = 3;
	$tables['vu_evenement']['source_nom'] = 3;
	// Prendre en compte les champs des publications
	$tables['vu_publication']['titre'] = 3;
	$tables['vu_publication']['auteur'] = 3;
	$tables['vu_publication']['editeur'] = 3;
	$tables['vu_publication']['type'] = 3;
	$tables['vu_publication']['descriptif'] = 3;
	$tables['vu_publication']['source_nom'] = 3;

	return $tables;
}

//Pipeline. Compatibilite avec le plugin ChampsExtra2
function vu_objets_extensibles($objets){
        return array_merge($objets, array(
		'vu_annonce' => _T('vu:info_vu_libelle_annonce'), 
		'vu_evenement' => _T('vu:info_vu_libelle_evenement'), 
		'vu_publication' => _T('vu:info_vu_libelle_publication')
	));
}

//Pipeline. Affiche les éléments de veille en attente de validation
function vu_accueil_encours($res){

	$cpt = sql_countsel("spip_vu_annonces", "statut='prop'");
	if ($cpt) {
		$res .= afficher_objets('annonce',afficher_plus(generer_url_ecrire('veille_tous'))._T('vu:titre_enattente_annonces'), array("FROM" => 'spip_vu_annonces AS annonces', 'WHERE' => "statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	}
	
	$cpt = sql_countsel("spip_vu_evenements", "statut='prop'");
	if ($cpt) {
		$res .= afficher_objets('evenement',afficher_plus(generer_url_ecrire('veille_tous'))._T('vu:titre_enattente_evenements'), array("FROM" => 'spip_vu_evenements AS evenements', 'WHERE' => "statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	}

	$cpt = sql_countsel("spip_vu_publications", "statut='prop'");
	if ($cpt) {
		$res .= afficher_objets('publication',afficher_plus(generer_url_ecrire('veille_tous'))._T('vu:titre_enattente_publications'), array("FROM" => 'spip_vu_publications AS publications', 'WHERE' => "statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	}

	return $res;

}


//Pipeline. Faire apparaitre les nouveaux objets dans le cartouche d'information
//de la page d'accueil (?exec=accueil).
//Reprise integrale du code de /ecrire/exec/accueil.php
function vu_accueil_informations($res){

	global $spip_display, $spip_lang_left, $connect_id_rubrique;

	// On ouvre le style css
	$res .= "<div class='verdana1' style='border-top: 1px gray solid; padding-top:10px; margin-top:10px;'>";	

	// Concernant les annonces
	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_vu_annonces', '', 'statut', '','', "COUNT(*)<>0");
	
	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}
 
	if ($cpt) {
		if ($where) {
			$q = sql_select("COUNT(*) AS cnt, statut", 'spip_vu_annonces', $where, "statut");
			while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}
		$res .= afficher_plus(generer_url_ecrire("veille_tous",""))."<b>"._T('vu:titre_cartouche_accueil_annonces')."</b>";
		$res .= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if (isset($cpt['prop'])) $res .= "<li>"._T("texte_statut_attente_validation").": ".$cpt2['prop'].$cpt['prop'] . '</li>';
		if (isset($cpt['publie'])) $res .= "<li><b>"._T("texte_statut_publies").": ".$cpt2['publie'] .$cpt['publie'] . "</b>" .'</li>';
		$res .= "</ul>";
	}

	// Concernant les evenements
	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_vu_evenements', '', 'statut', '','', "COUNT(*)<>0");
	
	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}
 
	if ($cpt) {
		if ($where) {
			$q = sql_select("COUNT(*) AS cnt, statut", 'spip_vu_evenements', $where, "statut");
			while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}
		$res .= afficher_plus(generer_url_ecrire("veille_tous",""))."<b>"._T('vu:titre_cartouche_accueil_evenements')."</b>";
		$res .= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if (isset($cpt['prop'])) $res .= "<li>"._T("texte_statut_attente_validation").": ".$cpt2['prop'].$cpt['prop'] . '</li>';
		if (isset($cpt['publie'])) $res .= "<li><b>"._T("texte_statut_publies").": ".$cpt2['publie'] .$cpt['publie'] . "</b>" .'</li>';
		$res .= "</ul>";
	}

	// Concernant les publications
	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_vu_publications', '', 'statut', '','', "COUNT(*)<>0");
	
	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}
 
	if ($cpt) {
		if ($where) {
			$q = sql_select("COUNT(*) AS cnt, statut", 'spip_vu_publications', $where, "statut");
			while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}
		$res .= afficher_plus(generer_url_ecrire("veille_tous",""))."<b>"._T('vu:titre_cartouche_accueil_publications')."</b>";
		$res .= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if (isset($cpt['prop'])) $res .= "<li>"._T("texte_statut_attente_validation").": ".$cpt2['prop'].$cpt['prop'] . '</li>';
		if (isset($cpt['publie'])) $res .= "<li><b>"._T("texte_statut_publies").": ".$cpt2['publie'] .$cpt['publie'] . "</b>" .'</li>';
		$res .= "</ul>";
	}

	// On ferme le style
	$res .= "</div>";
	
	// Et on envoie enfin dans le pipeline !
	return $res;
}

//Pipeline. Faire apparaitre les nouveaux objets avec le récapitulatif des mots clés 
//de la page d'accueil (?exec=mots_tous).
//Reprise integrale du code de /ecrire/exec/accueil.php

?>

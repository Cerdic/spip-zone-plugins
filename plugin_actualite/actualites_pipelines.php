<?php


function actualites_header_prive($flux){
	$flux = actualites_insert_head($flux);
	return $flux;
}

// Pipeline. Pour l'entete des pages de l'espace prive
function actualites_insert_head($flux)
{
	// Insertion dans l'entete des pages 'actualites' d'un appel la feuille de style dediee
	$flux .= "<link rel='stylesheet' href='"._DIR_ACTUALITES_PRIVE."actualites_style_prive.css' type='text/css' media='all' />\n";
	return $flux;
}

// Pipeline. Pour ajouter du contenu aux formulaires CVT du core.
function actualites_editer_contenu_objet($flux){
	// Concernant le formulaire CVT 'editer_groupe_mot', on veut faire apparaitre les nouveaux objets
	if ($flux['args']['type']=='groupe_mot') {
		// Si le formulaire concerne les groupes de mots-cles, alors recupere le resultat
		// de la compilation du squelette 'inc-groupe-mot-actualites.html' qui contient les lignes
		// a ajouter au formulaire CVT,
		$actualites_gp_objets = recuperer_fond('formulaires/inc-groupe-mot-actualites', $flux['args']['contexte']);
		// que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
		$flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $actualites_gp_objets."\n".'$1', $flux['data']);
	}
	return $flux;
}

// Pipeline. Pour associer un libelle (etiquette) aux types d'objets.
// Dans la page listant tous les groupes de mots (exec/mots_tous),
// il est indique pour chacun d'entre eux les objets sur lesquels 
// ils s'appliquent (ex : '> Articles'). Pour que cela fonctionne,
// il est necessaire que ces objets aient un libelle, au risque sinon
// d'afficher '> info_table'. 
function actualites_libelle_association_mots($flux){
	// On recupere le flux, ici le tableau des libelles,
	// et on ajoute nos trois objets.
	$flux['actualites'] = 'actualites:info_vu_libelle_actualite';

	return $flux;
}

// Pipeline. Pour permettre la recherche dans les nouveaux objets
function actualites_rechercher_liste_des_champs($tables){
	// Prendre en compte les champs des actualites
	$tables['actualite']['titre'] = 3;

	return $tables;
}

//Pipeline. Compatibilite avec le plugin ChampsExtra2
function actualites_objets_extensibles($objets){
        return array_merge($objets, array(
		'actualite' => _T('actualites:info_vu_libelle_actualite')
	));
}

//Pipeline. Affiche les éléments de actualites en attente de validation
function actualites_accueil_encours($res){

	$cpt = sql_countsel("spip_actualites", "statut='prop'");
	if ($cpt) {
		$res .= afficher_objets('actualite',afficher_plus(generer_url_ecrire('actualites_tous'))._T('actualites:titre_enattente_actualites'), array("FROM" => 'spip_actualites AS actualites', 'WHERE' => "statut='prop'", 'ORDER BY' => "date DESC"),'',true);
	}

	return $res;

}


//Pipeline. Faire apparaitre les nouveaux objets dans le cartouche d'information
//de la page d'accueil (?exec=accueil).
//Reprise integrale du code de /ecrire/exec/accueil.php
function actualites_accueil_informations($res){

	global $spip_display, $spip_lang_left, $connect_id_rubrique;

	// On ouvre le style css
	$res .= "<div class='verdana1' style='border-top: 1px gray solid; padding-top:10px; margin-top:10px;'>";	

	// Concernant les annonces
	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_actualites', '', 'statut', '','', "COUNT(*)<>0");
	
	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}
 
	if ($cpt) {
		if ($where) {
			$q = sql_select("COUNT(*) AS cnt, statut", 'spip_actualites', $where, "statut");
			while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}
		$res .= afficher_plus(generer_url_ecrire("actualites_tous",""))."<b>"._T('actualites:titre_cartouche_accueil_actualites')."</b>";
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

function actualites_affiche_enfants($flux){

	switch($flux['args']['exec']){
		case "naviguer":
			if($flux['args']['id_rubrique']!=""){
						
				$id_rubrique=$flux['args']['id_rubrique'];
							
				$flux['data'].= icone_inline(_T('actualites:icone_creer_objet'), generer_url_ecrire("actualites_edit","type=actualite&new=oui"), _DIR_ACTUALITES_IMG_PACK."actualite-24.gif", "creer.gif","right");
				$flux['data'].="<div class='nettoyeur'></div>";

				// Passage en variable des tableaux contenant pour les listes d'objet (simplification de l'écriture)
				$requete=array(
					"SELECT"=> "o.id_actualite as id_actualite,o.titre,o.statut,'actualites' as objet",
					"FROM"=> "spip_actualites o,spip_actualites_liens ol",
					"WHERE"=> "o.id_actualite=ol.id_actualite AND ol.objet='rubrique' AND ol.id_objet='".$id_rubrique."'",
					"ORDERBY"=>"o.id_actualite DESC"
					);
				$liste_des_actualites = afficher_objets('actualite',_T('actualites:liste_actualites'), $requete,'',true);
				if ( (function_exists('lire_config')) && (lire_config('actualites/objet_actualite') == "off") )
					$liste_des_actualites = "";
				$flux['data'].= $liste_des_actualites;	
				
			}
		break;
		/*
		case "articles":
			if($flux['args']['id_article']!=""){
						
				$id_article=$flux['args']['id_article'];
				$presenter_liste=charger_fonction('presenter_liste','inc');
								
				$objets_installes=liste_objets_meta();
				foreach ($objets_installes as $objet) {
					$nom_objet=objets_nom_objet($objet);
					$flux['data'].= icone_inline(_T('objets:icone_creer_objet')." : ".$nom_objet, generer_url_ecrire("objet_edit","objet=".$objet."&new=oui&retour=articles&id_article=$id_article&type=article"), objets_vignette_objet($objet,"24","gif"), "creer.gif","right");
					$flux['data'].="<div class='nettoyeur'></div>";
					$requete=array(
						"SELECT"=> "o.id_".$nom_objet." as id_objet,o.titre,o.statut,'".$objet."' as objet", 
						"FROM"=> "spip_".$objet." o,spip_".$objet."_liens ol",
						"WHERE"=> "o.id_".$nom_objet."=ol.id_".$nom_objet." AND ol.objet='article' AND ol.id_objet='".$id_article."'", 
						"ORDERBY"=>"o.id_".$nom_objet." DESC"
						);
					$les_objets='id_'.$nom_objet;
					$styles=array();
					$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);					
					$flux['data'].=$presenter_liste($requete,'presenter_objet_boucle',$les_objets,true,false,$styles,$tmp_var,_T('objets:titre_liste').$nom_objet,objets_vignette_objet($objet,"24","gif"));					
				}
			}
		break;	
		*/
	}
	return $flux;
}

/* Gestion de l'affichage des documents */
//$GLOBALS['medias_exec_colonne_document'][] = 'actualites_edit';
//TODO parser sur tous les champs extra ceux qui ont un traitement propre pour les ajouter ici
//$GLOBALS['medias_liste_champs'][] = 'descriptif';

//function objets_post_edition($flux){}

$GLOBALS['medias_exec_colonne_document'][] = 'actualites_edit';

function actualites_affiche_gauche($flux){
	if($flux['args']['exec']=='actualites_edit'){

		$id_actualite = $flux['args']['id_actualite'];
		
		if($id_actualite) {
			$objet= 'actualites';
			$nom_objet= 'actualite';
			$GLOBALS['logo_libelles']['id_'.$nom_objet]= _T('actualites:titre_logo');
			$iconifier = charger_fonction('iconifier', 'inc'); 
			
			$contexte = array(
				'id_objet'=>$flux['args']['id_actualite'],
				'objet'=>$objet,
				'nom_objet'=>$nom_objet,
				'retour'=>generer_url_ecrire("actualites_edit","id_actualite=".$id_actualite."&retour=nav")
			);
			
			$flux['data'].= recuperer_fond("prive/navigation/logo_objets",$contexte);
		}
	}
	return $flux;
}

/**
 * 
 * Insertion dans le pipeline declarer_url_objets
 * Permet d'avoir des url propres de actualites avec un actualite.html et un #URL_ACTUALITE (SPIP 2.1)
 * 
 * @param object $array
 * @return 
 */
function actualites_declarer_url_objets($array){
	$array[] = 'actualite';
	return $array;
}

?>

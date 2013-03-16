<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/simplecal_utils');



// Pipeline. Entete des pages de l'espace prive
function simplecal_header_prive($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_SIMPLECAL_PRIVE.'simplecal_style_prive.css" />';
    return $flux;
}

// Pipeline. Entete des pages de l'espace public
function simplecal_insert_head_css($flux) {
	// Thèmes basé sur : http://jqueryui.com/themeroller/
    $theme_public = $GLOBALS['meta']['simplecal_themepublic'];
    $flux .= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_SIMPLECAL.'css/datepicker/'.$theme_public.'.css" />';
    return $flux;
}


// Pipeline : elements 'en cours' de la page d'accueil
function simplecal_accueil_encours($flux) {
	$lister_objets = charger_fonction('lister_objets','inc');

	$flux .= $lister_objets('evenements', array(
		'titre'=>afficher_plus_info(generer_url_ecrire('evenements', 'mode=avenir'))._T('simplecal:info_evenements_valider'),
		'statut'=>array('prop'),
		'par'=>'date'));

	return $flux;
}


// Pipeline : elements 'en cours' d'une rubrique
function simplecal_rubrique_encours($flux) {
	if ($flux['args']['type'] == 'rubrique') {
		$lister_objets = charger_fonction('lister_objets','inc');

		$id_rubrique = $flux['args']['id_objet'];

		$flux['data'] .= $lister_objets('evenements', array(
			'titre'=>_T('simplecal:info_evenements_valider'),
			'statut'=>array('prop'),
			'id_rubrique'=>$id_rubrique,
			'par'=>'date'));
	}
	return $flux;
}

// Pipeline : les evenements references au niveau d'une rubrique
function simplecal_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec']) AND $e['type'] == 'rubrique' AND $e['edition'] == false) {
		$id_rubrique = $flux['args']['id_rubrique'];
		$affiche = false;
		
		$config_rubrique = $GLOBALS['meta']['simplecal_rubrique'];
		// affichage si config = 'partout' ou 'secteur'
		if ($config_rubrique != 'non'){
			$affiche = true;
			// si config = 'secteur', on verifie que la rubrique est un secteur
			if ($config_rubrique == 'secteur'){
				$row_tmp = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=$id_rubrique");
				$id_parent = intval($row_tmp['id_parent']);
				if ($id_parent == 0){
					$affiche = true; // car secteur
				} else {
					$affiche = false; // car pas un secteur
				}                
			}
		}
		
		if ($affiche) {
			$lister_objets = charger_fonction('lister_objets','inc');
			$bouton_evenements = '';
			$id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique='.$id_rubrique);
			if (autoriser('creerevenementdans','rubrique',$id_rubrique,NULL,array('id_parent'=>$id_parent))) {
				$bouton_evenements .= icone_verticale(_T('simplecal:icone_nouvel_evenement'), generer_url_ecrire("evenement_edit","id_rubrique=$id_rubrique&new=oui"), "evenement-24.png","new", 'right')
				. "<br class='nettoyeur' />";
			}
			
			$flux['data'] .= $lister_objets('evenements', array(
				'titre'=>_T('simplecal:titre_contenu_rubrique'), 
				'where'=>"statut != 'prop' AND statut != 'prepa'", 
				'id_rubrique'=>$id_rubrique, 
				'par'=>'date')
			);
			$flux['data'] .= $bouton_evenements;
		}
	}
	return $flux;
}

// Pipeline : synthese des elements 'publies' de la page d'accueil
function simplecal_accueil_informations($texte) {
	$texte .= recuperer_fond('prive/squelettes/inclure/evenement-accueil-information', array());
	return $texte;
}

// Zone de contenu
function simplecal_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	
	// Page de configuration
	if ($exec == "configurer_contenu") {
		$flux["data"] .=  recuperer_fond('prive/squelettes/inclure/configurer',array('configurer'=>'configurer_evenements'));
	}
	
	return $flux;
}


// OK SPIP3
function simplecal_affiche_auteurs_interventions($flux){
	$id_auteur = intval($flux['args']['id_auteur']);
	
	$lister_objets = charger_fonction('lister_objets','inc');
	$listing = $lister_objets('evenements', array(
		'titre'=>afficher_plus_info(generer_url_ecrire('evenements', 'mode=avenir'))._T('simplecal:liste_evenements_auteur'),
		'id_auteur'=>$id_auteur,
		'par'=>'date'));

	
	$flux['data'] .= $listing;
	return $flux;
}


/**
 * Afficher le nombre d'evenements de l'auteur ou de la rubrique
 *
 */
function simplecal_boite_infos($flux){
	$type = $flux['args']['type'];
	$id = intval($flux['args']['id']);
	
	if ($type == 'auteur'){
		$n_evt = sql_countsel("spip_auteurs_liens", "id_auteur=".$id." and objet='evenement'");
	} 
	if ($type == 'rubrique'){
		$n_evt = sql_countsel("spip_evenements", "statut='publie' and id_rubrique=".$id);
	}
	
	if (in_array($type, array("auteur", "rubrique"))){
		if (($pos = strpos($flux['data'],'<!--nb_elements-->'))!==FALSE) {
			if ($n_evt > 0){
				$aff = '<div>'.singulier_ou_pluriel($n_evt, 'simplecal:info_1_evenement', 'simplecal:info_nb_evenements').'</div>';
			}
			$flux['data'] = substr($flux['data'],0,$pos).$aff.substr($flux['data'],$pos);
		}
	}
	
	return $flux;
}


function simplecal_configurer_liste_metas($metas) {
	$metas['simplecal_autorisation_redac'] = 'non'; // [oui, non]
	$metas['simplecal_rubrique'] = 'non'; // [non, secteur, partout]
	$metas['simplecal_refobj'] = 'non';   // [oui, non]
	$metas['simplecal_descriptif'] = 'oui';   // [oui, non]
	$metas['simplecal_texte'] = 'oui';  // [oui, non]
	$metas['simplecal_lieu'] = 'oui';   // [oui, non]
	$metas['simplecal_lien'] = 'non';   // [oui, non]
	$metas['simplecal_themepublic'] = 'base';
	return $metas;
}

function simplecal_affiche_gauche($flux) {
	$exec =  $flux['args']['exec'];
	
	// On se trouve sur un article
	if ($exec == 'article') {
		if ($GLOBALS['meta']['simplecal_refobj'] == 'oui'){
			$id_article = intval($flux['args']['id_article']);
			$contexte = array(
				'type' => 'article',
				'id_objet'=>$id_article
			);
			$portlet = recuperer_fond('prive/squelettes/inclure/portlet_refobj', $contexte);
			$flux['data'] .= $portlet;
		}
	}
	
	// On se trouve sur une breve
	if ($exec == 'breve'){ 
		if ($GLOBALS['meta']['simplecal_refobj'] == 'oui'){
			$id_breve = intval($flux['args']['id_breve']);
			$contexte = array(
				'type' => 'breve',
				'id_objet'=>$id_breve
			);
			$portlet = recuperer_fond('prive/squelettes/inclure/portlet_refobj', $contexte);
			$flux['data'] .= $portlet;
		}
	}
	
	return $flux;
}


// Liste des contributions d'un auteur (bloc auteur)
function simplecal_compter_contributions_auteur($flux){
	$id_auteur = intval($flux['args']['id_auteur']);
	if ($cpt = sql_countsel("spip_auteurs_liens AS lien", "lien.objet='evenement' and lien.id_auteur=".intval($flux['args']['id_auteur']))){
		$contributions = singulier_ou_pluriel($cpt,'simplecal:info_1_evenement','simplecal:info_nb_evenements');
		$flux['data'][] = $contributions;
	}
	return $flux;
}


// Definir le squelette evenement.html pour les urls de type spip.php?evenement123
// http://programmer.spip.org/declarer_url_objets
/*function simplecal_declarer_url_objets($array){
	$array[] = 'evenement';
	return $array;
}*/


// cf. urls/propres.php
function simplecal_propres_creer_chaine_url($flux){
	/*
	$flux = Array ( 
		[data] => evenement2 
		[objet] => Array ( 
			[url] => evenement2 
			[date] => 2010-07-25 22:53:04 
			[date_debut] => 2010-05-09 00:00:00 
			[date_fin] => 2010-05-10 00:00:00 
			[lieu] => 
			{titre] =>
			[lang] => 
			[type] => evenement 
			[id_objet] => 2 ) 
	) 
	*/
	
	$type = $flux['objet']['type'];
	if ($type == 'evenement'){
		$date_debut = $flux['objet']['date_debut'];
		$titre = substr($date_debut, 8, 2)."-".substr($date_debut, 5, 2)."-".substr($date_debut, 0, 4);
		$titre = "evenement-du-".$titre;
	}
	
	$flux['objet']['data'] = $titre;
	return $flux;
}


function simplecal_optimiser_base_disparus($flux){
	$n = &$flux['data'];
	$mydate = $flux['args']['date'];


	# les evenements qui sont dans une id_rubrique inexistante
	$res = sql_select(
		"e.id_evenement AS id",
		"spip_evenements AS e LEFT JOIN spip_rubriques AS r ON e.id_rubrique=r.id_rubrique",
		"e.id_rubrique!=0 AND r.id_rubrique IS NULL AND e.maj < $mydate"
	);

	$n+= optimiser_sansref('spip_evenements', 'id_evenement', $res);

	//
	// Evenements
	//

	sql_delete("spip_evenements", "statut='poubelle' AND maj < $mydate");
	
	
	# R.A.Z des type/id_objet inexistants
	# -------------------------------------
	$r = sql_select("DISTINCT type", "spip_evenements as e", "e.type is not null and e.type!=''");
	while ($t = sql_fetch($r)){
		$type = $t['type'];
		$spip_table_objet = table_objet_sql($type);
		$id_table_objet = id_table_objet($type);
		
		$res = sql_select(
			"e.id_evenement AS id, e.id_objet",
			"spip_evenements AS e LEFT JOIN $spip_table_objet AS o ON o.$id_table_objet=e.id_objet AND e.type=".sql_quote($type),
			"o.$id_table_objet IS NULL"
		);
		
		// sur une cle primaire composee, pas d'autres solutions que de traiter un a un
		while ($row = sql_fetch($sel)){
			$data = array();
			$data['type'] = "";
			$data['objet'] = null;
			sql_updateq('spip_evenements', $data, "id_evenement=".$row['id']);
			spip_log("- Reference '".$type."".$row['id_objet']."' retirée dans la table spip_evenements (id=".$row['id'].")", "simplecal");
		}
	}
	
	# -------------------------------------------------------------------------
	# Nettoyage des liens vers des evenements inexistants 
	# -------------------------------------------------------------------------
	# spip_auteurs_liens   : Traite par SPIP (ecrire/genie/optimiser.php)
	# spip_mots_liens      : Traite par l'extension 'mots'
	# spip_documents_liens : Traite par l'extension 'media'
	# spip_forums          : Traite par l'extension 'forums'
	# -------------------------------------------------------------------------
	
	return $flux;
}

// pipeline : permettre la recherche dans les evenements
function simplecal_rechercher_liste_des_champs($tables){
	// Prendre en compte certains champs
	$tables['evenements']['titre'] = 3;
	$tables['evenements']['lieu'] = 3;
	$tables['evenements']['descriptif'] = 3;
	$tables['evenements']['texte'] = 3;

	return $tables;
}

?>

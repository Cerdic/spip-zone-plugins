<?php
/**
 * Plugin À vos souhaits
 * (c) 2012 RastaPopoulos
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajouter les objets sur les vues de rubriques
**/
function souhaits_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['type'] == 'rubrique'
		AND $e['edition'] == false) {

		$id_rubrique = $flux['args']['id_rubrique'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creersouhaitdans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("souhait:icone_creer_souhait"), generer_url_ecrire("souhait_edit", "id_rubrique=$id_rubrique"), "souhait-24.png", "new", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('souhaits', array('titre'=>_T('souhait:titre_souhaits_rubrique') , 'id_rubrique'=>$id_rubrique, 'par'=>'titre'));
		$flux['data'] .= $bouton;

	}
	return $flux;
}


/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 */
function souhaits_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les souhaits
	if (!$e['edition'] AND in_array($e['type'], array('souhait'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}



	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 */
function souhaits_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/souhaits', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('souhait:info_souhaits_auteur')
		), array('ajax' => true));

	}
	return $flux;
}

/*
 * Ajoute les souhaits dans le comptage des enfants d'une rubrique
 */
function souhaits_objet_compte_enfants($flux){
	if ($flux['args']['objet'] == 'rubrique'){
		$statut = '';
		if (isset($flux['args']['statut'])){
			if ($flux['args']['statut'] == 'publie'){
				$statut = sql_in('statut', array('libre', 'cagnotte', 'propose', 'achete'));
			}
		}
		$postdates = ($GLOBALS['meta']["post_dates"] == "non") ?
			"date <= ".sql_quote(date('Y-m-d H:i:s')) : '1=1';
		
		$flux['data']['souhaits'] = sql_countsel(
			'spip_souhaits',
			array(
				'id_rubrique = '.intval($flux['args']['id_objet']),
				$statut,
				$postdates
			)
		);
	}
	
	return $flux;
}

/*
 * Modifie le statut des rubriques parentes (ancienne et nouvelle) lors d'une institution
 */
function souhaits_post_edition($flux){
	// Si on est bien dans l'institution d'un souhait
	if ($flux['args']['action'] == 'instituer' and $flux['args']['table'] == 'spip_souhaits'){
		// Liste des statuts considérés comme publiés
		$statuts_publies = array('libre', 'cagnotte', 'propose', 'achete');
		
		// On calcule si l'ancien statut est considéré comme publié ou pas
		$statut_ancien = in_array($flux['args']['statut_ancien'], $statuts_publies) ? 'publie' : 'new';
		
		//  S'il y a changements de statut
		$champs = $flux['data'];
		if (isset($champs['statut'])){
			// Si un admin a repassé manuellement un souhait en libre ou cagnotte, on vide les propositions
			if (in_array($champs['statut'], array('libre', 'cagnotte')) and $champs['statut'] != $flux['args']['statut_ancien']){
				include_spip('action/editer_objet');
				objet_modifier('souhait', $flux['args']['id_objet'], array('propositions'=>''));
			}
			
			// On calcule si le nouveau statut est considéré comme publié ou pas pour une rubrique
			$champs['statut'] = in_array($champs['statut'], $statuts_publies) ? 'publie' : 'new';
			
			// Si les statuts sont les mêmes on ne fait rien
			if ($statut_ancien == $champs['statut']){
				unset($champs['statut']);
			}
		}
		
		// On modifie le statut des rubriques parentes
		include_spip('inc/rubriques');
		calculer_rubriques_if($flux['args']['id_parent_ancien'], $champs, $statut_ancien, false);
	}
	
	return $flux;
}

?>

<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Export de la config
function produits_ieconfig_metas($table) {
	$table['produits']['titre'] = _T('produit:titre_produit');
	$table['produits']['icone'] = 'prive/themes/spip/images/produit-16.png';
	$table['produits']['metas_serialize'] = 'produits,produits_*';

	return $table;
}

/**
 * Insertion dans le pipeline grappes_objets_lies (Plugin Grappes)
 * Définis le tableau des objets pouvant être liés aux grappes, la clé est le type d'objet (au pluriel),
 * la valeur, le label affiché dans le formulaire d'édition de grappe
 * @param array $array
 *	Le tableau du pipeline
 * @return array $array
 *	Le tableau complété
 */
function produits_grappes_objets_lies($array) {
	$array['produits'] = _T('produits:titre_page_configurer_produits');
	return $array;
}

/**
 * produits_acceuil_encours
 *
 * Afficher les produits en cours de validation
 * sur l'acceuil de l'espace privé
 */
function produits_accueil_encours($flux) {
	$flux .= recuperer_fond(
		'prive/objets/liste/produits',
		array(
			'statut' => array('prepa','prop'),
			'cacher_tri' => true,
			'nb' => 5
		),
		array('ajax' => true)
	);

	return $flux;
}


// Insérer les listes de produits et le bouton de création
// dans les pages rubriques et la liste des produits de l'auteur
function produits_affiche_enfants($flux) {
	if (isset($flux['args']['id_rubrique'])
		AND $flux['args']['id_rubrique'] > 0) {
		$flux['data'] .= recuperer_fond(
			'prive/objets/liste/produits',
			array('id_rubrique' => $flux['args']['id_rubrique']),
			array(
				'ajax' => true
			)
		);

		if (autoriser('creerproduitdans', 'rubrique', $flux['args']['id_rubrique'])) {
			$flux['data'] .= icone_verticale(_T('produit:icone_creer_produit'), generer_url_ecrire('produit_edit', 'id_rubrique='.$flux['args']['id_rubrique']), find_in_path('prive/themes/spip/images/produits-24.png'), 'new', 'right'). "<br class='nettoyeur' />";
		}
	}
	// Afficher les produits sur la page d'un auteur
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'auteur'
	  AND $e['edition'] == false) {
			$id_auteur = $flux['args']['id_objet'];
			$lister_objets = charger_fonction('lister_objets','inc');
			$flux['data'] .= $lister_objets('produits', array('titre'=>_L('Produits de cet auteur') , 'id_auteur'=>$id_auteur, 'par'=>'date'));
			$flux['data'] .= "<br class='nettoyeur' />";
	}

	return $flux;
}

/**
 * Afficher le nombre d'éléments dans les parents
 *
 * @pipeline boite_infos
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function produits_boite_infos($flux) {
	if (isset($flux['args']['type']) and isset($flux['args']['id']) and $id = intval($flux['args']['id'])) {
		$texte = '';
		if ($flux['args']['type'] == 'rubrique' and $nb = sql_countsel('spip_produits', array("statut='publie'", 'id_rubrique=' . $id))) {
			$texte .= '<div>' . singulier_ou_pluriel($nb, 'produit:info_1_produit', 'produit:info_nb_produits') . "</div>\n";
		}
		if ($texte and $p = strpos($flux['data'], '<!--nb_elements-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		}
	}
	return $flux;
}

/**
 * Compter les enfants d'un objet
 *
 * @pipeline objets_compte_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function produits_objet_compte_enfants($flux) {
	if ($flux['args']['objet'] == 'rubrique' and $id_rubrique = intval($flux['args']['id_objet'])) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['produits'] = sql_countsel('spip_produits', 'id_rubrique= ' . intval($id_rubrique) . " AND (statut = 'publie')");
		}
		else {
			$flux['data']['produits'] = sql_countsel('spip_produits', 'id_rubrique= ' . intval($id_rubrique) . " AND (statut <> 'poubelle')");
		}
	}
	return $flux;
}


/**
 * Publier et dater les rubriques qui ont un produit publie
 *
 * @param array $flux
 * @return array
 */
function produits_calculer_rubriques($flux){
	include_spip('inc/config');
	if (lire_config('produits/publier_rubriques')) {
		$r = sql_select("R.id_rubrique AS id, max(A.date) AS date_h", "spip_rubriques AS R, spip_produits AS A", "R.id_rubrique = A.id_rubrique AND R.date_tmp <= A.date AND A.statut='publie' ", "R.id_rubrique");
		while ($row = sql_fetch($r)) {
			sql_updateq('spip_rubriques', array('statut_tmp' => 'publie', 'date_tmp' => $row['date_h']), "id_rubrique=" . $row['id']);
		}
	}
	return $flux;
}

// Si pas de critère "statut", on affiche que les produits publiés
function produits_pre_boucle($boucle) {
	if ($boucle->type_requete == 'produits') {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut']) and !isset($boucle->modificateur['tout'])) {
			$boucle->where[] = array("'='", "'$statut'", "sql_quote('publie')");
		}
	}
	return $boucle;
}


// Lier les auteurs aux produits
// inssérer le formulaire de liaison d'auteur sur la vue d'un produit
function produits_affiche_milieu($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
			AND $e['edition'] == false
		  AND $e['type'] == 'produit'
			AND $id_table_objet = $e['id_table_objet']) {

			$texte = recuperer_fond('prive/objets/editer/liens', array(
				'table_source' => 'auteurs',
				'objet' => $e['type'],
				'id_objet' => $flux['args'][$id_table_objet],
				#'editable'=>autoriser('associerauteurs', $e['type'], $e['id_objet']) ? 'oui' : 'non'
			));
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}
/**
 * Optimiser la base de données
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array	   Données du pipeline
 */
function produits_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('produit'=>'*'), '*');
	sql_delete('spip_produits', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Pipeline de la corbeille, permet de définir les objets à supprimer
 *
 * @param array $param Tableau d'objets
 *
 * @return array Tableau d'objets complété
 */
function produits_corbeille_table_infos($param){
	$param['produits'] = array(
		'statut' => 'poubelle',
		'tableliee'=> array('spip_produits'),
	);
	return $param;
}

/**
 * Déclarer l'héritage pour compositions
 *
 * @param $heritages
 *
 * @return mixed
 */
function produits_compositions_declarer_heritage($heritages){
	$heritages['produit'] = 'rubrique';
	return $heritages;
}

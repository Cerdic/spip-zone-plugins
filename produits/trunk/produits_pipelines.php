<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Export de la config 
function produits_ieconfig_metas($table){
	
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
 * 	Le tableau du pipeline
 * @return array $array
 * 	Le tableau complété
 */
function produits_grappes_objets_lies($array){
	$array['produits'] = _T('produits:titre_page_configurer_produits');
	return $array;
}

/**
 * produits_acceuil_encours
 *
 * Afficher les produits en cours de validation
 * sur l'acceuil de l'espace privé
 * 
*/
function produits_accueil_encours($flux){
	$flux .= recuperer_fond('prive/objets/liste/produits', array(
					'statut' => array('prepa','prop'),
					'cacher_tri' => true,
					'nb' => 5),
					array('ajax' => true)
				);
	return $flux;
}


// Insérer les listes de produits et le bouton de création dans les pages rubriques
function produits_affiche_enfants($flux){
	if ($flux['args']['id_rubrique'] > 0){
		$flux['data'] .= recuperer_fond(
			'prive/objets/liste/produits',
			array('id_rubrique' => $flux['args']['id_rubrique']),
			array(
				'ajax' => true
			)
		);
	
		if (autoriser('creerproduitdans', 'rubrique', $flux['args']['id_rubrique'])){
			$flux['data'] .= icone_verticale(_T('produit:icone_creer_produit'), generer_url_ecrire('produit_edit', 'id_rubrique='.$flux['args']['id_rubrique']), find_in_path('prive/themes/spip/images/produits-24.png'), 'new', 'right');
		}
	}
	
	return $flux;
}

// Compter les produits comme des enfants de rubriques
function produits_objet_compte_enfants($flux){
	if ($flux['args']['objet'] == 'rubrique' and ($id_rubrique = intval($flux['args']['id_objet'])) > 0){	
		$statut = $flux['args']['statut'] ? ' and statut='.sql_quote($flux['args']['statut']) : '';
		$flux['data']['produits'] = sql_countsel('spip_produits', 'id_rubrique='.$id_rubrique.$statut);
	}
	return $flux;
}

// Si pas de critère "statut", on affiche que les produits publiés
function produits_pre_boucle($boucle){
	if ($boucle->type_requete == 'produits') {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut']) and !isset($boucle->modificateur['tout'])){
			$boucle->where[] = array("'='", "'$statut'", "sql_quote('publie')");
		}
	}
	return $boucle;
}

?>

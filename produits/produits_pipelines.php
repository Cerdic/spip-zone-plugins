<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Déclarer pour l'URL des produits
function produits_declarer_url_objets($objets){
	$objets[] = 'produit';
	return $objets;
}

// Insérer les listes de produits et le bouton de création dans les pages rubriques
function produits_affiche_enfants($flux){
	if ($flux['args']['id_rubrique'] > 0){
		$flux['data'] .= recuperer_fond(
			'prive/objets/liste/produits',
			array(),
			array(
				'ajax' => true
			)
		);
	
		if (autoriser('creerproduitdans', 'rubrique', $flux['args']['id_rubrique'])){
			$flux['data'] .= icone_inline(_T('produits:produit_bouton_ajouter'), generer_url_ecrire('produit_edit', 'nouveau=oui&id_rubrique='.$flux['args']['id_rubrique']), find_in_path('images/produits-24.png'), 'creer.gif', 'right');
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

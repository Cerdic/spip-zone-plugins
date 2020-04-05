<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION')) return;


function produits_liens_affiche_milieu($flux){
	if($flux['args']['exec'] == 'produit' && $id_produit=$flux['args']['id_produit']) { 
		$flux['data'] .= recuperer_fond('prive/objets/liste/objets_lies_produit', array('page_envoi'=>'produit','id_produit'=>$id_produit), array('ajax'=>true));
	
	}
return $flux;
}


/**
 * Pipeline afficher_complement_objet
 * afficher le portfolio des produits 
 * ajout de produit sur les fiches objet (avec autorisation dans le squelette)
 * 
 * @param  $flux
 * @return
 */
function produits_liens_afficher_complement_objet($flux){
	if ($type=$flux['args']['type']
		AND $id=intval($flux['args']['id'])
	  ) {
		$contexte = array('objet'=>$type,'id_objet'=>$id);
		$flux['data'] .= recuperer_fond('prive/objets/contenu/portfolio_produits',array_merge($_GET,$contexte));
	}
	
	return $flux;
}


/**
 * Configuration des contenus
 * @param array $flux
 * @return array
 */
function produits_liens_recuperer_fond($flux){
	
	// surcharge la configuration du plugin produits
    	if ($flux['args']['fond'] == 'formulaires/configurer_produits'){
    		include_spip('inc/config');
    		 
                $flux['args']['contexte']['produits_objets'] = lire_config("produits/produits_liens/produits_objets");
              
    		$objets_liens = recuperer_fond('formulaires/configurer_produits_liens', $flux['args']['contexte']);
    		$flux['data']['texte'] = str_replace('<!--extra-->', '<!--extra-->' . $objets_liens, $flux['data']['texte']);   			
    	
    	}
	return $flux;
}

function produits_liens_formulaire_traiter($flux){
	
	// surcharge le traitement pour la configuration du plugin produits	
	if ($flux['args']['form'] == 'configurer_produits'){
		include_spip('inc/config');
		$produits_objets  = _request("produits_objets"); 
		
                ecrire_config("produits/produits_liens/produits_objets",$produits_objets);			
	}
	return $flux;
}


/**
 * Compter les produits dans un objet
 *
 * @param array $flux
 * @return array
 */
function produits_liens_objet_compte_enfants($flux){
	if ($objet = $flux['args']['objet']
	  AND $id=intval($flux['args']['id_objet'])) {
		// juste les publies ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['produit'] = sql_countsel('spip_produits AS D JOIN spip_produits_liens AS L ON D.id_produit=L.id_produit', "L.objet=".sql_quote($objet)."AND L.id_objet=".intval($id)." AND (D.statut='publie')");
		} else {
			$flux['data']['produit'] = sql_countsel('spip_produits AS D JOIN spip_produits_liens AS L ON D.id_produit=L.id_produit', "L.objet=".sql_quote($objet)."AND L.id_objet=".intval($id)." AND (D.statut='publie' OR D.statut='prepa')");
		}
	}
	return $flux;
}

<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Remplir un panier avec un objet quelconque
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_remplir_panier_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// On récupère les infos de l'argument
	@list($objet, $id_objet, $quantite) = explode('-', $arg);
	$id_objet = intval($id_objet);
	$quantite = intval($quantite) ? intval($quantite) : 1;
	
	// Il faut cherche le panier du visiteur en cours, et sinon le créer
	include_spip('inc/paniers');
	$id_panier = paniers_id_panier_encours();
	
	// On ne fait que s'il y a bien un panier existant et un objet valable
	if ($id_panier > 0 and $objet and $id_objet) {
		// Il faut maintenant chercher si cet objet précis est *déjà* dans le panier
		$quantite_deja = sql_getfetsel(
			'quantite',
			'spip_paniers_liens',
			array(
				'id_panier = '.$id_panier,
				'objet = '.sql_quote($objet),
				'id_objet = '.$id_objet
			)
		);
		
		// Si on a déjà une quantité, on fait une mise à jour
		if ($quantite_deja !== false){
			sql_updateq(
				'spip_paniers_liens',
				array('quantite' => intval($quantite_deja) + $quantite),
				'id_panier = '.$id_panier.' and objet = '.sql_quote($objet).' and id_objet = '.$id_objet
			);
		}
		// Sinon on crée le lien
		else{
			sql_insertq(
				'spip_paniers_liens',
				array(
					'id_panier' => $id_panier,
					'objet' => $objet,
					'id_objet' => $id_objet,
					'quantite' => $quantite
				)
			);
		}
	}
}

?>

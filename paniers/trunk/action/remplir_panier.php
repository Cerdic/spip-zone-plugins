<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Remplir un panier avec un objet quelconque
 * @param string $arg
 */
function action_remplir_panier_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// On récupère les infos de l'argument
	@list($objet, $id_objet, $quantite, $negatif) = explode('-', $arg);

	$quantite = intval($quantite) ? intval($quantite) : 1;
	// retirer un objet du panier
	if(isset($negatif)) $quantite = intval(-$quantite);
		
	// Il faut cherche le panier du visiteur en cours
	include_spip('inc/paniers');
	$id_panier_base = 0;
	if ($id_panier = paniers_id_panier_encours()){
		//est-ce que le panier est bien en base
		$id_panier_base = intval(sql_getfetsel(
				'id_panier',
				'spip_paniers',
				array(
					'id_panier = '.intval($id_panier),
					'statut = '.sql_quote('encours')
				)
		));
	}
	
	// S'il n'y a pas de panier, on le crée
	if (!$id_panier OR !$id_panier_base){
		$id_panier = paniers_creer_panier();
	}

	// On ne fait que s'il y a bien un panier existant et un objet valable
	if ($id_panier > 0 and $objet and $id_objet) {
		// Il faut maintenant chercher si cet objet précis est *déjà* dans le panier
		$quantite_deja = intval(sql_getfetsel(
			'quantite',
			'spip_paniers_liens',
			array(
				'id_panier = '.intval($id_panier),
				'objet = '.sql_quote($objet),
				'id_objet = '.intval($id_objet)
			)
		));
		
				
		// Si on a déjà une quantité, on fait une mise à jour
		if ($quantite_deja){
			$cumul_quantite = $quantite_deja + $quantite;
			//Si le cumul_quantite est 0, on efface
			if ($cumul_quantite <= 0) 
				sql_delete('spip_paniers_liens','id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet));
			//Sinon on met à jour
			else sql_updateq(
				'spip_paniers_liens',
				array('quantite' => $cumul_quantite),
				'id_panier = '.intval($id_panier).' and objet = '.sql_quote($objet).' and id_objet = '.intval($id_objet)
			);
		}
		// Sinon on crée le lien
		else {
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
		
		// Mais dans tous les cas on met la date du panier à jour
		sql_updateq(
			'spip_paniers',
			array('date'=>date('Y-m-d H:i:s')),
			'id_panier = '.intval($id_panier)
		);
	}

	// On vide le cache de l'objet sur lequel on vient de travailler.
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");

}

?>

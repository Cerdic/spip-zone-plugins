<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function stocks_formulaire_charger($flux) {

	$form = $flux['args']['form'];

	if ($form == 'editer_produit') {
		$stock_default = lire_config('stocks/quantite_default');
		include_spip('inc/stocks');
		$id_produit = intval($flux['args']['args'][0]);
		$quantite = get_quantite('produit', $id_produit);

		// La quantité produit
		$flux['data']['_saisies'][] = array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'stocks',
				'label'=> 'Stock'
			),
			'saisies'=> array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'quantite_produit',
						'label' => '<:stocks:quantite_produit:>',
						'defaut' => isset($quantite) ? $quantite : $stock_default
					)
				)
			)
		);
	}

	return $flux;
}

function stocks_formulaire_traiter($flux) {

	$form = $flux['args']['form'];

	if ($form == 'editer_produit') {
		include_spip('inc/stocks');
		$id_produit = $flux['data']['id_produit'];
		$quantite = intval(_request('quantite_produit'));

		//spip_log("$id_produit",'stocks');
		set_quantite('produit', $id_produit, $quantite);
	}

	return $flux;
}

/*
 * function stocks_afficher_fiche_objet
 * @param $flux
 */
function stocks_afficher_fiche_objet($flux) {

	if ($flux['args']['type'] == 'produit') {
		$objet = $flux['args']['type'];
		$id_objet = intval($flux['args']['id']);

		$texte = recuperer_fond(
			'prive/squelettes/inclure/stock_fiche_objet',
			array(
			'objet'=>$objet,
			'id_objet'=>$id_objet
			)
		);

		if ($p = strpos($flux['data'], '<!--afficher_fiche_objet-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}
	return $flux;
}

function stocks_affiche_milieu($flux) {
	$texte = "";
	if ($flux['args']['exec'] == 'produit') {
		$flux['data'] .= recuperer_fond(
			'prive/squelettes/inclure/gerer_stock',
			$flux['args'] // On passe le contexte au squelette
		);
	}
	if($texte){
		if ($p = strpos($flux['data'], "<!--affiche_milieu-->")) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}
	return $flux;
}

// ? je ne vois pas la necessité de cette pipeline
// n'utilisant pas des champ extra quantité sur des objets
function stocks_pre_boucle($boucle) {
	//Connaitre la table en cours
  $id_table = $boucle->id_table;
  // Savoir si la table a une jointure avec la table Stocks
  if ($jointure = array_keys($boucle->from, 'spip_stocks')) {
	  //Vérifier qu'on est bien dans le cas d'une jointure automatique
	  if (isset($boucle->join[$jointure[0]])
	  	and isset($boucle->join[$jointure[0]][3])
			and $boucle->join[$jointure[0]]
			and $boucle->join[$jointure[0]][3]
	  ) {
		  //Le critere ON de la jointure (index 3 dans le tableau de jointure) est incompléte
		  //on fait en sorte de retomber sur ses pattes, en indiquant l'objet à joindre
		  $boucle->join[$jointure[0]][3] = "'L1.objet='.sql_quote('".objet_type($id_table)."')";
		}
  }
	return $boucle;
}

/**
 * post_edition
 *
 * passe un produit au statut épuisé QUAND la commande passe du statut attente|encours a payee
 *
 * @see http://contrib.spip.net/Commandes-4527#forum478302
 * @see http://programmer.spip.net/et-les-autres
 *
*/
function stocks_post_edition($flux){

	// Apres COMMANDE :
	// quand la commande passe du statut=attente|encours a statut=paye
	if (
			$flux['args']['action'] == 'instituer'
			AND $flux['args']['table'] == 'spip_commandes'
			AND ($id_commande = intval($flux['args']['id_objet'])) > 0
			AND ($statut_nouveau = $flux['data']['statut']) == 'paye'
			AND ( ($statut_ancien = $flux['args']['statut_ancien']) == 'attente'
						OR ($statut_ancien = $flux['args']['statut_ancien']) == 'encours'
			)
	){
			// retrouver les objets correspondants a la commande dans spip_commandes_details
			if (
					$objets = sql_allfetsel('objet,id_objet,quantite', 'spip_commandes_details', 'id_commande='.intval($id_commande))
					AND is_array($objets)
					AND count($objets)
			){

					include_spip('action/editer_objet');

					foreach($objets as $v) {
						if($v['objet']=='produit'){
								// spip_log($v,'stocks');
								// Stock
								$objet = $v['objet'];
								$id_objet = intval($v['id_objet']);
								$quantite = intval($v['quantite']);

								include_spip('inc/stocks');
								$dispo = get_quantite($objet,$id_objet);
								if($dispo >= $quantite){
										spip_log("Dispo : $dispo - Quantite : $quantite | Objet : $objet Id_objet : $id_objet",'stocks');
										//$dispo = get_quantite($objet,$id_objet);
										// $new_stock = intval($dispo) - intval($quantite);
										// spip_log("Mise a jour du stock : $new_stock",'stocks');
										$stock = incrementer_quantite($objet,$id_objet,-$quantite);
										// Passé le produit en statut épuisé si stock 0
										$new_stock = get_quantite($objet,$id_objet);
										if($new_stock <= 0){
												// https://www.spip.net/fr_article5528.html
												include_spip('inc/autoriser');
												// donner une autorisation exceptionnelle temporaire
												autoriser_exception('modifier', 'produit', $id_objet);
												spip_log("Stock épuisé : $new_stock produit $id_objet",'stocks');
												objet_modifier($objet, $id_objet, array('statut'=>'epuise'));
												// retirer l'autorisation exceptionnelle
												autoriser_exception('modifier', 'produit', $id_objet, false);
										}
								}
						}
					}
			}
	}

	return $flux;
}

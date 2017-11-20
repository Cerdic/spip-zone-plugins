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

function stocks_formulaire_traiter($flux) 
{

	$form = $flux['args']['form'];

	if($form == "editer_produit") {
		include_spip('inc/stocks');
		$id_produit = $flux['data']['id_produit'];
		$quantite = intval(_request('quantite_produit'));
		
		//spip_log("$id_produit",'stocks');
		set_quantite("produit", $id_produit, $quantite);
	}
	
	return $flux;
}

/*
 * function stocks_afficher_contenu_objet
 * @param $flux
 */

function stocks_afficher_fiche_objet($flux) 
{

	if($flux['args']['type'] == 'produit') {

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


function stocks_pre_boucle($boucle) 
{
	//Connaitre la table en cours
	//	  $id_table = $boucle->id_table;
	//
	//	  //Savoir si on consulté la table organisations_liens
	//	  if ($jointure = array_keys($boucle->from, 'spip_stocks')) {
	//		  //Vérifier qu'on est bien dans le cas d'une jointure automatique
	//		  if (isset($boucle->join[$jointure[0]])
	//		  and isset($boucle->join[$jointure[0]][3])
	//				and $boucle->join[$jointure[0]]
	//				and $boucle->join[$jointure[0]][3]
	//		  ) {
	//			  //Le critere ON de la jointure (index 3 dans le tableau de jointure) est incompléte
	//			  //on fait en sorte de retomber sur ses pattes, en indiquant l'objet à joindre
	//			  $boucle->join[$jointure[0]][3] = "'L1.objet='.sql_quote('".objet_type($id_table)."')";
	//		}
	//	  }

	return $boucle;
}


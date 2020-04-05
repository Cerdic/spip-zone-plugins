<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// La CSS pour une commande
function commandes_insert_head_css($flux){
	$css = find_in_path('css/commandes.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

// Supprimer toutes les commandes en cours qui sont trop vieilles
function commandes_optimiser_base_disparus($flux){
	include_spip('inc/config');
	// On cherche la date depuis quand on a le droit d'avoir fait la commande (par défaut 1h)
	$depuis = date('Y-m-d H:i:s', time() - 3600*intval(lire_config('commandes/duree_vie', 1)));
	// On récupère les commandes trop vieilles
	$commandes = sql_allfetsel(
		'id_commande',
		'spip_commandes',
		'statut = '.sql_quote('encours').' and date<'.sql_quote($depuis)
	);

	// S'il y a bien des commandes à supprimer
	if ($commandes) {
		$commandes = array_map('reset', $commandes);
		include_spip('inc/commandes');
		commandes_effacer($commandes);
		$flux['data'] += count($commandes);
	}

	return $flux;
}


/**
 * Ajouter une boite sur la fiche de commande
 *
 * @param string $flux
 * @return string
 */
function commandes_affiche_gauche($flux) {
		
	if ($flux['args']['exec'] == 'commande_edit'
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND ($id = intval($flux['args'][$id_table_objet]))
	  AND (autoriser('modifier', 'commande', 0))) {
		//un test pour todo ajouter un objet (produit,document,article,abonnement,rubrique ...)
			$flux['data'] .= recuperer_fond('prive/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
		}
	
	return $flux;
}

?>

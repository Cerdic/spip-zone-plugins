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

	if (
		$flux['args']['exec'] == 'commande_edit'
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND ($id = intval($flux['args'][$id_table_objet]))
		AND (autoriser('modifier', 'commande', 0))
	) {
		//un test pour todo ajouter un objet (produit,document,article,abonnement,rubrique ...)
		$flux['data'] .= recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
	}

	return $flux;
}


/**
 * accueil : liste des commandes en attente de validation
 *
 * @param string $flux
 * @return string $flux 
 */
function commandes_accueil_encours($flux) {

	if ( $liste = recuperer_fond('prive/objets/liste/commandes', array(
		'titre' => _T('commandes:titre_commandes_a_envoyer'),
		'statut' => 'paye',
		'cacher_tri' => true))
	) {
		$flux .= "<div class='commandes'>";
		$flux .= $liste . "</div>";
	}
	return $flux;
}


/**
 * Mettre à jour les dates de paiement ou d'envoi
 * en amont de la modification du statut d'une commande
 *
 * @param array $flux
 *	$flux['args'][x] = arguments (action, table, id_objet etc.)
 *	$flux['data'][x] = champs modifiés (statut, date etc.)
 * @return array $flux
**/
function commandes_pre_edition($flux){

	if (
		$action = $flux['args']['action']
		and $table = $flux['args']['table']
		and $statut = $flux['data']['statut']
		and $action == 'instituer'
		and $table == table_objet_sql('commande')
		and $date = date('Y-m-d H:i:s') // il faudrait copier la date de maj pour bien faire...
	) {
		switch ($statut) {
			case 'paye';
				$flux['data']['date_paiement'] = $date;
				break;
			case 'envoye';
				$flux['data']['date_envoi'] = $date;
				break;
		}
	}

	return($flux);
}


/**
 * Envoyer les notifications 
 * en aval de la modification du statut d'une commande
 *
 * @param array $flux
 *	$flux['args'][x] = arguments (table, id_objet, action, statut_ancien, date_ancienne, id_parent_ancien)
 *	$flux['data'][x] = champs modifiés (statut, date_paiement etc.)
 * @return array $flux
**/
function commandes_post_edition($flux){

	if (
		($action=$flux['args']['action']) == 'instituer' // action instituer
		and ($table=$flux['args']['table']) == table_objet_sql('commande') // on institue une commande
		and ($statut_ancien=$flux['args']['statut_ancien']) != ($statut=$flux['data']['statut']) // le statut a été modifié
		and $id_commande = $flux['args']['id_objet'] // on a bien un identifiant pour la commande
		and include_spip('inc/config')
		and $config = lire_config('commandes')
		and ($config['activer']) // les notifications sont activées
		and (in_array($statut, $config['quand'])) // le nouveau statut est valide pour envoyer une notification
		and ($notifications = charger_fonction('notifications', 'inc', true)) // le plugin notifications est actif
	) {
		// Déterminer l'expediteur
		$options = array();
		if( $config['expediteur'] != "facteur" )
			$options['expediteur'] = $config['expediteur_'.$config['expediteur']];

		// Envoyer au vendeur et au client
		$notifications('commande_vendeur', $id_commande, $options);
		if($config['client'])
			$notifications('commande_client', $id_commande, $options);
	}

	return($flux);
}

?>

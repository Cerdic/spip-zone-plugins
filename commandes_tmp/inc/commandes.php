<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Créer une commande en cours pour le visiteur actuel.
 *
 * @return int $id_commande Retourne l'identifiant SQL de la commande
 */
function creer_commande_encours(){
	include_spip('inc/session');
	
	// S'il y a une commande en cours dans la session, on la supprime
	if (($id_commande = intval(session_get('id_commande'))) > 0){
		// Si la commande est toujours "encours" on la supprime de la base
		if ($statut = sql_getfetsel('statut', 'spip_commandes', 'id_commande = '.$id_commande) and $statut == 'encours'){
			// On supprime son contenu
			sql_delete('spip_commandes_details', 'id_commande = '.$id_commande);
		
			// S'il y a des adresses attachées à la commande, on les supprime
			if ($adresses_commande = sql_allfetsel('id_adresse', 'spip_adresses_liens', array('objet = '.sql_quote('commande'), 'id_objet = '.$id_commande))){
				$adresses_commande = array_map('reset', $adresses_commande);
				$in = sql_in('id_adresse', $adresses_commande);
				sql_delete('spip_adresses_liens', $in);
				sql_delete('spip_adresses', $in);
			}
		
			// On supprime la commande
			sql_delete('spip_commandes', 'id_commande = '.$id_commande);
		}
		
		// Dans tous les cas on supprime la valeur de session
		session_set('id_commande');
	}
	
	// Le visiteur en cours
	$id_auteur = session_get('id_auteur') > 0 ? session_get('id_auteur') : 0;
	
	// La référence
	$fonction_reference = charger_fonction('commandes_reference', 'inc/');
	
	$champs = array(
		'reference' => $fonction_reference($id_auteur),
		'id_auteur' => $id_auteur,
		'date' => date('Y-m-d H:i:s'),
		'statut' => 'encours'
	);
	
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_commandes',
			),
			'data' => $champs
		)
	);
	$id_commande = sql_insertq('spip_commandes', $champs);
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_commandes',
				'id_objet' => $id_commande
			),
			'data' => $champs
		)
	);
	
	session_set('id_commande', $id_commande);
	
	return $id_commande;
}

?>

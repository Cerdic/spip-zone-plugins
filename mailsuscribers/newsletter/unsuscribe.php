<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("action/editer_objet");
include_spip('inc/mailsuscribers');
include_spip('inc/lire_config');
include_spip('inc/autoriser');

/**
 * Desinscrire un suscriber par son email
 * si une ou plusieurs listes precisees, le suscriber est desinscrit de ces seules listes
 * si il n'en reste aucune, le statut du suscriber est suspendu
 *
 * si aucune liste precisee, le suscriber est desinscrit de toutes les listes newsletter*
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   listes : array
 * @return bool
 *   true si inscrit, false sinon
 */
function newsletter_unsuscribe_dist($email,$options = array()){

	// chercher si un tel email est deja en base
	$row = sql_fetsel('*','spip_mailsuscribers','email='.sql_quote($email));
	if ($row AND $row['statut']!=='poubelle'){

		$set = array();
		$listes = explode(",",$row['listes']);
		$listes = array_map('trim',$listes);
		$listes = array_unique($listes);
		$listes = array_filter($listes);

		if (isset($options['listes'])){
			$retire = array_map('mailsuscribers_normaliser_nom_liste',$options['listes']);
			$listes = array_diff($listes,$retire);
			$set['listes'] = implode(",",$listes);
			if (!count($listes)){
				$set['statut'] = "refuse";
			}
		}
		else {
			// aucune liste precisee : on veut desabonner de toutes les newsletter
			// si il n'y a que des newsletter:: on y touche pas et on change simplement le statut
			// si il y a d'autres inscriptions, on ne laisse que celles-ci
			$restantes = array();
			foreach ($listes as $l){
				if ($l!=="newsletter" AND strncmp($l,'newsletter::',12)!==0)
					$restantes[] = $l;
			}
			if (count($restantes)){
				$set['listes'] = implode(",",$restantes);
			}
			else {
				$set['statut'] = "refuse";
			}
		}

		if (count($set)){
			autoriser_exception("modifier","mailsuscriber",$row['id_mailsuscriber']);
			objet_modifier("mailsuscriber",$row['id_mailsuscriber'],$set);
			autoriser_exception("modifier","mailsuscriber",$row['id_mailsuscriber'],false);
		}
	}

	return true;
}
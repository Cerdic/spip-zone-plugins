<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("action/editer_objet");
include_spip('inc/mailsubscribers');
include_spip('inc/config');
include_spip('inc/autoriser');

/**
 * Desinscrire un subscriber par son email
 * si une ou plusieurs listes precisees, le subscriber est desinscrit de ces seules listes
 * si il n'en reste aucune, le statut du subscriber est suspendu
 *
 * si aucune liste precisee, le subscriber est desinscrit de toutes les listes newsletter*
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   listes : array
 * @return bool
 *   true si inscrit, false sinon
 */
function newsletter_unsubscribe_dist($email,$options = array()){

	// chercher si un tel email est deja en base
	$row = sql_fetsel('*','spip_mailsubscribers','email='.sql_quote($email));
	if ($row AND $row['statut']!=='poubelle'){

		$set = array();
		$listes = explode(",",$row['listes']);
		$listes = array_map('trim',$listes);
		$listes = array_unique($listes);
		$listes = array_filter($listes);

		if (isset($options['listes'])){
			$retire = array_map('mailsubscribers_normaliser_nom_liste',$options['listes']);
			$listes = array_diff($listes,$retire);
			$set['listes'] = implode(",",$listes);
			if (!count($listes)){
				$set['statut'] = "refuse";
				$set['email'] = mailsubscribers_obfusquer_email($email);
			}
		}
		else {
			// aucune liste precisee : on veut desabonner de toutes les newsletter
			// si il n'y a que des newsletter:: on y touche pas et on change simplement le statut
			// si il y a d'autres inscriptions, on ne laisse que celles-ci
			$restantes = array();
			foreach ($listes as $l){
				if (strncmp($l,'newsletter::',12)!==0)
					$restantes[] = $l;
			}
			if (count($restantes)){
				$set['listes'] = implode(",",$restantes);
			}
			else {
				$set['statut'] = "refuse";
				$set['email'] = mailsubscribers_obfusquer_email($email);
			}
		}

		if (count($set)){
			autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber']);
			autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber']);
			// d'abord le statut pour notifier avec le bon mail
			if (isset($set['statut'])){
				objet_modifier("mailsubscriber",$row['id_mailsubscriber'],array('statut'=>$set['statut']));
				unset($set['statut']);
			}
			// ensuite l'email ou autre si besoin
			if (count($set))
				objet_modifier("mailsubscriber",$row['id_mailsubscriber'],$set);
			autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber'],false);
			autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber'],false);
		}
	}

	return true;
}

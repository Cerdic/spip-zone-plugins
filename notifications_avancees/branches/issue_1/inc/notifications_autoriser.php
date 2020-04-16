<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function notifavancees_autoriser(){
}

// Configurer les notifications d'un auteur : soit l'auteur soit un admin
function autoriser_auteur_configurer_notifications_dist($faire, $type, $id, $qui, $options){
	if (($qui['statut'] <= '0minirezo' and !$qui['restreint'])
		or ($id>0 and $qui['id_auteur'] == $id)
	)
		return true;
	else
		return false;
}

// Modifier un abonnement : soit l'abonné, soit un admin
function autoriser_notifications_abonnement_modifier_dist($faire, $type, $id, $qui, $options){
	if (($qui['statut'] <= '0minirezo' and !$qui['restreint'])
		or (
			$id_auteur = intval(sql_getfetsel('id_auteur', 'spip_notifications_abonnements', 'id_notifications_abonnement = '.intval($id)))
			and $qui['id_auteur'] == $id_auteur
		)
	)
		return true;
	else
		return false;
}

// Désactiver un abonnement : pouvoir modifier
function autoriser_notifications_abonnement_toggle_dist($faire, $type, $id, $qui, $options){
	return autoriser('modifier', 'notifications_abonnement', $id, $qui, $options);
}

// Supprimer un abonnement : pouvoir modifier
function autoriser_notifications_abonnement_supprimer_dist($faire, $type, $id, $qui, $options){
	return autoriser('modifier', 'notifications_abonnement', $id, $qui, $options);
}

?>

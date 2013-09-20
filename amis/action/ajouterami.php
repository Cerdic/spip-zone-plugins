<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


/**
 * Relie deux auteurs entre eux via la table spip_amis
 * id_auteur = visiteur a l'origine de la demande
 * id_ami = visiteur ayant accepte la demande
 * lors d'une demande par un des protagoniste, le champ statut='prop' indique une demande
 * le champ statut='publie' indique une acceptation
 * les liens sont directionnels, ce qui permet de garder la trace du sens de la transaction
 * les tests X est ami de Y se font ensuite par double test :
 * statut='publie' AND ((id_auteur=X AND id_ami=Y) OR (id_auteur=Y and id_ami=X))
 *
 */
function action_ajouterami_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_ami = $securiser_action();
	if ($id_ami = intval($id_ami)
	AND ($id_auteur = $GLOBALS['visiteur_session']['id_auteur'])){
		include_spip('base/abstract_sql');
		// s'assurer que pas deja une invitation dans ce sens ou deja un ami
		if ($row=sql_fetsel('*','spip_amis',array('id_auteur='.intval($id_auteur),'id_ami='.intval($id_ami))))
			return;
		// si deja une invitation dans l'autre sens, alors on valide
		if ($row=sql_fetsel('*','spip_amis',array('id_auteur='.intval($id_ami),'id_ami='.intval($id_auteur),"statut='prop'"))){
			sql_updateq('spip_amis',array('statut'=>'publie','date'=>'NOW()'),array('id_auteur='.intval($id_ami),'id_ami='.intval($id_auteur)));
			$notification = charger_fonction('notifications','inc');
			$notification('ajouterami',$id_auteur,array('id_auteur' => $id_ami));
		}
		else {
			// sinon lancer une invitation
			sql_insertq('spip_amis',array('id_auteur'=>$id_auteur,'id_ami'=>$id_ami,'statut'=>'prop'));
			$notification = charger_fonction('notifications','inc');
			$notification('inviterami',$id_ami,array('id_auteur' => $id_auteur));
		}
		spip_log("invitation de $id_ami par $id_auteur",'amis');
		include_spip('inc/invalideur');
		suivre_invalideur("amis/$id_auteur/$id_ami");
	}
}

?>
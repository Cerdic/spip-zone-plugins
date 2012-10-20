<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action journal supprimer
 *
 * Supprime de la table journal les entrées de l'environnement passé
 */
function action_journal_supprimer_dist(){
	global $visiteur_session;

	include_spip('inc/autoriser');
	if(!autoriser('supprimer','journal','',$visiteur_session)){
		return false;
	}

	$where = '';
	if($auteur = _request('id_auteur')){
		$where .= 'id_auteur='.sql_quote($auteur);
		$cond = true;
	}
	if($action = _request('journal_action')){
		$where .= $cond ? ' AND ':'';
		$where .= 'action='.sql_quote($action);
		$cond = true;
	}
	if($objet = _request('objet')){
		$where .= $cond ? ' AND ':'';
		$where .= ' objet='.sql_quote($objet);
		$cond = true;
	}
	if($id_objet = _request('id_objet')){
		$where .= $cond ? ' AND ':'';
		$where .= ' id_objet='.sql_quote($id_objet);
		$cond = true;
	}
	if($date_debut = _request('date_debut')){
		$where .= $cond ? ' AND ':'';
		$where .= ' date > '.sql_quote($date_debut);
		$cond = true;
	}
	if($date_fin = _request('date_fin')){
		$where .= $cond ? ' AND ':'';
		$where .= ' date < '.sql_quote($date_fin);
		$cond = true;
	}

	$retour = sql_delete('spip_journal',$where);

	if($redirect = _request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return $nombre;
}
?>
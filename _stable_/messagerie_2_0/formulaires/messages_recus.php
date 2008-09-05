<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


/**
 * Chargement des valeurs par defaut de #FORMULAIRE_MESSAGES_RECUS
 *
 * @return array
 */
function formulaires_messages_recus_charger_dist(){
	$valeurs = array();

	return $valeurs;
}


/**
 * Traitement de la saisie de #FORMULAIRE_MESSAGES_RECUS
 *
 * @return string
 */
function formulaires_messages_recus_traiter_dist(){
	include_spip('base/abstract_sql');
	include_spip('inc/texte');
	$liste = _request('selectionne');
	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	if (is_array($liste) && count($liste)) {
		include_spip('inc/messages');
		if (_request('marquer_lus'))
			messagerie_marquer_lus($id_auteur,$liste);
		elseif (_request('marquer_non_lus'))
			messagerie_marquer_non_lus($id_auteur,$liste);
		elseif (_request('effacer'))
			messagerie_effacer($id_auteur,$liste);
	}
	
	return array(true,"");
}

?>
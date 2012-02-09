<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function dictionnaires_autoriser($flux){
	return $flux;
}

// Qui peut voir la liste des dictionnaires : tout le monde
function autoriser_dictionnaires_bouton_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('bouton', 'dictionnaires_edition');
}
function autoriser_dictionnaires_edition_bouton_dist($faire, $quoi, $id, $qui, $options){
	return true;
}

// Qui peut créer un dictionnaire : ceux qui peuvent configurer le site
function autoriser_dictionnaire_creer_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer');
}
// Qui peut modifier un dictionnaire : ceux qui peuvent configurer le site
function autoriser_dictionnaire_modifier_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer');
}
// Qui peut supprimer un dictionnaire : ceux qui peuvent configurer le site SI ya plus aucune définition dedans !
function autoriser_dictionnaire_supprimer_dist($faire, $quoi, $id, $qui, $options){
	return ($id > 0)
		and autoriser('configurer')
		and !sql_fetsel(
			'id_definition',
			'spip_definitions',
			array(
				'id_dictionnaire = '.$id,
				sql_in('statut', array('publie', 'prop'))
			)
		);
}

// Qui peut créer une définition : un rédacteur
function autoriser_dictionnaire_creerdefinitiondans_dist($faire, $quoi, $id, $qui, $options){
	return $qui['statut'] <= '1comite';
}
// Qui peut modifier une définition : un rédacteur si pas publié, un admin sinon
function autoriser_definition_modifier_dist($faire, $quoi, $id, $qui, $options){
	if ($id > 0
		and $statut = sql_getfetsel('statut', 'spip_definitions', 'id_definition = '.$id)
		and (
			($statut == 'publie' and $qui['statut'] <= '0minirezo')
			or
			($statut != 'publie' and $qui['statut'] <= '1comite')
		)
	){
		return true;
	}
	
	return false;
}
// Qui peut supprimer une définition : pareil que modifier
function autoriser_definition_supprimer_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('modifier', $quoi, $id, $quoi, $options);
}

?>

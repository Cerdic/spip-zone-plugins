<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function campagnes_autoriser(){}


// -----------------
// Objet encarts


// bouton de menu
function autoriser_encarts_menu_dist($faire, $type, $id, $qui, $options){
	return true;
} 


// creer
function autoriser_encart_creer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('configurer', '', 0, $qui, $options); 
}

// voir les fiches completes
function autoriser_encart_voir_dist($faire, $type, $id, $qui, $options) {
	return true;
}

// modifier
function autoriser_encart_modifier_dist($faire, $type, $id, $qui, $options) {
	return autoriser('configurer', '', 0, $qui, $options);
}

// Supprimer un encart : pouvoir modifier l'encart + aucune pub attachée à cet encart
function autoriser_encart_supprimer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('modifier', 'encart', $id, $qui, $options) and !sql_getfetsel('id_campagne', 'spip_campagnes', 'id_encart = '.$id, '', '', '0,1');
}


// -----------------
// Objet campagnes



// Ajouter rapidement une réclame : au moins un encart dans le site et pouvoir en créer
function autoriser_campagnecreer_menu_dist($faire, $type, $id, $qui, $options){
	return sql_countsel('spip_encarts') > 0 and autoriser('creer', 'campagne', '', $qui, $options);
} 

// Créer une publicité : pouvoir configurer le site ou être annonceur
function autoriser_campagne_creer_dist($faire, $type, $id, $qui, $options) {
	if (
		autoriser('configurer', '', 0, $qui, $options)
		or sql_getfetsel('id_annonceur', 'spip_annonceurs', 'id_auteur = '.intval($qui['id_auteur'])) > 0
	)
		return true;
	else
		return false;
}

// Créer une nouvelle publicité dans un encart : pouvoir configurer le site ou être annonceur
function autoriser_encart_creercampagnedans_dist($faire, $quoi, $id, $qui, $options){
	if (autoriser('configurer', '', 0, $qui, $options)
		or sql_getfetsel('id_annonceur', 'spip_annonceurs', 'id_auteur = '.intval($qui['id_auteur'])) > 0
	)
		return true;
	else
		return false;
}

// voir les fiches completes
function autoriser_campagne_voir_dist($faire, $type, $id, $qui, $options) {
	return true;
}

// Modifier une publicité : ceux qui peuvent configurer le site ou l'annonceur de cette publicité
function autoriser_campagne_modifier_dist($faire, $type, $id, $qui, $options) {
	if (autoriser('configurer', '', 0, $qui, $options)
		or $qui['id_auteur'] == sql_getfetsel('a.id_auteur', 'spip_annonceurs as a, spip_campagnes as r', "r.id_campagne = $id and r.id_annonceur = a.id_annonceur")
	)
		return true;
	else
		return false;
}

// Modifier le statut d'une publicité : pouvoir la modifier ET suivant les paramètres et le statut demandé
function autoriser_campagne_instituer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('modifier', 'campagne', $id, $qui, $options)
		and (
			// S'il n'y a pas de précision sur quel statut
			!isset($options['statut'])
			// S'il n'y a aucune restriction de date
			or (
				$editer_campagne_charger = charger_fonction('charger', 'formulaires/editer_campagne')
				and $campagne = $editer_campagne_charger($id)
				and !$campagne['date_debut'] and !$campagne['date_fin']
			)
			// Ou si on demande à publier et qu'on est bien entre les dates de publication
			or(
				$options['statut'] == 'publie'
				and $jourdhui = date('Y-m-d')
				and $jourdhui >= $campagne['date_debut']
				and $jourdhui <= $campagne['date_fin']
			)
			// Ou si on demande à dépublier et qu'on est bien hors des dates de publication
			or(
				in_array($options['statut'], array('prepa', 'obsolete'))
				and ($jourdhui < $campagne['date_debut'] or $jourdhui > $campagne['date_fin'])
			)
		);
}

// Supprimer une publicité : pouvoir la modifier
function autoriser_campagne_supprimer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('modifier', 'campagne', $id, $qui, $options);
}


// -----------------
// Objet annonceurs



// Transformer un auteur en annonceur : pouvoir créer un annonceur
function autoriser_auteur_promouvoirannonceur_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('creer', 'annonceur', 0, $qui, $options);
}

// Créer un nouvel annonceur : pouvoir configurer le site
function autoriser_annonceur_creer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('configurer', '', 0, $qui, $options); 
}

// voir les fiches completes
function autoriser_annonceur_voir_dist($faire, $type, $id, $qui, $options) {
	return true;
}

// Modifier un annonceur : pouvoir configurer le site ou être l'annonceur en question
function autoriser_annonceur_modifier_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer', '', 0, $qui, $options) or $qui['id_auteur'] == sql_getfetsel('id_auteur', 'spip_annonceurs', 'id_annonceur = '.$id);
}

// Supprimer un annonceur : pouvoir configurer le site
function autoriser_annonceur_supprimer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('configurer', '', 0, $qui, $options);
}




?>

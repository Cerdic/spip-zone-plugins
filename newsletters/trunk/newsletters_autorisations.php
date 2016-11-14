<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function newsletters_autoriser(){}


// -----------------
// Objet newsletters


// bouton de menu
function autoriser_newsletters_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_newsletter_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// dater
function autoriser_newsletter_dater_dist($faire, $type, $id, $qui, $opt) {
	if (!isset($opt['statut']))
		$statut = sql_getfetsel("statut", "spip_newsletters", "id_newsletter=".intval($id));
	else
		$statut = $opt['statut'];

	if (in_array($statut,array('publie','prop')))
		return autoriser('modifier', $type, $id);
	return false;
}

// voir les fiches completes
function autoriser_newsletter_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_newsletter_modifier_dist($faire, $type, $id, $qui, $opt) {
	static $baked = array();
	// si les crayons : pas de modif des champs edito si baked
	if (isset($opt['champ'])
		AND $champ=$opt['champ']
	  AND in_array($champ,array('titre','chapo','texte'))){
		if (!isset($baked[$id]))
			$baked[$id] = sql_getfetsel('baked','spip_newsletters','id_newsletter='.intval($id));
		if ($baked[$id])
			return false;
	}
	if (!isset($opt['statut']))
		$statut = sql_getfetsel("statut", "spip_newsletters", "id_newsletter=".intval($id));
	else
		$statut = $opt['statut'];

	if ($statut === 'publie') {
		return ($qui['statut'] === '0minirezo' and !$qui['restreint']);
	}
	else {
		return in_array($qui['statut'], array('0minirezo', '1comite'));
	}
}

// instituer
function autoriser_newsletter_instituer_dist($faire, $type, $id, $qui, $opt) {
	if (isset($opt['statut']) and $opt['statut'] === 'publie'){
		return ($qui['statut'] === '0minirezo' and !$qui['restreint']);
	}
	return autoriser('modifier', $type, $id, $qui, $opt);
}

// envoyer
function autoriser_newsletter_envoyer_dist($faire, $type, $id, $qui, $opt) {
	// en mode test, tous ceux qui peuvent la modifier
	if (isset($opt['test']) and $opt['test']) {
		return autoriser('modifier', $type, $id, $qui, $opt);
	}

	// en vrai, seuls les admins
	return ($qui['statut'] === '0minirezo' and !$qui['restreint']);
}

// supprimer
function autoriser_newsletter_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// associer (lier / delier)
function autoriser_associernewsletters_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


?>
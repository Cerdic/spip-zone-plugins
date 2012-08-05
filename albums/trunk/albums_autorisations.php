<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function albums_autoriser(){}

// creer
function autoriser_album_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_album_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_album_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_album_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// associer (lier / delier)
function autoriser_associeralbums_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// ajouter un album a un objet editorial
function autoriser_ajouteralbum_dist($faire, $type, $id, $qui, $opt){
	return
		(
			$type=='article' OR in_array(table_objet_sql($type),lire_config('albums/objets'))
		)
		AND (
		  (
			  $id>0
		    AND autoriser('modifier', $type, $id, $qui, $opt)
		  )
			OR (
				$id<0
				AND abs($id) == $qui['id_auteur']
				AND autoriser('ecrire', $type, $id, $qui, $opt)
			)
		);
}

/**
 * Auto-association d'albums a du contenu editorial qui le reference
 * par defaut true pour tous les objets
 */
function autoriser_autoassocieralbum_dist($faire, $type, $id, $qui, $opts) {
	return true;
}


?>

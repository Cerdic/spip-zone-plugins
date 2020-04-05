<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

/* Pour que le pipeline de rale pas ! */
function produits_liens_autoriser(){}


/**
 * On ne peut joindre un produit qu'a un objet qu'on a le droit d'editer
 * mais il faut prevoir le cas d'une *creation* par un redacteur, qui correspond
 * au hack id_objet = 0-id_auteur
 * Il faut aussi que les liens de produits aient ete actives sur les objets concernes
 *
 * https://code.spip.net/@autoriser_joindredocument_dist
 *
 * @return bool
 */
function autoriser_lierproduit_dist($faire, $type, $id, $qui, $opt){
	include_spip('inc/config');
	if (lire_config('produits/produits_liens/produits_objets'))
	return
		(
			in_array(table_objet_sql($type),lire_config('produits/produits_liens/produits_objets', ''))
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
 * Autoriser a associer des produits a un objet :
 * il faut avoir le droit de modifier cet objet
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 * @return bool
 */
 /*
function autoriser_associerproduits_dist($faire, $type, $id, $qui, $opt){
	// cas particulier (hack nouvel objet)
	if (intval($id)<0 AND $id==-$qui['id_auteur']){
		return true;
	}
	return autoriser('modifier',$type,$id,$qui,$opt);
}
*/

/**
 * Autoriser a delier des produits a un objet :
 * il faut avoir le droit de modifier cet objet
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 * @return bool
 */
function autoriser_delierproduits_dist($faire, $type, $id, $qui, $opt){
	// cas particulier (hack nouvel objet)
	if (intval($id)<0 AND $id==-$qui['id_auteur']){
		return true;
	}
	return autoriser('modifier',$type,$id,$qui,$opt);
}

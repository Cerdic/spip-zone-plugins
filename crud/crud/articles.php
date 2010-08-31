<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('action/editer_article');

/**
 * Interface C(r)UD
 */
function crud_articles_create_dist($dummy,$set=null){
	if ($id = insert_article($set['id_rubrique']))
		list($e,$ok) = articles_set($id,$set);
	else
		$e = _L('create error');
	return array($id,$ok,$e);
}
function crud_articles_update_dist($id,$set=null){
	list($e,$ok) = articles_set($id,$set);
	return array($id,$ok,$e);
}
function crud_articles_delete_dist($id){
	list($e,$ok) = articles_set($id,array('statut'=>'poubelle'));
	return array($id,$ok,$e);
}

?>
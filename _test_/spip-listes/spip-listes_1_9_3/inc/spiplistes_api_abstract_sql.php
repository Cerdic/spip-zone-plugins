<?php

// inc/spiplistes_api_abstract_sql.php
	
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// CP-20080324 : ajout du script

if (!defined("_ECRIRE_INC_VERSION")) return;

/* Les fonctions abstract_sql compatibles 192 193
*/


function spilistes_sql_select (
	$select = array(), $from = array(), $where = array(),
	$groupby = array(), $orderby = array(), $limit = '', $having = array(),
	$serveur = '', $option = true) {
	
	if($select && $from) {
		if(spiplistes_spip_est_inferieur_193()) {
			include_spip ("inc/utils");
			spip_query(
				_spiplistes_sql_select($select)
				. _spiplistes_sql_from($from)
				. _spiplistes_sql_where($where)
				. _spiplistes_sql_groupby($groupby)
				. _spiplistes_sql_orderby($orderby)
				. _spiplistes_sql_having($having)
				. _spiplistes_sql_limit($limit)
				, $serveur
		} else {
			include_spip ("base/abstract_sql");
			sql_select(
				$select, $from, $where,
				$groupby, $orderby, $limit, $having,
				$serveur, $option
			);
		}
	}
	return(false);
}

// CP-20080324: sous-fonctions locales à ce script
function _spiplistes_sql_select ($select) {
	return($select ? " SELECT ".$select : "");
}
function _spiplistes_sql_from ($from) {
	return($from ? " FROM ".$from : "");
}
function _spiplistes_sql_where ($where) {
	return($where ? " WHERE ".$where : "");
}
function _spiplistes_sql_groupby ($groupby) {
	return($groupby ? " GROUP BY ".$groupby : "");
}
function _spiplistes_sql_orderby ($orderby) {
	return($orderby ? " ORDER BY ".$orderby : "");
}
function _spiplistes_sql_having ($having) {
	return($having ? " HAVING ".$having : "");
}
function _spiplistes_sql_limit ($limit) {
	return($limit ? " LIMIT ".$limit : "");
}


//
?>
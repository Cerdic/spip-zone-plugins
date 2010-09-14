<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * $crud = charger_fonction('crud','action');
 * $crud('create','articles',null,array('id_rubrique'=>23));
 * $crud('update','rubriques',12,array('titre'=>'Super !'));
 * $crud('delete','syndic',5);
 *
 *
 * @param string $action
 *  create, update, delete
 * @param $table
 *  nom court de la table (articles, rubriques, syndic, ...)
 * @param int/string $id
 *  valeur de la cle pour update/delete, inutilise pour la creation
 * @param array $args
 *  liste des valeurs a affecter sous forme champ=>valeur
 *  ou conditions du where pour la lecture
 * @return array
 *  ('success'=>true/false,'message'=>..,'result'=>array())
 */
function action_crud_dist($action=null,$table=null,$id=null,$args = array()){
	// si pas d'action fournie en arg, c'est un appel par url
	// avec un arg signe, qu'on decode
	// et un $args en _request
	if (is_null($action)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$arg = $securiser_action();
		list($action,$table,$id) = explode('-',$arg);
		$args = _request('args');
	}

	if (!in_array($action,array('create','update','delete', 'read')))
		$res = array('message'=>_L("CRUD action $action erronee"));

	elseif (!preg_match(',^\w+$,',$table))
		$res = array('message'=>_L("CRUD table $table erronee"));

	elseif(!include_spip("crud/$table")
		// tolerer un appel avec type plutot que table
		AND (!$table = table_objet($table) OR !include_spip("crud/$table")))
		$res = array('message'=>_L("CRUD table $table inconnue"));

	elseif ($f=charger_fonction("{$table}_{$action}","crud",true))
		$res = $f($id,$args);
	elseif ($f=charger_fonction("{$action}","crud",true))
		$res = $f($table,$id,$args);

	else
		$res = array('message'=>_L("CRUD action $action inconnue pour table $table"));

	// interpretons un peu le retour pour le mettre en forme :
	if (!$res['success'])
		$res['success'] = false;

	if (!$res['result'])
		$res['result'] = array();

	if ($res['success'] AND !$res['message'])
		$res['message'] = _L("ok");

	// TODO : verifier que l'objet a ete supprime physiquement, et dans ce cas
	// trigger le pipeline de suppression des objets lies

	// apres un insert ou un update, on relit la ligne complete
	// pour la fournir en resultat
	if ($res['success'] AND isset($res['result']['id'])) {
		$crud = charger_fonction('crud','action');
		$res['result']['row'] = $crud('read',$table,$res['result']['id']);
		$res['result']['row'] = reset($res['result']['row']);
	}

	return $res;

}

/**
 * R(ead) les lignes d'une table
 * dans le retour, 'result' est un tableau de lignes.
 * Si un $id est fournit en entree, une seule ligne sera dans le tableau
 * et il faut utiliser reset() pour avoir la ligne seule
 *
 * @param string $table
 * @param int $id
 *   primary value
 * @param array $args
 *   arguments for where condition (TBD)
 * @return array
 */
function crud_read_dist($table,$id,$args=array()) {
	if (!preg_match(',^\w+$,',$table))
		return array('message'=>_L("CRUD table $table erronee"));

	$type = objet_type($table);
	$table_sql = table_objet_sql($type);
	$primary = id_table_objet($type);

	// TODO : exploiter $args pour specifier des conditions where supplementaires
	// et des jointures ?
	$where = "$primary=".sql_quote($id);

	$res = sql_allfetsel("*",$table_sql,$where);
	return array('success'=>$res?true:false,'message'=>$res?'':sql_error(),'result'=>$res);
}
?>